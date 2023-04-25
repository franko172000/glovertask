<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;
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
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Throwable $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return $this->respondNotFound(sprintf(
                    'Entry for %s not found',
                    str_replace('App\\', '', $exception->getModel())
                ));
            }

            if ($exception instanceof ValidationException) {
                return $this->respondValidationErrors($exception);
            }

            if ($exception instanceof ActionValidationException) {
                return $this->respondActionValidationErrors($exception);
            }

            if ($exception instanceof AccessDeniedHttpException || $exception instanceof UnauthorizedHttpException) {
                return $this->respondForbidden("Forbidden. You don't permission to access this resource");
            }

            if ($exception instanceof QueryException) {
                return $this->respondInternalError('There was Issue with the Query', $exception);
            }

            if ($exception instanceof AccountDeleteException) {
                return $this->respondError(
                    $exception->getMessage()
                );
            }

            if ($exception instanceof RequestActionException) {
                return $this->respondError(
                    $exception->getMessage()
                );
            }
            if ($exception instanceof \Error) {
                return $this->respondInternalError(
                    'There was some internal error',
                    $exception
                );
            }
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return $this->respondUnAuthorized('Unauthenticated or Token Expired, Please Login');
        }

        return redirect()->guest(route('login'));
    }
}
