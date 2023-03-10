<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

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

Route::get('auth/facebook',[Controller::class,'redirectToFacebook'])->name('auth.facebook');
Route::get('auth/facebook/callback',[Controller::class, 'handleFacebookCallback']);



