<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

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
     * protected $redirectTo = RouteServiceProvider::HOME;
     */

    protected $redirectTo = '/manager/stores';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if($user->role=='ROLE_USER' && session()->has('cart')){
            return redirect()->route('checkout.index');
        } else{
            return redirect()->route('home');
        }

        return null;
    }

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        $providerUser = Socialite::driver($provider)->stateless()->user();

        $user = User::firstOrCreate(['email'=> $providerUser->getEmail()],[
            'name'=> $providerUser->getName() ?? $provider->getNickname(),
            'provider_id' => $providerUser->getId(),
            'provider'=> $provider,
        ]);
        Auth::login($user);
        return redirect()->route('home');

        // $user->token;
    }
}
