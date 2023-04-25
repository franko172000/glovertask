<?php

namespace App\Traits;

use Error;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

trait ApiResponseTrait
{
    /*
     *
     * Just a wrapper to facilitate abstract
     */

    /**
     * Return generic json response with the given data.
     *
     * @param array       $data
     * @param int         $statusCode
     * @param string|null $message
     * @param array       $headers
     *
     * @return JsonResponse
     */
    protected function apiResponse(
        array $data = [],
        int $statusCode = 200,
        string $message = null,
        array $headers = []
    ): JsonResponse {
        $response = [];
        $error = Arr::get($data, 'error');

        if ($error) {
            Arr::set($data, 'error', array_merge([
                'message' => $message,
            ], $data['error']));
        }

        if ($message) {
            Arr::set($response, 'message', $message);
        }

        if ($data) {
            $response = array_merge($response, $data);
        }

        return response()->json(
            $response,
            $statusCode,
            $headers
        );
    }

    /**
     * Respond with success.
     *
     * @param string $message
     * @param array  $data
     * @param int    $statusCode
     *
     * @return JsonResponse
     */
    protected function respondSuccess(string $message = 'Ok', int $statusCode = 200, array $data = []): JsonResponse
    {
        $response = [];

        if ($data) {
            $response['data'] = $data;
        }

        return $this->apiResponse($response, $statusCode, $message);
    }

    /**
     * Respond with unauthorized.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    protected function respondUnAuthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->respondError($message, 401);
    }

    /**
     * Respond with error.
     *
     * @param string      $message
     * @param int         $statusCode
     * @param null        $exception
     * @param string|null $errorType
     * @param array       $errorBag
     *
     * @return JsonResponse
     */
    protected function respondError(
        string $message,
        int $statusCode = 400,
        $exception = null,
        string $errorType = null,
        array $errorBag = []
    ): JsonResponse {
        $response = [
            'message' => $message ?? 'There was an internal error, Pls try again later',
            'type' => $errorType,
        ];

        if (($exception instanceof \Throwable)) {
            if (!app()->environment('production')) {
                $response['exception'] = [
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'code' => $exception->getCode(),
                    'trace' => $exception->getTrace(),
                ];
            }
            $statusCode = 500;
        }

        if ($errorBag) {
            $response['errors'] = $errorBag;
        }

        return $this->apiResponse(
            [
                'error' => $response,
            ],
            $statusCode
        );
    }

    /**
     * Respond with forbidden.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    protected function respondForbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->respondError($message, 403);
    }

    /**
     * Respond with not found.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    protected function respondNotFound(string $message = 'Not Found'): JsonResponse
    {
        return $this->respondError($message, 404);
    }

    /**
     * Respond with internal error.
     *
     * @param string $message
     * @param null   $exception
     *
     * @return JsonResponse
     */
    protected function respondInternalError(string $message = 'Internal Error', $exception = null): JsonResponse
    {
        return $this->respondError($message, 500, $exception);
    }

    protected function respondValidationErrors(ValidationException $exception): JsonResponse
    {
        return $this->respondError($exception->getMessage(), 422, null, null, $exception->errors());
    }
}
