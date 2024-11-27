<?php

use App\Http\Controllers\API\V1\TicketsController;
use App\Http\Controllers\API\AuthController;
use App\Models\Tickets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->prefix('v1')->group(function() {
    Route::apiResource('/tickets', TicketsController::class);


});

