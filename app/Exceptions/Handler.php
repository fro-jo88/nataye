<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'Unauthenticated.',
                'errors' => []
            ], 401)
            : redirect()->guest(route('login'));
    }

    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Resource not found.',
                    'errors' => []
                ], 404);
            }

            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'status' => 'error',
                    'code' => 422,
                    'message' => 'Validation failed.',
                    'errors' => $exception->errors()
                ], 422);
            }

            if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'This action is unauthorized.',
                    'errors' => []
                ], 403);
            }
        }

        return parent::render($request, $exception);
    }
}
