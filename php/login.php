<?php
ini_set("display_errors", 1);
require_once "dbconfig.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["username"], $_POST["password"])) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        echo 'Invalid input: Username or password cannot be empty.';
        exit;
    }

    try {
        // Prepare the SQL query
        $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM tbl_user WHERE username = :uname AND password = :upassword");
        $stmt->bindParam(":uname", $username, PDO::PARAM_STR);
        $stmt->bindParam(":upassword", $password, PDO::PARAM_STR);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['cnt'] > 0) {
            // Check if Redis is initialized
            if ($redis) {
                try {
                    $redis->set("logged_in_user", $username);
                } catch (Exception $redisException) {
                    error_log("Redis set failed: " . $redisException->getMessage());
                }
            } else {
                error_log("Redis instance is null. Skipping Redis set.");
            }
            echo 'Success';
        } else {
            echo 'Failure';
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo 'Error occurred while processing the request.';
    }
} else {
    echo 'Invalid input';
}
?>
