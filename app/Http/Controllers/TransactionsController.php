<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Account;
use App\Models\DeliveryAddress;
use App\Models\Transaction;
use App\Models\Package;
use App\Models\Referral;
use App\Models\Rank;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Reward;
use App\Models\SubscribedUser;
use App\Jobs\NotifyRequestJob;
use App\Mail\DepositRequestMail;
use App\Mail\WithdrawalRequestMail;
use App\Mail\PurchaseSuccessMail;
use App\Mail\RepurchaseSuccessMail;

class TransactionsController extends Controller
{
    public function __construct(DeliveryAddress $address){
        $this->middleware(['auth', 'verified']);
        $this->address = $address;
    } 

    // Send deposit request to the admin
    public function deposit(Request $request){
        $inputs = $request->validate([
            'deposit_amount' => 'required',
            'bank_name' => 'required',
            'account_name' => 'required',
        ]);
        
        // return strtoupper($this->uniqueCodeGenerator(12));

        $transaction = new Transaction;
        $transaction->user_id = Auth::user()->id;
        // $transaction->reference_id = strtoupper($this->uniqueCodeGenerator(12));
        $transaction->category = "deposit";
        $transaction->status = "pending";
        $transaction->amount = $request->input('deposit_amount');
        $transaction->bank_name = $request->input("bank_name");
        $transaction->account_name = $request->input("account_name");
        $transaction->save();

        //send notification of deposit request
        dispatch(new NotifyRequestJob("deposit_request"));

        return redirect('/deposit')->with('success', 'Your deposit request has been sent');
    }

    // Send withdrawal request to the admin
    public function withdraw(Request $request){
        $inputs = $request->validate([
            'pin' => 'required|string|max:6',
            'withdrawal_amount' => 'required',
        ]);

        if(!Hash::check($inputs['pin'], Auth::user()->pin)){
            return back()->with('error', 'Oops, your PIN is incorrect. Try again! ');
        }

        $transaction = new Transaction;
        $transaction->user_id = Auth::user()->id;
        $transaction->category = "withdraw";
        $transaction->status = "pending";
        $transaction->amount = $request->input('withdrawal_amount');
        $transaction->fee = $request->input('fee');
        $transaction->total_amount = $request->input('total_amount');
        $transaction->bank_name = $request->input("bank_name");
        $transaction->account_name = $request->input("account_name");
        $transaction->account_number = $request->input("account_number");
        $transaction->save();

        $user = Auth::user();
        $user->available_points = Auth::user()->available_points - $request->input('withdrawal_amount');
        $user->save();

        // Send email notification of withdrawal request
        $notifyMail = new WithdrawalRequestMail();    
        Mail::to(Auth::user()->email)->send($notifyMail);       

        return redirect('/withdrawal')->with('success', 'Your withdrawal request has been sent');

    }

    // Purchase a package
    public function purchasePackage(Request $request){
        //check if purchase limit is exceeded
        $this->validate($request, [
            'package_price' => 'required',
            'quantity' => 'required',
            'total_amount' => 'required',
        ]);
        
        if(!Hash::check($request->input('pin'), Auth::user()->pin)){
            return back()->with('error', 'Oops, your PIN is incorrect. Try again! ');
        } 
        
        if(Auth::user()->available_points < $request->input('total_amount')){
            return back()->with("error", "Insufficient funds! Please credit your Dotori account to purchase more package");
        }
        
        // Get the rank id of the subscribed user
        $referrals = User::where('referrerId', Auth::user()->id)->get()->count();
        $ranks = Rank::select('id', 'referral_limit')->orderBy('id', 'asc')->get();
        foreach($ranks as $rank){
            if($rank->referral_limit > $referrals){
                $rank_id = $rank->id;
                break;
            }
        }        
        
        // check if daily purchase limit of 11 million won is exceeded
        $this->checkDailySubStatus($request->input('total_amount'));        

        //create new instance of subscribed user
        $subscriber = new SubscribedUser;
        $subscriber->user_id = Auth::user()->id;
        $subscriber->package_id = $request->input("package_id");
        $subscriber->rank_id = $rank_id;
        $subscriber->quantity = $request->input('quantity');
        $subscriber->status = "active";
        $subscriber->save();
        
        //update user balance
        $reward_user = Auth::user()->reward;
        if($reward_user === null){
            $reward_user = new Reward;
            $reward_user->user_id = Auth::user()->id;
            $reward_user->spoints = $subscriber->package->reward * $subscriber->quantity;
        }
        else{
            $reward_user->spoints = $reward_user->spoints + ($subscriber->package->reward * $subscriber->quantity);
        }
        
        $reward_user->save();
        
        Auth::user()->available_points = Auth::user()->available_points - $request->input('total_amount');
        Auth::user()->save();

        //send purchase success notification to user email
        $notifyMail = new PurchaseSuccessMail();    
        Mail::to(Auth::user()->email)->send($notifyMail);   

        return redirect('/packages/subscribed')->with('success', 'Package has been subscribed successfully.');
    }

    // get the total amount that has been spent of subscription between two midnights
    private function getDaySubscriptionAmount(){
        $yesterday_midnight = strtotime('today midnight');
        $today_midnight = strtotime('tomorrow midnight');
        $timestamp = time(); //UTC or GMT time

        $subscribers = SubscribedUser::where("user_id", Auth::user()->id)->get();
        $subscribed_amt = 0;
        foreach($subscribers as $subscriber){
            $transaction_time = strtotime($subscriber->created_at);
            if($transaction_time > $yesterday_midnight && $transaction_time < $today_midnight){
                $price = $subscriber->package->staking_amount;
                $qty = $subscriber->quantity;
                $subscribed_amt += $price * $qty;
            }            
        }
        return $subscribed_amt;
    }

    // check if daily subscription limit is reached and return back to previous page
    private function checkDailySubStatus($purchase_amount){
        $subscribed_amt = $this->getDaySubscriptionAmount();
        if($subscribed_amt >= 11000000){
            return back()->with('error', 'You have exceeded your daily limit of purchase');
        }
        $allowed_amount = 11000000 - $subscribed_amt;
        if($purchase_amount > $allowed_amount){
            return back()->with('error', 'You can only subscribe ' . $allowed_amount . 'KRW worth of package for today.');
        }
    }

    // repurchase an existing subscription package on completion of an earning cycle
    public function repurchasePackage(Request $request){
        if(!Hash::check($request->input('pin'), Auth::user()->pin)){
            return back()->with('error', 'Oops, your PIN is incorrect. Try again! ');
        }
        $subscriber = SubscribedUser::where('id', $request->input('package_subscription_id'))->first();
        if($subscriber === null || $subscriber->percent_paid < 200){
            return back()->with('error', 'Your earning cycle is not completed yet.');
        }
        $subscriber->repurchase++;
        $subscriber->percent_paid = 0;
        $subscriber->status = "active";
        $subscriber->save();  

        // Send notification on successful package repurchase
        $notifyMail = new RepurchaseSuccessMail();    
        Mail::to(Auth::user()->email)->send($notifyMail);   

        return back()->with('success', 'Package repurchase is successful.');
    }

    // revoke a subscription and withdraw funds
    public function cancelPackageSub(Request $request){
        return $request;
    }

    public function purchaseProduct(Request $request){
        $this->validate($request, [
            "quantity" => "required",
            "product_price" => "required"
        ]);
        $purchase_amount = $request->input('product_price') * $request->input('quantity'); 
        if(Auth::user()->reward === null || $purchase_amount > Auth::user()->reward->spoints){
            return back()->with('error', 'Oops! Insuffient Balance. Purchase more packages to accumulate SPOINTS.');
        }

        $this->address = DeliveryAddress::where('user_id', Auth::user()->id)->first();
        if($this->address === null){
            $this->validate($request, [
                "street" => "required|string",
                "city" => "required|string",
                "state" => "required|string",
                "country" => "required|string"
            ]);
            $this->address = new DeliveryAddress;
            $this->address->street = $request->input('street');
            $this->address->city = $request->input('city');
            $this->address->state = $request->input('state');
            $this->address->country = $request->input('country');
            $this->address->user_id = Auth::user()->id;
            $this->address->save();
        }
        
        $order = new Order;
        $order->unique_id = $this->orderIDGenerator(16);
        $order->product_id = $request->input('product_id');
        $order->delivery_address_id = $this->address->id;
        $order->price = $request->input('product_price');
        $order->quantity = $request->input('quantity');
        $order->status = "preparing";
        $order->save();

        $user = Auth::user();
        $user->available_points = $user->available_points - $purchase_amount;
        $user->save();

        // Send Email for successful purchase

        return redirect('/products/shop')->with('success', 'You have successfully made a purchase.
            Your delivery is on its way. Your order reference ID is ' . $order->unique_id . '.');
    }
}
