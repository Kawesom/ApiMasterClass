<?php

use App\Exceptions\Api\V1\ApiExceptions;
use Dotenv\Exception\ValidationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: [
            __DIR__.'/../routes/api.php',
            __DIR__.'/../routes/api_v1.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            $className = get_class($e); //gets class name of incoming exception
            $handlers = ApiExceptions::$handlers;

            if (array_key_exists($className, $handlers)) {//if error type exists in  our list of errors
                $method = $handlers[$className];
                return ApiExceptions::$method($e, $request);
            }

            return response()->json([
                'error' => [
                    'type' => basename(get_class($e)),
                    'status' => intval($e->getCode()), // returns 0 if no code
                    'message' =>  $e->getMessage(),
                ]
            ]);
        });
    })->create();
