<?php

namespace App\Exceptions;

use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            if ($statusCode == 403) {
                return response()->view('error', [
                    'code' => 403,
                    'heading' => 'Access Denied',
                    'message' => 'You do not have permission to access this resource.',
                ], 403);
            } else if ($statusCode == 404) {
                return response()->view('error', [
                    'code' => 404,
                    'heading' => 'Not Found',
                    'message' => 'The requested resource could not be found.',
                ], 404);
            } else {
                return response()->view('error', [
                    'code' => 500,
                    'heading' => 'Server Error',
                    'message' => 'An error occurred while processing your request.',
                ], 500);
            }
        }

        return parent::render($request, $exception);
    }
}
