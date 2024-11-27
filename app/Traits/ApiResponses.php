<?php
namespace App\Traits;

trait ApiResponses {
    protected function ok($message, $data = []) {
        return $this->success($message, $data, 200);
    }

    protected function success($message, $data = [], $statusCode = 200) {
        return response()->json([
            'data' => $data,
            'Message' => $message,
            'status' => $statusCode
        ],$statusCode);
    }

    protected function error($message, $data, $statusCode) {
        return response()->json([
            'Message' => $message,
            'status' => $statusCode
        ],$statusCode);
    }
}
