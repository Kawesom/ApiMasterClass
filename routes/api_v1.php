<?php

use App\Http\Controllers\API\V1\TicketsController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\V1\AuthorsController;
use App\Http\Controllers\API\V1\AuthorTicketsController;
use App\Http\Controllers\API\V1\UserController;
use App\Models\Tickets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth:sanctum')->group(function() {
    Route::apiResource('/tickets', TicketsController::class)->except(['update']);
    Route::put('/tickets/{ticket}', [TicketsController::class, 'replace']);
    Route::patch('/tickets/{ticket}', [TicketsController::class, 'update']);

    Route::apiResource('users', UserController::class)->except(['update']);
    Route::put('users/{user}', [UserController::class, 'replace']);
    Route::patch('users/{user}', [UserController::class, 'update']);

    //Route::apiResource('/user', UsersController::class);

    Route::apiResource('/authors', AuthorsController::class)->except(['store','update','delete']);
    Route::apiResource('authors.tickets', AuthorTicketsController::class)->except(['update']);
    Route::put('/authors/{author}/tickets/{ticket}', [AuthorTicketsController::class, 'replace']);//put for all
    Route::patch('/authors/{author}/tickets/{ticket}', [AuthorTicketsController::class, 'update']);//patch for 1 or 2
});
