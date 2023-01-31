<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Usercontroller;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::Post('Create',([Usercontroller::class,'Create']));

Route::view('/','welcome')->name('login');

Route::get('auth/facebook',[Usercontroller::class,'redirectToFacebook'])->name('auth.facebook');
Route::get('auth/facebook/callback',[Usercontroller::class, 'handleFacebookCallback']);


Route::get("Post","https://graph.facebook.com/v15.0/102993912710831/feed?message=Prince%20Kumar&access_token=EAAHLUsx5i0EBAHJ2W9Gvb7ck3YOtpYxDGGei67OaZA5Ifr61iv4vON43QxIytZBCfSheUpTmaAfixFc0vmiz2U9FmURJKhi3OA9eDXV2ZA99RTBg6ZCKnZBo1xRzNPygcOl2ZA6GHDo5VoGTOBaU1G7nEljv76slkswZAaF6dc35jYx9ZBVmWjq4WZAMnNeMS1zYpDmCQPsoPJkKobrmbpzW5");

