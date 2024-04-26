<?php

namespace App\Helpers;

class ResponseHelper
{
    public function withFormat($data = null, string $message = '', string $status = 'success', bool $is_valid = true, bool $error = false)
    {
        return [
            'status' => $status,
            'is_valid' => $is_valid,
            'error' => $error,
            'message' => $message,
            'data' => $data
        ];
    }
}
