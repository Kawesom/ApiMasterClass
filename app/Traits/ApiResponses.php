<?php
namespace App\Traits;

trait ApiResponses {
    protected function ok($message, $data = []) {
        return $this->success($message, $data, 200);
    }

    /**
     * Returns a successful JsonResponse with message and 200 status code
     *
     * @param String $message
     * @param array $data
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function success($message, $data = [], $statusCode = 200) {
        return response()->json([
            'data' => $data,
            'Message' => $message,
            'status' => $statusCode
        ],$statusCode);
    }

    /**
     * Returns an error JsonResponse with message and status code
     *
     * @param array|string $errors
     * @param int|null $statusCode
     * @return JsonResponse
     */
    protected function error($errors = [], $statusCode = null) {
        if (is_string($errors)) {
            return response()->json([
            'Message' => $errors,
            'status' => $statusCode
        ],$statusCode);
        }
        return response()->json([
            'errors' => $errors
        ]);
    }

    /**
     * Returns an unauthorized 403 JsonResponse
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function notAuthorized($message) {
        return $this->error([
            'status' => 401,
            'message' => $message,
            //'source' => ''
        ]);
    }
}
