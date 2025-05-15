<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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

        $this->reportable(function(ApiException $e){
           if (
               $e->getErrorCode() == ApiException::VALIDATION_ERROR ||
               $e->getErrorCode() == ApiException::UNAUTHORIZED
           ) {
               return false;
           }
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->is('api/*', 'admin/clients/bulk-push/send')) {
            $error_code = $code = ApiException::INTERNAL_ERROR;
            $reason = $e->getMessage();
            $message = ApiException::getErrorMessage($error_code);
            if ($e instanceof ApiException) {
                $code = $e->getCode();
                $error_code = $e->getErrorCode();
                $reason = $e->getReason();
                $message = ApiException::getErrorMessage($error_code);
            }
            if ($e instanceof ModelNotFoundException) {
                $code = $error_code = 404;
                $reason = 'Resource not found';
                $message = ApiException::getErrorMessage($code);
            }
            if ($e instanceof ThrottleRequestsException) {
                $code = $error_code = $e->getStatusCode();
                $reason = 'Request was blocked';
                $message = ApiException::getErrorMessage($code);
            }
            if ($e instanceof NotFoundHttpException) {
                $error_code = $code = $e->getStatusCode();
                $reason = 'Endpoint not found';
                $message = ApiException::getErrorMessage($code);
            }
            if ($e instanceof MethodNotAllowedHttpException) {
                $error_code = $code = $e->getStatusCode();
                $reason = 'Method not allowed';
                $message = ApiException::getErrorMessage($code);
            }
            $context = [
                'success' => false,
                'error' => [
                    'code' => $error_code,
                    'message' => $message,
                    'reason' => $reason,
                ],
            ];
            return response()->json($context, $code);
        }
        return parent::render($request, $e);
    }
}
