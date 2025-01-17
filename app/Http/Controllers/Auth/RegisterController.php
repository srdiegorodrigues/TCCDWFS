<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\UserRegisteredEmail;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/manager/stores';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */

    protected function create(array $data)
    {

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'ROLE_USER',
            'phone' => $data['phone'],
            'mobile_phone' => $data['mobile_phone'],
            'street' => $data['street'],
            'house_number' => $data['house_number'],
            'neighborhood' => $data['neighborhood'],
            'complement' => $data['complement'],
            'postal_code' => $data['postal_code'],
            'state' => $data['state'],
            'city' => $data['city'],
            'country' => $data['country'],
        ]);
    }
    protected function registered(Request $request, $user)
    {
        Mail::to($user->email)->send(new UserRegisteredEmail($user));

        if($user->role=='ROLE_USER' && session()->has('cart')){
            return redirect()->route('checkout.index');
        }else{
            return redirect()->route('home');
        }

        return null;
    }

}
