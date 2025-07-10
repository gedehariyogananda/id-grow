<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Exception $exception, Request $request) {
            if ($request->is('api/*')) {
                if ($exception instanceof AuthenticationException) {
                    return response()->json([
                        'statusCode' => 401,
                        'message' => 'Unauthorized!',
                        'errors' => 'E_UNAUTHORIZED_ACCESS',
                    ], 401);
                }

                if ($exception instanceof ValidationException) {
                    return response()->json([
                        'statusCode' => 422,
                        'message' => 'E_VALIDATION_FAILURE: Validation Exception',
                        'errors' =>  $exception->errors()
                    ], 422);
                }

                if ($exception instanceof MethodNotAllowedHttpException) {
                    return response()->json([
                        'statusCode' => 405,
                        'message' => 'Method Not Allowed',
                        'errors' => 'E_METHOD_NOT_ALLOWED'
                    ], 405);
                }

                if ($exception instanceof HttpException) {
                    return response()->json([
                        'statusCode' => $exception->getStatusCode(),
                        'message' => $exception->getMessage() ?: 'Http Exception',
                        'errors' => 'E_HTTP_EXCEPTION'
                    ], $exception->getStatusCode());
                }   
            }
        });
    })->create();
