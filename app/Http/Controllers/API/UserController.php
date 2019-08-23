<?php

namespace App\Http\Controllers\API;

use JWTAuth;
use Illuminate\Support\Facades\Session;
use App\Model\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\EmailConfirmation;
use App\Notifications\AccountActivate;
use Illuminate\Support\Facades\Hash;
use therealsmat\Ebulksms\EbulkSMS;
use PragmaRX\Google2FAQRCode\Google2FA;
use App\Notifications\sendOTP;


class UserController extends Controller {
    
    /**
     * @SWG\Info(title="P2P Trading API", version="1.00")
     */

    /**
     * @SWG\POST(
     *     path="/api/v1/auth/register",
     *     tags={"user"},
     *     summary="Create a user",
     *     description="Create a user and send a link for verification",
     *     @SWG\Parameter(
     *         name="email",
     *         in="query",
     *         description="The email of the user",
     *         required=true,
     *         type="string"
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
     *         description="Invalid email field"
     *     ),
     * )
     */


    public function signup(Request $request) { 

        $adminEmail = config('p2p.admin_email');

        if($request->email === $adminEmail){
            return response()->json([
                'errorMessage' => 'Invalid email',
            ], 400); 
        }

        $user = new User;
        $user->email = $request->email;
        $user->activation_token = str_random(60);
            
        if($user->save()){
            $user->notify(new EmailConfirmation());            
            
            return response()->json([
                'successMessage' => 'A confirmation mail has been sent to your email',
            ], 201);

        }
        
        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    /**
     * @SWG\POST(
     *     path="/api/v1/auth/account-activation/{token}",
     *     summary="Activates user account",
     *     description="Complete user registration after email verification",
     *     tags={"user"},
     *     @SWG\Parameter(
     *         name="first_name",
     *         in="query",
     *         description="The first name of the user",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="last_name",
     *         in="query",
     *         description="The last name of the user",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="date_of_birth",
     *         in="query",
     *         description="The date of birth of the user",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="query",
     *         description="The password of the user",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="password_confirmation",
     *         in="query",
     *         description="The password confirmation  of the user",
     *         required=true,
     *         type="string"
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

    public function findActivationToken($token){
            
        $user = User::where('activation_token', $token)->first();        

        if(!$user){            
            return response()->json([
                'errorMessage' => 'Invalid token',
            ], 400);
        }

        return response()->json([
            'successMessage' => 'User exist',
        ], 200); 

}


    public function signupComplete(Request $request){
            
            $user = $request->user;
            
            $user->active = true;
            $user->activation_token = '';
            $user->first_name = strtolower($request->first_name);
            $user->last_name = strtolower($request->last_name);
            $user->date_of_birth = $request->date_of_birth;
            $user->password = Hash::make($request->password);
            

            if($user->save()){
                $user->notify(new AccountActivate($user));
                
                $credentials = [
                    'email' => $user->email, 
                    'password' => $request->password, 
                    'active' => 1
                ];
                
                $token = JWTAuth::attempt($credentials);
                
                if ($token) {
                    return response()->json([
                        'two_fa' => null,
                        'token' => $token,
                    ], 201);
                }

            }

            return response()->json([
                'errorMessage' => 'Internal server error',
            ], 500); 

    }


    static function twoFactorWithSMS($user, $sms, $token){
        $otp = mt_rand(100000, 666666);
        $user->sms2fa_otp = $otp;

        if($sms->getBalance() > 5){
            $sms->fromSender('P2P TRADING')
            ->composeMessage($otp." is your login code")
            ->addRecipients($user->phone)->send();
        } else {
            $title = 'Two  Factor login';
            $user->notify(new SendOTP($otp, $title));   
        }            

        
        if($user->save()){
            return response()->json([
                'two_fa' => 'sms',
                'token' => $token,
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }


    static function twoFactorWithGoogle($user, $token){
        
        $google2fa = new Google2FA();
        
        $google2fa_qr = $google2fa->getQRCodeInline(
           config('app.name'),
           $user->email,
           $user->google2fa_secret
        );
        
        if($google2fa_qr){
            return response()->json([
                'two_fa' => 'google',
                'token' => $token,
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
     * @SWG\POST(
     *     path="/api/v1/auth/login",
     *     tags={"user"},
     *     summary="login a user",
     *     description="This logins an activation user after entering email and password",
     *     @SWG\Parameter(
     *         name="email",
     *         in="query",
     *         description="The email of the user",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="query",
     *         description="The password of the user",
     *         required=true,
     *         type="string"
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
     *         description="Invalid input fields"
     *     ),
     * )
     */  


    public function login(Request $request, EbulkSMS $sms){

        $user = User::where('email', $request->email)->first();

        $credentials = [
            'email' => $request->email, 
            'password' => $request->password, 
            'active' => 1
        ];
        
        $token = JWTAuth::attempt($credentials);
        
        if (!$user || !$token) {
            return response()->json([
                'errorMessage' => 'Invalid email or password'
            ], 401);
        } 

        if($user->two_fa === 'google'){
            $this->twoFactorWithGoogle($user, $token);
        } 
        
        if($user->two_fa === 'sms'){
            //$this->twoFactorWithSMS($user, $sms, $token);

            $otp = mt_rand(100000, 666666);
            $user->sms2fa_otp = $otp;
    
            if($sms->getBalance() > 5){
                $sms->fromSender('P2P TRADING')
                ->composeMessage($otp." is your login code")
                ->addRecipients($user->phone)->send();
            } else {
                $title = 'Two  Factor login';
                $user->notify(new SendOTP($otp, $title));   
            }            
            
            if($user->save()){
                return response()->json([
                    'two_fa' => 'sms',
                    'token' => $token,
                ], 200);
            }
    
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);

        } 
        
        if(!$user->two_fa) {
            return response()->json([
                'two_fa' => null,
                'token' => $token,
            ], 200);
        }


    
    }
    

    /**
     * @SWG\POST(
     *     path="/api/v1/auth/login-with-sms",
     *     tags={"user"},
     *     summary="login a using sms 2fa",
     *     description="This logs in a user after entering the OTP send to their phone",
     *     @SWG\Parameter(
     *         name="otp",
     *         in="query",
     *         description="The OTP for 2FA",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response=500,
     *         description="Internal server error"
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Invalid input field"
     *     ),
     * )
     */  


    public function loginWithSMS(Request $request){

        $user = User::where('sms2fa_otp', $request->otp)->first();

        if(!$user){
            return response()->json([
                'errorMessage' => 'Invalid OTP',
            ], 400);
        }

        $user->sms2fa_otp = null;

        if($user->save()){
            return response()->json([
                'auth' => true
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500);

    }


    /**
     * @SWG\POST(
     *     path="/api/v1/auth/login-with-google",
     *     tags={"user"},
     *     summary="login a using 2fa with google authenticator",
     *     description="This logs in a user after entering the OTP from google QR code",
     *     @SWG\Parameter(
     *         name="otp",
     *         in="query",
     *         description="The OTP for 2FA",
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

    public function loginWithGoogle(Request $request){
        $auth = session('auth');

        if(!$auth){
            return response()->json([
                'errorMessage' => 'Please login',
            ], 401);
        }

        $google2fa = new Google2FA();
        
        $user = User::where('email', $auth['email'])->first();
                
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->otp);

        if(!$user || !$valid){
            return response()->json([
                'errorMessage' => 'Invalid OTP',
            ], 400);
        }
        
        Session::forget('auth');
        return response()->json([
            'token' => $auth['token'],
        ], 200);

    }


    /**
     * @SWG\GET(
     *     path="/api/v1/profile",
     *     tags={"user"},
     *     summary="display a user profile",
     *     description="Displays the logged in user profile",
     *     @SWG\Response(
     *         response="200",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     * )
     */ 

    public function profile(Request $request){

        $user = $request->user;
        $user->bvn = $user->bvn;
        $user->bankAccounts = $user->bankAccounts;
        $user->notifications = $user->notifications;
        $user->mockAccounts = $user->banks;
        $user->wallet = $user->wallet;
        $user->clients = $user->clients;
        $user->ads = $user->ads;

        foreach($user->ads as $ad){
            $ad->clients = $ad->clients;
            foreach($ad->clients as $client){
                $client->user = $client->user;
                $client->transaction = $client->transaction;
            }
    
        }

        foreach($user->clients as $client){
            $client->ad = $client->ad;
            $client->ad->creator = $client->ad->creator;
            $client->transaction = $client->transaction; 
        }

        
        if(!$user){
            return response()->json([
                'errorMessage' => 'User can not be found'
            ], 404);
        }

        return response()->json([
            'user' => $user
        ], 200);

    }


    /**
     * @SWG\PUT(
     *     path="/api/v1/profile",
     *     tags={"user"},
     *     summary="updates a user profile",
     *     description="Displays the logged in user profile",
     *     @SWG\Parameter(
     *         name="first_name",
     *         in="query",
     *         description="First Name of the user",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="last_name",
     *         in="query",
     *         description="Last Name of the user",
     *         required=false,
     *         type="string"
     *     ),    
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid input field"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     * )
     */     


    public function update(Request $request){

        $user = $request->user;

        $user->first_name = $request->first_name ? 
        $request->first_name : $user->first_name;

        $user->last_name = $request->last_name ? 
        $request->last_name : $user->last_name;

        $user->date_of_birth = $request->date_of_birth ? 
        $request->date_of_birth : $user->date_of_birth;

        if($user->save()){
            return response()->json([
                'successMessage' => 'Profile updated successfully',
                'user' => $user
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500);

    }


    
}
