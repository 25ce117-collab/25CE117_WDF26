<?php
/**
 * Practical 13: Standardized JSON API Response Helper
 * Advanced Extension: Standard HTTP Status Codes & Centralized Error Handling
 */

class ApiResponse {
    /**
     * Send standard success response
     */
    public static function success($data = null, string $message = 'Success', int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status'     => 'success',
            'statusCode' => $statusCode,
            'message'    => $message,
            'data'       => $data,
            'timestamp'  => date('Y-m-d H:i:s')
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Send standard error response
     */
    public static function error(string $message, int $statusCode = 400, $details = null): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status'     => 'error',
            'statusCode' => $statusCode,
            'message'    => $message,
            'details'    => $details,
            'timestamp'  => date('Y-m-d H:i:s')
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
