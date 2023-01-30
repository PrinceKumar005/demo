<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminController;
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

Route::middleware(['auth:api','user-access:SuperAdmin'])->group(function () {
    Route::Get('Getuser',[SuperAdminController::class,'index']);
});

// Route::Get('Getuser',[SuperAdminController::class,'index']);

//* <-----------------------This Route Create New Admin ------------------------------>
// Route::middleware(['auth:api','user-access:Admin,SuperAdmin'])->group(function () {
    Route::Post('Create',[SuperAdminController::class,'create']);
// });

//* <-----------------------This Route Login User and Provide them Api Key ------------------------------>

Route::middleware(['auth:api','user-access:Admin'])->group(function () {
    Route::Post('login',[SuperAdminController::class,'login']);
});

//* <-----------------------This Route Show Only Selected User ------------------------------>

Route::Post('Getuser{id}',[SuperAdminController::class,'show']);

//* <-----------------------This Route Update The Existing User Name Email Passsword of Selected User ------------------------------>

// Route::Post('update{id}',[SuperAdminController::class,'update']);
Route::middleware('auth:api')->group(function () {
    Route::Put('update',[SuperAdminController::class,'update']);
});

//* <-----------------------This Route Delete the Selected User ------------------------------>

// Route::Get('Delete',[SuperAdminController::class,'destroy']);
Route::middleware('auth:api')->group(function () {
    Route::Delete('Delete{id}',[SuperAdminController::class,'destroy']);
});

//* <-----------------------This Route Logout the Active SuperAdmin  and Delete Their Api's------------------------------>
Route::middleware(['auth:api','user-access:SuperAdmin'])->group(function () {
    Route::Delete('logout',[SuperAdminController::class,'logout']);
});

//* <-----------------------This Route Provide The Information Of Login User------------------------------>

Route::middleware('auth:api')->group(function () {
    Route::get('userinfo',[SuperAdminController::class,'userinfo']);
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
