<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Exception;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {

            $user = Socialite::driver('facebook')->user();

            // dd($user);

            $finduser = User::where('facebook_id', $user->id)->first();

            if($finduser){

                Auth::login($finduser);

                return redirect()->intended('/');

            }else{
                $newUser = User::updateOrCreate(['email' => $user->email],[
                        'name' => $user->name,
                        'facebook_id'=> $user->id,
                        'password' => Hash::make('123456')
                    ]);

                Auth::login($newUser);

                return redirect()->intended('/');
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
