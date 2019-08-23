<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use therealsmat\Ebulksms\EbulkSMS;
use PragmaRX\Google2FAQRCode\Google2FA;
use App\Notifications\sendOTP;

class SettingController extends Controller
{

    /**
     * @SWG\GET(
     *     path="/api/v1/settings/remove-2fa",
     *     tags={"settings"},
     *     summary="Removes 2FA authentication",
     *     description="Removes 2FA authentication after login",
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     * )
     */    

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


    /**
     * @SWG\GET(
     *     path="/api/v1/settings/google-2fa",
     *     tags={"settings"},
     *     summary="Request for 2FA with google",
     *     description="Request for 2FA with google",
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     * )
     */


    public function requestGoogle2fa(Request $request){

        $user = $request->user;

        if($user->google2fa_secret){
            $user->two_fa = 'google';

            if($user->save()){
                return response()->json([
                    'successMessage' => 'Two factor authentication set with Google',
                    'set' => true,
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


    /**
     * @SWG\GET(
     *     path="/api/v1/settings/sms-2fa",
     *     tags={"settings"},
     *     summary="Request for 2FA with sms",
     *     description="Request for 2FA with sms",
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     * )
     */    

    public function request2faSMS(Request $request, EbulkSMS $sms){
        
        $user = $request->user;

        if($user->sms2fa){
            $user->two_fa = 'sms';

            if($user->save()){
                return response()->json([
                    'successMessage' => 'Two factor authentication set with SMS',
                    'set' => true,
                ], 200);  
            }

            return response()->json([
                'errorMessage' => 'Internal server error',
            ], 500); 

        }
        
        $otp = mt_rand(100000, 999999);
        $user->sms2fa_otp = $otp;

        if($sms->getBalance() > 5){
            $sms->fromSender('P2P TRADING')
            ->composeMessage($otp." is your login OTP")
            ->addRecipients($user->phone)->send();
        } else {
            $title = 'Two-Factor Authentication setup';
            $user->notify(new SendOTP($otp, $title));   
        }
                    

        if($user->save()){
            return response()->json([
                'successMessage' => 'Please enter the code sent to your phone',
            ], 200);  
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    /**
     * @SWG\POST(
     *     path="/api/v1/settings/set-sms-2fa",
     *     summary="Set 2FA with sms",
     *     description="Set 2FA with sms",
     *     tags={"settings"},
     *     @SWG\Parameter(
     *         name="otp",
     *         in="query",
     *         description="The otp sent to the user's phone",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid input field"
     *     ),
     * )
     */

    public function setSMS2fa(Request $request){
        
        $user = $request->user;

        if($user->sms2fa_otp != $request->otp){
            return response()->json([
                'errorMessage' => 'Invalid OTP',
            ], 400);             
        }

        $user->two_fa = 'sms';
        $user->sms2fa = true;
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


    /**
     * @SWG\POST(
     *     path="/api/v1/settings/set-google-2fa",
     *     summary="Set 2FA with google authenticator",
     *     description="Set 2FA with google authenticator",
     *     tags={"settings"},
     *     @SWG\Parameter(
     *         name="otp",
     *         in="query",
     *         description="The otp from google authenticator app",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid input field"
     *     ),
     * )
     */


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
