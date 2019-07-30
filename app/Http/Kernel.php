<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

        // middleware to validate email
        'validateEmail' => \App\Http\Middleware\ValidateEmail::class,

        // middleware to check if email exist
        'emailExist' => \App\Http\Middleware\EmailExist::class,

        // middleware to validate signup complete
        'validateSignup' => \App\Http\Middleware\ValidateSignup::class,

        // middleware to validate password reset
        'validatePasswords' => \App\Http\Middleware\ValidatePasswords::class,

        // middleware to validate date
        'validateDate' => \App\Http\Middleware\ValidateDate::class,

        // middleware to validate login
        'validateLogin' => \App\Http\Middleware\ValidateLogin::class,

        // middleware to validate bvn
        'validateBVN' => \App\Http\Middleware\ValidateBVN::class,

        // middleware to validate otp code
        'validateOTP' => \App\Http\Middleware\ValidateOTP::class,

        // for passing the auth user
        'authUser' => \App\Http\Middleware\AuthUser::class,

        // jwt authentication
        'jwt.auth' => Tymon\JWTAuth\Http\Middleware\Authenticate::class,
        'jwt.refresh' => Tymon\JWTAuth\Http\Middleware\RefreshToken::class,

    ];
}
