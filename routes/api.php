<?php

use Illuminate\Http\Request;

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

    });
});
