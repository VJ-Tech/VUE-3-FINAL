<?php

use App\Http\Controllers\EasyController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/easy/random' ,[EasyController::class,'index']);

Route::get('/users',[UserController::class,'index']);
Route::get('/users/{id}',[UserController::class,'getUserById']);
Route::post('/users/add',[UserController::class,'store']);
