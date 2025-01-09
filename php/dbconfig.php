<?php
$db_host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "registration_db";

try {
    // Connect to MySQL database using PDO
    $db = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle database connection error
    die("Database Connection Error: " . $e->getMessage());
}

// Initialize Redis
$redis = null; // Default to null to handle cases where Redis is not available
try {
    if (class_exists('Redis')) { // Check if the Redis class exists
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379); // Ensure Redis is running locally on port 6379
        // Optionally authenticate if Redis is password-protected
        // $redis->auth('your_redis_password');
    } else {
        error_log("Redis class is not available.");
    }
} catch (Exception $e) {
    // Log Redis connection errors
    error_log("Redis Connection Error: " . $e->getMessage());
}
