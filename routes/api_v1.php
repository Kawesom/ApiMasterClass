<?php

use App\Http\Controllers\API\V1\TicketsController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\V1\UsersController;
use App\Models\Tickets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth:sanctum')->group(function() {
    Route::apiResource('/tickets', TicketsController::class);
    Route::apiResource('/user', UsersController::class);


});
