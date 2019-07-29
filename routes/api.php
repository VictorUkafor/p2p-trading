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
    Route::middleware('jwt.auth')->group(function () {

        // account routes
        Route::prefix('account')->group(function () {

            Route::prefix('profile')->group(function () {
                
                // view profile
                Route::get('/', 'UserController@profile');   

                // update profile
                Route::put('/', 'UserController@update')
                ->middleware('validateDate');   

            });

        });

    });

});
