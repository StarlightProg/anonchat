<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ApiController extends Controller
{
    /**
     * Success response
     *
     * @param array $result
     * @param bool $prettyPrint
     * @return JsonResponse
     */
    protected function success($result = [], bool $prettyPrint = false, int $status = 200): JsonResponse
    {
        $response = ['success' => true];
        if (!empty($result)) {
            $response['result'] = $result;
        }
        return response()->json($response, $status, [], $prettyPrint ? JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE : 0);
    }

    /**
     * Failure response
     *
     * @param string $message
     * @param int $status
     * @param int $error_code
     * @return JsonResponse
     * @throws ApiException
     */
    protected function failure(string $message, int $status = 500, int $error_code = 0)
    {
        throw new ApiException($message, $status, $error_code);
    }
}