<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Rank;
use App\Models\Account;
use App\Models\SubscribedUser;
use App\Models\Transaction;
use App\Models\Referral;
use App\Models\Package;
use App\Models\Product;
use App\Models\Order;
use App\Models\Reward;
use App\Models\Announcement;
use App\Jobs\NotifyRequestJob;



class PagesController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    
    public function index(){
        //create new rank instance
        $ranks = Rank::all();
        if($ranks->count() <= 0){
            $rank = new Rank;
            $rank->title = "Level 1";
            $rank->referral_limit = 5;
            $rank->daily_percent_yield = 1;
            $rank->save();
        }

        // get total number of referrals
        $referrals = User::select('id')->where('referrerId', Auth::user()->memberId)->get()->count();


        // $referrals = User::select('id')->where('referrerId', Auth::user()->referrerId)->get()->count();
        
        // get the rank of the user
        $subscribed_packages = Auth::user()->subscribed_users;
        if($subscribed_packages->count() < 1){
            if($referrals > 0){
                $rank = Rank::where('referral_limit', "<", $referrals)->orderBy('referral_limit', 'ASC')->first();
            }
            else{
                $rank = Rank::where('referral_limit', '5')->first();
            }
        }
        else{
            //update the rank of referee
            $referee = Auth::user();
            $rank = Rank::where('referral_limit', '>', $referrals)->orderBy('id', 'asc')->first();    
            foreach($subscribed_packages as $subscribed_package){
                $subscribed_package->rank_id = $rank->id;
                $subscribed_package->save();
            }
        }      
        return view('pages.dashboard')
            ->with([
                'rank' => $rank,
                'referrals' => $referrals
            ]);
    }

    public function deposit(){
        //send notification of deposit request
        // dispatch(new NotifyRequestJob("deposit_request"));
        return view('pages.deposit');
    }
    public function depositHistory(){
        //get deposit history
        $deposits = Transaction::where(['user_id' => Auth::user()->id, 'category'=>'deposit'])->get();
        return view('pages.depositHistory')->with('deposits', $deposits);
    }

    public function withdrawal(){
        $timestamp = time();
        $time = (float)date("H", $timestamp); //UTC or GMT time
        $day = (string)date('l', $timestamp);
        $withdraw_active = false;
        $message = __('You can only make withdrawal requests on Tuesdays, Thursdays and Saturdays, between 10:00am to 9:00pm (KST).');
        $active_days = ["Tuesday", "Thursday", "Saturday"];
        // $active_days = ["Monday", "Thursday", "Saturday"];
        
        for($i=0; $i<count($active_days); $i++){
            if($day === $active_days[$i] && $time > 10 && $time < 20){
                $withdraw_active = true;
                break;
            }
        }
        return view('pages.withdrawal')->with([
            'withdraw_active' => $withdraw_active,
            'inactive_message' => $message
        ]);
    }
    public function withdrawalHistory(){
        //get withdrawal history
        $withdrawals = Transaction::where(['user_id' => Auth::user()->id, 'category'=>'withdraw'])->get();
        return view('pages.withdrawalHistory')->with('withdrawals', $withdrawals);
    }

    public function announcement(){
        return view('pages.announcement');
    }

    public function subscribedPackages(){
        $subscribed_packages = SubscribedUser::where('user_id', Auth::user()->id)->orderBy('percent_paid', 'ASC')->get();
        $total_staking_amount = 0;
        $completed_payment = false;
        foreach($subscribed_packages as $subscriber){
            $total_staking_amount += $subscriber->package->staking_amount * $subscriber->quantity;
            $completed_payment = $subscriber->percent_paid >= 200 ? true : false;
        }
        return view('pages.packages')
            ->with([
                'subscribed_packages' => $subscribed_packages,
                'total_staking_amount' => $total_staking_amount,
                'completed_payment' => $completed_payment
            ]);
    }
    public function purchasePackage(){
        // check day of the week and validate get requests 
        $timestamp = time();
        $time = (float)date("H", $timestamp); //UTC or GMT time
        $day = date('l', $timestamp);
        $purchase_active = true;
        $message = "";

        // if($day === "Saturday" || $day === "Sunday" || $time < 0 || $time > 18){
        //     $message = "Package purchase is only available from Monday to Friday, from 10:00am to 6:00pm (IST).";
        //     $purchase_active = false;
        // }

        $packages = Package::all();
        return view('pages.packagepurchase')
            ->with([
                'packages' => $packages,
                'purchase_active' => $purchase_active,
                'inactive_message' => $message
            ]);
    }

    public function productShop(){
        $products = Product::all();
        return view('pages.products')
            ->with([
                'products' => $products,
            ]);
    }

    public function purchaseProduct($id){
        $product = Product::findOrFail($id);
        return view('pages.purchaseproduct')->with('product', $product);
    }

    public function orderHistory(){
        $user_address = Auth::user()->delivery_address;
        $orders = $user_address !== null ? Order::where('delivery_address_id', $user_address->id)->get() : [];
        return view('pages.orderhistory')->with([
            'orders' => $orders,
        ]);
    }

    public function profile(){
        $referrals = User::where('referrerId', Auth::user()->memberId)->get()->count();
        return view('pages.profile', compact('referrals'));
    }

    public function changePassword(){
        return view('pages.changepwd');
    }

    public function changePin(){
        return view('pages.changepin');
    }

    public function referral(){
        // get total number of referrals
        $referrals = User::where('referrerId', Auth::user()->memberId)->get();
        return view('pages.referral')->with('referrals', $referrals);
    }

    public function announcements(){
        $announcements = Announcement::all();
        return view('pages.announcements')->with('announcements', $announcements);
    }

    public function searchProduct(Request $request){
        $this->validate($request, [
           "search" => "required"
        ]);
        
        // Get the search value from the request
        $search = $request->input('search');
        
        // Search in the title and body columns from the posts table
        $results = Product::query()
            ->where('name', 'LIKE', "%{$search}%")
            ->orWhere('description', 'LIKE', "%{$search}%")
            ->get();

        return view('pages.searches.results', [
            'results' => $results
        ]);
    }

    public function dailyRewardsHistory($id){
        $subscription = SubscribedUser::findOrFail($id);
        $rewards = Reward::where('subscribed_user_id', $id)->orderBy('created_at', 'DESC')->paginate(10);
        return view('pages.dailyrewards', compact('rewards', 'subscription'));
    }
}
