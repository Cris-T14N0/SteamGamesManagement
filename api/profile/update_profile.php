<?php
header('Content-Type: application/json');
session_start();

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not authenticated"]);
    exit;
}

if (!file_exists(__DIR__ . '/../../config.php')) {
    error_log("Config file not found at: " . __DIR__ . '/../../config.php');
    echo json_encode(["success" => false, "message" => "Configuration error"]);
    exit;
}

include __DIR__ . '/../../config.php';

if (!isset($conn) || $conn->connect_error) {
    error_log("Database connection failed: " . ($conn->connect_error ?? 'Connection object not set'));
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

try {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    
    if (!$data) {
        $data = $_POST;
    }
    
    $username = isset($data['username']) ? trim($data['username']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    
    if (empty($username) || empty($email)) {
        echo json_encode(["success" => false, "message" => "Fields cannot be empty"]);
        exit;
    }
    
    if (strlen($username) < 3 || strlen($username) > 50) {
        echo json_encode(["success" => false, "message" => "Username must be between 3 and 50 characters"]);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Invalid email format"]);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT id_user FROM `USER` WHERE (username = ? OR email = ?) AND id_user != ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
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
    
    $stmt = $conn->prepare("UPDATE `USER` SET username = ?, email = ? WHERE id_user = ?");
    if (!$stmt) {
        throw new Exception("Prepare update failed: " . $conn->error);
    }
    
    $stmt->bind_param("ssi", $username, $email, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['username'] = $username; 
        echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
    
}
catch (Exception $e) {
    error_log("Exception in update_profile.php: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "An error occurred: " . $e->getMessage()]);
}
?>