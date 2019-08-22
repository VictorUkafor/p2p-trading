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

        // find activation token
        Route::get('/find-token/{token}', 'UserController@findActivationToken'); 

        // sign up activation
        Route::post('/account-activation/{token}', 'UserController@signupComplete')
        ->middleware('validateSignup'); 

        // login
        Route::post('/login', 'UserController@login')
        ->middleware('validateLogin'); 

        // login with sms 2fa
        Route::post('/login-with-sms', 'UserController@loginWithSMS')
        ->middleware('validateOTP'); 

        // login with google 2fa
        Route::post('/login-with-google', 'UserController@loginWithGoogle')
        ->middleware('validateOTP');

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
        Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {

            // view admin profile
            Route::get('/', 'AdminController@profile');   

        });


        // user profile
        Route::prefix('profile')->group(function () {
                
            // view profile
            Route::get('/', 'UserController@profile');   

            // update profile
            Route::put('/', 'UserController@update')->middleware('validateDate');   

        });


        // routes for fake bank accounts
        Route::prefix('banks')->group(function () {

            // create new fake bank account
            Route::post('/', 'BankController@create')
            ->middleware('validateBank');   

            // view all fake bank accounts
            Route::get('/', 'BankController@accounts');  

            // view a fake bank account
            Route::get('/{accountNumber}', 'BankController@account')
            ->middleware('findBank');
            
            // fund a fake bank account
            Route::post('/{accountNumber}', 'BankController@fund')
            ->middleware('fundAccount'); 

        });
        

        // bvn routes
        Route::prefix('bvn')->group(function () {
            
            // route for verifing BVN number
            Route::post('/', 'AccountController@bvn')
            ->middleware('validateBVN');         
            
            // route for updating BVN
            Route::put('/', 'AccountController@bvnUpdate')
            ->middleware('validateBVN');         
            
            // route for sending OTP
            Route::get('/send-otp', 'AccountController@sendOTP');        
            
            // route for verifing OTP
            Route::post('/verify-otp', 'AccountController@OTPVerification')
            ->middleware('validateOTP'); 

        });           


        // only for bvn verified accounts
        Route::middleware('verify')->group(function () {
            

            // routes for bank accounts
            Route::prefix('bank-accounts')->group(function () {            
                
                // route for viewing all bank accounts
                Route::get('/', 'AccountController@accounts'); 
                
                // route for adding bank account
                Route::post('/', 'AccountController@addAccount')
                ->middleware('validateAccount');             
            
                // route for viewing a single bank accounts
                Route::get('/{accountId}', 'AccountController@account')
                ->middleware('findAccount'); 

                // route for updating bank account
                Route::put('/{accountId}', 'AccountController@updateAccount')
                ->middleware('findAccount'); 

            });
            
            
            // routes for notifications
            Route::prefix('notifications')->group(function () {
                
                // route for viewing notifications
                Route::get('/', 'NotificationController@notifications');

                // route for setting push notification
                Route::post('/push', 'NotificationController@push');

                // route for setting email notification
                Route::post('/email', 'NotificationController@email');

                // route for setting auto-logout
                Route::post('/auto-logout', 'NotificationController@autoLogout');

            });


            // routes for settings
            Route::prefix('settings')->group(function () {
                
                Route::get('/remove-2fa', 'SettingController@turnOffTwoFactor');

                Route::get('/sms-2fa', 'SettingController@request2faSMS');

                Route::get('/google-2fa', 'SettingController@requestGoogle2fa');

                Route::post('/set-google-2fa', 'SettingController@setGoogle2fa')
                ->middleware('validateOTP');

                Route::post('/set-sms-2fa', 'SettingController@setSMS2fa')
                ->middleware('validateOTP');

            });
            
            
            // routes for wallet
            Route::prefix('wallet')->group(function () {

                // get wallet 
                Route::get('/', 'WalletController@wallet');

            });


            // routes for ads
            Route::prefix('ads')->group(function () { 
            
                // view all my trade ads
                Route::get('/', 'AdController@myAds');
                
                // view all trade ads
                Route::get('/all/trades', 'AdController@allAds');

                // create buy trade ad
                Route::post('/buy', 'AdController@createBuyAd')
                ->middleware('validateBuy');

                // create sell trade ad
                Route::post('/sell', 'AdController@createSellAd')
                ->middleware('validateSell');


                // routes for single ads
                Route::group([
                'prefix' => '{adId}',
                'middleware' => 'findAd',
                ], function () {
                
                    // view a trade ad
                    Route::get('/', 'AdController@ad');

                    // engage an add
                    Route::post('/engage', 'TransactionController@engageAd')
                    ->middleware('validateEngage');


                    // middleware finding client
                    Route::middleware('findClient')->group(function () {                    
                        
                        // deposit coin for selling
                        Route::post('/deposit-coin/{clientId}', 'SellerClientController@depositCoin');

                        // make payment for buying coins
                        Route::post('/make-payment/{clientId}', 'BuyerClientController@makePayment')
                        ->middleware('validateCard');

                        // refund coin after rejection by the buyer
                        Route::post('/refund-coin/{clientId}', 'SellerClientController@refundCoin');

                        // refund payment after rejection by the seller
                        Route::post('/refund-payment/{clientId}', 'BuyerClientController@refundPayment')
                        ->middleware('validateCard');

                    });

                });
                
                
                // routes for my ads
                Route::group([
                    'prefix' => '{adId}',
                    'middleware' => 'myAd',
                ], function () {
                    
                    // update a trade ad
                    Route::put('/', 'AdController@updateAd')
                    ->middleware('updateBuy');
                
                    // remove a trade ad
                    Route::post('/remove', 'AdController@removeAd')
                    ->middleware('validateCard');


                    // client exist
                    Route::middleware('clientExist')->group(function () {
                        
                        // approve an add
                        Route::post('/approve/{clientId}', 'TransactionController@approveTrade');

                        // decline an add
                        Route::post('/decline/{clientId}', 'TransactionController@declineTrade')
                        ->middleware('validateCard');  

                        // confirm deposit of coin by the buyer
                        Route::post('/confirm-coin/{clientId}', 'SellerClientController@confirmDeposit')
                        ->middleware('validateCard'); 

                        // confirm confirm payment by the seller
                        Route::post('/confirm-payment/{clientId}', 'BuyerClientController@confirmPayment');

                        // reject coin deposit by the buyer
                        Route::post('/reject-coin/{clientId}', 'SellerClientController@declineCoin')
                        ->middleware('validateCard');

                        // reject payment by the seller
                        Route::post('/reject-payment/{clientId}', 'BuyerClientController@declinePayment'); 

                        // refund balance to buyer
                        Route::post('/refund-balance/{clientId}', 'SellerClientController@refundBalance')
                        ->middleware('validateCard'); 

                    });   
                

                
                });
            
            });
            
            
            // routes for fees
            Route::prefix('fees')->group(function () {

                // view all fees
                Route::get('/all', 'FeeController@allFees')
                ->middleware('admin');

                // view all user fees
                Route::get('/', 'FeeController@fees');

                // view a fee
                Route::get('/{feeId}', 'FeeController@fee')
                ->middleware('findFee');

            });


            
            // routes for wallet addresses
            Route::prefix('addresses')->group(function () {
            
                // generate address
                Route::post('/', 'TransferController@generateAddress')
                ->middleware('validateCoin');

                // view addresses
                Route::get('/', 'TransferController@addresses');                
                
                // view address
                Route::get('/{address}', 'TransferController@address');
                
                // fund wallet with address
                Route::post('/fund-with-address', 'TransferController@fundWithAddress')
                ->middleware('withAddress');
            
                // fund wallet with username
                Route::post('/fund-with-username', 'TransferController@fundWithUsername')
                ->middleware('withUsername');
            
            });
            
            
            // route for mailing p2p trading
            Route::post('/mail-us', 'MailController@create')
            ->middleware('validateMail');
        
        });
    
    
    });


});
