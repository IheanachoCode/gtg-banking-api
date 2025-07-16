<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                $response = [
                    'status' => false,
                    'message' => 'An error occurred',
                    'data' => ['response' => false],
                    'timestamp' => now()->format('Y-m-d H:i:s'),
                    'response_time' => defined('LARAVEL_START') ? round((microtime(true) - LARAVEL_START) * 1000, 2) : 0
                ];
                $statusCode = 500;

                if ($e instanceof ValidationException) {
                    $response['message'] = 'Validation failed';
                    $response['data'] = ['errors' => $e->errors(), 'response' => false];
                    $statusCode = 422;
                } elseif ($e instanceof AuthenticationException) {
                    $response['message'] = 'Unauthenticated';
                    $statusCode = 401;
                } elseif ($e instanceof ModelNotFoundException) {
                    $response['message'] = 'Resource not found';
                    $statusCode = 404;
                } elseif ($e instanceof NotFoundHttpException) {
                    $response['message'] = 'Route not found';
                    $statusCode = 404;
                } elseif (config('app.debug')) {
                    $response['message'] = $e->getMessage();
                }

                // Log the error
                Log::error('API Error: ' . $e->getMessage(), [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json($response, $statusCode);
            }
        });
    }
} 