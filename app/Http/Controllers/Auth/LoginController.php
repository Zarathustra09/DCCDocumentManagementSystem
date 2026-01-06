<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function username()
    {
        return 'username';
    }

    protected function attemptLogin(\Illuminate\Http\Request $request)
    {
        $user = \App\Models\User::where('username', $request->input('username'))->first();

        if ($user && password_verify($request->input('password'), $user->password)) {
            auth()->login($user, $request->filled('remember'));
            return true;
        }
       $logContext = [
           'username'   => $request->input('username'),
           'ip'         => $request->ip(),
           'user_agent' => $request->header('User-Agent'),
           'time'       => now()->toDateTimeString(),
           'user_exists'=> (bool) $user,
       ];

       if (! $user) {
           Log::warning('Login attempt failed: user not found.', $logContext);
       } else {
           $logContext['reason'] = 'invalid_password';
           Log::warning('Login attempt failed: invalid credentials.', $logContext);
       }

       session()->flash('error', 'Invalid username or password.');

        return false;
    }
}
