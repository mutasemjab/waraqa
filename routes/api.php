<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\Driver\OrderDriverController;
use App\Http\Controllers\Api\v1\Driver\AuthDriverController;
use App\Http\Controllers\Api\v1\Driver\HomeDriverController;
use App\Http\Controllers\Api\v1\Driver\WalletDriverController;
use App\Http\Controllers\Api\v1\Driver\WithdrawalRequestDriverController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\User\AuthController;
use App\Http\Controllers\Api\v1\User\UploadPhotoVoiceController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

