<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

//Route::get('/user', function () {
//    $users=\App\Models\User::all();
//    return response()->json($users);
//});
Route::get('/check-auth', function () {
   if (Auth::check()){
       return response()->json([
           'auth'=>true,
           'user'=>\Illuminate\Support\Facades\Auth::user()
       ]);
   }else{
       return response()->json([
           'auth'=>false,
           'user'=>null
       ]);
   }
})->middleware('auth:sanctum');
Route::post('/register',[\App\Http\Controllers\AuthController::class,'register'])->name('register');
Route::post('/login',[\App\Http\Controllers\AuthController::class,'login'])->name('login');
Route::post('/logout',[\App\Http\Controllers\AuthController::class,'logout'])->name('logout')->middleware('auth:sanctum');


//
Route::get('/user', [\App\Http\Controllers\PostController::class, 'index'])->middleware('auth:sanctum');
