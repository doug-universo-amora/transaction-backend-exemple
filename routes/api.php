<?php

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

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
Route::apiResource('user', UserController::class);
Route::apiResource('transaction', TransactionController::class);


Route::middleware('auth:sanctum')->get('/users', function (Request $request) {
    return $request->user();
});
