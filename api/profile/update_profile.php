<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to user
ini_set('log_errors', 1);

header('Content-Type: application/json');
session_start();

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not authenticated"]);
    exit;
}

// Include database config
if (!file_exists(__DIR__ . '/../../config.php')) {
    error_log("Config file not found at: " . __DIR__ . '/../../config.php');
    echo json_encode(["success" => false, "message" => "Configuration error"]);
    exit;
}

include __DIR__ . '/../../config.php';

// Check database connection
if (!isset($conn) || $conn->connect_error) {
    error_log("Database connection failed: " . ($conn->connect_error ?? 'Connection object not set'));
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

try {
    // Get input data
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    
    // Fallback to $_POST if JSON is empty
    if (!$data) {
        $data = $_POST;
    }
    
    // Assign and trim
    $username = isset($data['username']) ? trim($data['username']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    
    // Validate input
    if (empty($username) || empty($email)) {
        echo json_encode(["success" => false, "message" => "Fields cannot be empty"]);
        exit;
    }
    
    // Validate username length
    if (strlen($username) < 3 || strlen($username) > 50) {
        echo json_encode(["success" => false, "message" => "Username must be between 3 and 50 characters"]);
        exit;
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Invalid email format"]);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Check for duplicates (username or email already taken by another user)
    $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(["success" => false, "message" => "Database error"]);
        exit;
    }
    
    $stmt->bind_param("ssi", $username, $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Username or email already taken"]);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
    
    // Update user profile
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(["success" => false, "message" => "Database error"]);
        exit;
    }
    
    $stmt->bind_param("ssi", $username, $email, $user_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['username'] = $username; // Update session
            echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
        } else {
            // No rows affected means no changes were made
            echo json_encode(["success" => true, "message" => "No changes detected"]);
        }
    } else {
        error_log("Execute failed: " . $stmt->error);
        echo json_encode(["success" => false, "message" => "Failed to update profile"]);
    }
    
    $stmt->close();
    $conn->close();
    
}
catch (Exception $e) {
    error_log("Exception in update_profile.php: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "An error occurred. Please try again."]);
}
?>