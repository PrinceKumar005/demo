<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
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

Route::group(['middleware'=>['auth:api'],['user-access:Admin,SuperAdmin']],function () {
    Route::Get('Getuser',[AdminController::class,'index']);
});

// Route::Get('Getuser',[AdminController::class,'index']);

//* <-----------------------This Route Create New Admin ------------------------------>
Route::group(['middleware'=>['auth:api'],['user-access:Admin,SuperAdmin']],function () {
    Route::Post('Create',[AdminController::class,'create']);
});

//* <-----------------------This Route Login User and Provide them Api Key ------------------------------>

Route::middleware(['auth:api','user-access:Admin'])->group(function () {
    Route::Post('login',[AdminController::class,'login']);
});

//* <-----------------------This Route Show Only Selected User ------------------------------>

Route::Post('Getuser{id}',[AdminController::class,'show']);

//* <-----------------------This Route Update The Existing User Name Email Passsword of Selected User ------------------------------>

Route::group(['middleware'=>['auth:api'],['user-access:Admin,SuperAdmin']],function () {
    Route::Put('updateuser{id}',[AdminController::class,'updateuser']);
});

//* <-----------------------This Route Delete the Current Admin ------------------------------>

// Route::Get('Delete',[AdminController::class,'destroy']);
Route::middleware('auth:api')->group(function () {
    Route::Delete('Delete',[AdminController::class,'destroy']);
});

//* <-----------------------This Route Delete the Selected User ------------------------------>

Route::middleware('auth:api')->group(function () {
    Route::Delete('Deleteuser{id}',[AdminController::class,'destroyuser']);
});

//* <-----------------------This Route Logout the Active User  and Delete Their Api's------------------------------>

Route::middleware(['auth:api','user-access:Admin'])->group(function () {
    Route::Delete('logout',[AdminController::class,'logout']);
});

//* <-----------------------This Route Provide The Information Of Login User------------------------------>

Route::middleware('auth:api')->group(function () {
    Route::get('userinfo',[AdminController::class,'userinfo']);
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
