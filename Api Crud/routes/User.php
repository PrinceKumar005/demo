<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Usercontroller;
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

//* <-----------------------This Route get all the User Information That create account ------------------------------>

Route::middleware(['auth:api','user-access:User'])->group(function () {
    Route::Get('Getuser',[Usercontroller::class,'index']);
});

// Route::Get('Getuser',[Usercontroller::class,'index']);

//* <-----------------------This Route Create New User ------------------------------>

Route::Post('Create',[Usercontroller::class,'create']);

//* <-----------------------This Route Login User and Provide them Api Key ------------------------------>

Route::Post('login',[Usercontroller::class,'login']);

//* <-----------------------This Route Show Only Selected User ------------------------------>
Route::middleware(['auth:api','user-access:User'])->group(function () {
    Route::Post('Getuser',[Usercontroller::class,'show']);
});

//* <-----------------------This Route Update The Existing User Name Email Passsword of Selected User ------------------------------>

// Route::Post('update{id}',[Usercontroller::class,'update']);
Route::middleware('auth:api')->group(function () {
    Route::Put('update',[Usercontroller::class,'update']);
});

//* <-----------------------This Route Delete the Selected User ------------------------------>

// Route::Get('Delete',[Usercontroller::class,'destroy']);
Route::middleware('auth:api')->group(function () {
    Route::Delete('Delete',[Usercontroller::class,'destroy']);
});

//* <-----------------------This Route Logout the Active User  and Delete Their Api's------------------------------>

Route::middleware(['auth:api','user-access:User'])->group(function () {
    Route::Delete('logout',[Usercontroller::class,'logout']);
});



//* <-----------------------This Route Provide The Information Of Login User------------------------------>

Route::middleware('auth:api')->group(function () {
    Route::get('userinfo',[Usercontroller::class,'userinfo']);
});


//* <-----------------------This Route Is For Error Handling of Wrong Url ----------------------------->
Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. Check Your URL and Try again'], 404);
});


//* <-----------------------This Route Is For Error Handling of Wrong Api Key ----------------------------->
Route::get('error', function () {
    return response()->json([
        'message' => 'Please Check Your Api Key and Try again'
    ],401);
})->name('login');
// Route::middleware('auth:api')->group(function () {
//     Route::get('userinfo',[Icontroller::class,'userinfo']);
// });
