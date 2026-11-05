<?php
require_once __DIR__ . '/env.php';

mysqli_report(MYSQLI_REPORT_OFF);

$conn = @new mysqli(
    env('DB_HOST', 'localhost'),
    env('DB_USERNAME', 'root'),
    env('DB_PASSWORD', ''),
    env('DB_NAME', 'dbcitruscafe_api')
);

if ($conn->connect_error) {
    error_log('Database connection failed: ' . $conn->connect_error);
    http_response_code(503);
    die('The service is temporarily unavailable. Please try again later.');
}

$conn->set_charset('utf8mb4');
