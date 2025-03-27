<?php

use App\Http\Controllers\BalInfoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccountController;

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


Route::post('users/register', [UserController::class, 'register']);
Route::post('users/login', [UserController::class, 'login']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('users/logout', [UserController::class, 'logout']);
    Route::post('informasi-saldo', [AccountController::class, 'getAccountInfo'])->middleware(['snap-bi']);
});

Route::get('/balinfo', [BalInfoController::class, 'index']);
