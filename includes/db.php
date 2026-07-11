<?php
// ============================================================
//  Grand Azure Hotel — Database Connection (MySQLi)
// ============================================================

require_once __DIR__ . '/config.php';

// Create a MySQLi connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for connection errors
if ($conn->connect_error) {
    // In production, log the error instead of displaying it
    error_log('Database Connection Failed: ' . $conn->connect_error);
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed. Please try again later.'
    ]));
}

// Set charset to utf8mb4 for full Unicode support (including emoji)
$conn->set_charset('utf8mb4');
