<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Auth;
use Socialite;
use App\User;
use DB;
use Str;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return 301 
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('google')->user();
        $apiToken = Str::random(10);
        
        $user_data = DB::select('select * from users where email = ?',[$user->email]);
        
        if(empty($user_data)){

            $user_l_data = User::create([
                                'name' => $user->name,
                                'email' => $user->email,
                                'password' => Hash::make($apiToken),
                            ]);

        }else{
            $user_l_data = User::where('email', $user->email)->first();
        }
        Auth::login($user_l_data, true);
        return redirect()->route('home');
    }
}