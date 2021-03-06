<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Account;
use App\Models\DeliveryAddress;
use App\Mail\PINChangeMail;
use App\Mail\PasswordChangeMail;


class SettingsController extends Controller
{
    public function __construct(){
        $this->middleware(['auth', 'verified']);
    }

    public function updateProfile(Request $request){
        $this->validate($request, [
            'name' => ['required', 'string'],
        ]);
        
        $user = Auth::user();
        $user->name = $request->input('name');
        // $user->phone = $request->input('phone');
        $user->save();

        //update or create new bank account details
        $account = Account::where('user_id', Auth::user()->id)->first();
        if($account === null){
            $this->validate($request, [
                "bank_name" => "required",
                "account_number" => "required",
                "account_name" => "required",
            ]);
            $account = new Account;
        }
        $account->bank_name = $request->input('bank_name');
        $account->account_name = $request->input('account_name');
        $account->account_number = $request->input('account_number');
        $account->user_id = Auth::user()->id;
        $account->save();

        //update or create new delivery address for user
        $address = DeliveryAddress::where('user_id', Auth::user()->id)->first();
        if($address === null){
            $this->validate($request, [
                "address" => "required",
                "address_detail" => "required",
                "zip_code" => "required",
            ]);
            $address = new DeliveryAddress;
            $address->user_id = Auth::user()->id;
        }
        $address->address = $request->input('address');
        $address->address_detail = $request->input('address_detail');
        $address->zip_code = $request->input('zip_code');
        $address->save();
        return redirect('/settings/profile')->with('success', __('Your profile has been updated successfully'));
        
    }

    
    public function changePassword(Request $request){
        $this->validate($request, [
            'password' => 'required|string|min:8|confirmed'
        ]);
        
        // update user password
        $user = Auth::user();
        $user->password = Hash::make($request->input('password'));
        $user->save();

        //send a notification for password change
        $notifyMail = new PasswordChangeMail();    
        Mail::to(Auth::user()->email)->send($notifyMail);       

        return redirect('/settings/password')->with('success', __('Your password has been updated successfully'));    
    }

    public function changePin(Request $request){
        $this->validate($request, [
            'pin' => 'required|string|max:6|confirmed'
        ]);

        // update user pin
        $user = Auth::user();
        $user->pin = Hash::make($request->input('pin'));
        $user->save();
        
        //send a notification for password change
        $notifyMail = new PINChangeMail();    
        Mail::to(Auth::user()->email)->send($notifyMail);       

        return redirect('/settings/pin')->with('success', __('Your pin has been updated successfully'));    
    }
    
    public function deleteUserProfile(Request $request){
        if(!Hash::check($request->input('pin'), Auth::user()->pin) || !Hash::check($request->input('password'), Auth::user()->password)){
            return back()->with('error', 'Please enter correct PIN and password');
        }
        $referrals = User::where('referrerId', Auth::user()->memberId)->get()->count();
        if($referrals > 1){
            return back()->with('error', __('You already have referrals'));
        }
        Auth::user()->delete();
        return redirect('/');
    }

    public function saveProfileImage(Request $request){
        if($request->input('base64image') !== null){
            $user = Auth::user();
            $folderPath = public_path('/storage/images/profile_images/');
            $old_path = $folderPath . $user->profile_image;

            if($user->profile_image !== "no_image.png" && file_exists($old_path)){
                unlink($old_path);
            }
            // Extract encoded image 
            $image_parts = explode(';base64,', $request->input('base64image'));
            $image_types_aux = explode('image/', $image_parts[0]);
            $image_type = $image_types_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $filename =  strtolower($user->user_unique_id) . '-' . time() . "." . $image_type;
            $file = $folderPath . $filename;
            $path = str_replace('\\', '/',  $file);
                
            //put image in the storage location 
            file_put_contents($path, $image_base64);
            
            //save filename in database
            $user->profile_image = $filename;
            $user->save();

            return redirect('/settings/profile')->with('success', __('Your profile Image has been updated successfully.'));
        }
    }

}
