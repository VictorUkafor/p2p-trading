<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use therealsmat\Ebulksms\EbulkSMS;
use PragmaRX\Google2FAQRCode\Google2FA;

class SettingController extends Controller
{

    public function turnOffTwoFactor(Request $request){
        
        $user = $request->user;

        $user->two_fa = null;

        if($user->save()){
            return response()->json([
                'successMessage' => 'Two factor authentication removed',
            ], 200);  
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }

    public function requestGoogle2fa(Request $request){

        $user = $request->user;

        if($user->google2fa_secret){
            $user->two_fa = 'google';

            if($user->save()){
                return response()->json([
                    'successMessage' => 'Two factor authentication set with Google',
                ], 200);  
            }

            return response()->json([
                'errorMessage' => 'Internal server error',
            ], 500); 

        }
        
        $google2fa = new Google2FA();

        $google2fa_secret = $google2fa->generateSecretKey();

        session(['google2fa_secret' => $google2fa_secret]);
        
        $google2fa_qr = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $google2fa_secret
        );

        if($google2fa_qr){
            return response()->json([
                'qrCode' => $google2fa_qr,
                'successMessage' => 'Scan the QrCode with Google Authenticator to grab the OTP', 
                'instruction' => 'copy the qrcode and paste it in a browser'
            ], 200);  
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    public function request2faSMS(Request $request, EbulkSMS $sms){
        
        $user = $request->user;

        if($user->two_fa !== 'unset'){
            $user->two_fa = 'sms';

            if($user->save()){
                return response()->json([
                    'successMessage' => 'Two factor authentication set with SMS',
                ], 200);  
            }

            return response()->json([
                'errorMessage' => 'Internal server error',
            ], 500); 

        }
        
        $otp = mt_rand(100000, 999999);
        $user->sms2fa_otp = $otp;
            
        $sms->fromSender('P2P TRADING')
        ->composeMessage($otp." is your login OTP")
        ->addRecipients($user->phone)->send();
        

        if($user->save()){
            return response()->json([
                'successMessage' => 'Please enter the code sent to your phone',
                'otp' => $otp
            ], 200);  
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    public function setSMS2fa(Request $request){
        
        $user = $request->user;

        if($user->sms2fa_otp != $request->otp){
            return response()->json([
                'errorMessage' => 'Invalid OTP',
            ], 400);             
        }

        $user->two_fa = 'sms';
        $user->sms2fa_otp = null;

        if($user->save()){
            return response()->json([
                'successMessage' => 'Two factor authentication set with SMS',
            ], 200);  
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    public function setGoogle2fa(Request $request){
        
        $user = $request->user;

        $google2fa = new Google2FA();
        
        $google2fa_secret = session('google2fa_secret');
        
        $valid = $google2fa->verifyKey($google2fa_secret, $request->otp);

        if(!$valid){
            return response()->json([
                'errorMessage' => 'Invalid OTP',
            ], 200);  
        }

        $user->two_fa = 'google';
        $user->google2fa_secret = $google2fa_secret;

        if($user->save()){
            Session::forget('google2fa_secret');
            return response()->json([
                'successMessage' => 'Two factor authentication set with Google',
            ], 200);  
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }    


}
