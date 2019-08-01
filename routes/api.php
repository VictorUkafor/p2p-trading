<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1', 'namespace' => 'API'], function () {

    // routes that don't require authentication
    Route::prefix('auth')->group(function () {

        // admin signup
        Route::post('/admin', 'AdminController@signup'); 
        
        // sign up account
        Route::post('/register', 'UserController@signup')
        ->middleware('validateEmail'); 

        // sign up activation
        Route::post('/account-activation/{token}', 'UserController@signupComplete')
        ->middleware('validateSignup'); 

        // login
        Route::post('/login', 'UserController@login')
        ->middleware('validateLogin'); 

        // Password resets routes
        Route::prefix('password-reset')->group(function () {
            
            // request password reset route
            Route::post('/request', 'PasswordResetController@create')
           ->middleware('emailExist');
        
            // get token for reset
            Route::get('/find/{token}', 'PasswordResetController@find');    
        
            // reset password route
            Route::post('/reset/{token}', 'PasswordResetController@reset')
            ->middleware('validatePasswords');

        });

    });


    // routes requring authentication
    Route::middleware(['jwt.auth', 'authUser'])->group(function () {


        // admin routes
        Route::group([
            'prefix' => 'admin', 
            'middleware' => 'admin'
        ], function () {

            // view admin profile
            Route::get('/profile', 'AdminController@profile');   

        });


        // account routes
        Route::prefix('account')->group(function () {

            Route::prefix('profile')->group(function () {
                
                // view profile
                Route::get('/', 'UserController@profile');   

                // update profile
                Route::put('/', 'UserController@update')
                ->middleware('validateDate');   

            });

            
            // route for verifing BVN number
            Route::post('/bvn', 'AccountController@bvn')
            ->middleware('validateBVN'); 
            
            // route for updating BVN
            Route::put('/bvn-update', 'AccountController@bvnUpdate')
            ->middleware('validateBVN'); 
            
            // route for sending OTP
            Route::get('/send-otp', 'AccountController@sendOTP'); 

            // route for verifing OTP
            Route::post('/verify-otp', 'AccountController@OTPVerification')
            ->middleware('validateOTP'); 

            
            // routes for bank accounts
            Route::prefix('bank-accounts')->group(function () {
                
                // route for adding bank account
                Route::post('/', 'AccountController@addAccount')
                ->middleware('validateAccount'); 

                // route for updating bank account
                Route::put('/{accountId}', 'AccountController@updateAccount')
                ->middleware(['findAccount', 'validateAccount']); 

                // route for viewing all bank accounts
                Route::get('/', 'AccountController@accounts'); 

                // route for viewing a single bank accounts
                Route::get('/{accountId}', 'AccountController@account')
                ->middleware('findAccount');  

            });

    
            // routes for notifications
            Route::prefix('notifications')->group(function () {
                
                // route for viewing notifications
                Route::get('/', 'NotificationController@notifications');

                // route for setting push notification
                Route::post('/push', 'NotificationController@pushNotification');

                // route for setting email notification
                Route::post('/email', 'NotificationController@emailNotification');

                // route for setting auto-logout
                Route::post('/auto-logout', 'NotificationController@autoLogout');

            });

        });


        // routes for wallet
        Route::prefix('wallet')->group(function () {

            // get wallet 
            Route::get('/', 'WalletController@wallet');

           // routes for buy crypto
           Route::prefix('buy-cryptos')->group(function () {

                // buy crypto
                Route::post('/', 'BuyCryptoController@buyCrypto')
                ->middleware('validateBuy');
            
                // view all buying transaction
                Route::get('/', 'BuyCryptoController@buys');

                // view a single buying transaction
                Route::get('/{buyId}', 'BuyCryptoController@buy')
                ->middleware('findBuy');

                // routes for admin
                Route::middleware('admin')->group(function () {                
                    
                    // complete a buying transaction
                    Route::get('/all/buys', 'BuyCryptoController@allBuys');

                    // cancel a buying transaction
                    Route::post('/{buyId}/cancel', 'BuyCryptoController@cancel')
                    ->middleware('findBuy');

                    // complete a buying transaction
                    Route::post('/{buyId}/complete', 'BuyCryptoController@complete')
                    ->middleware('findBuy');

                });



            

           });

        });


        // route for mailing p2p trading
        Route::post('/mail-us', 'MailController@create')
        ->middleware('validateMail'); 
        

    });

});
