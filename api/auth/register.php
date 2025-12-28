<?php
header('Content-Type: application/json');
session_start();
include __DIR__ . '/../../config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');

    if (empty($username) || empty($email) || empty($password)) {
        $response['message'] = "All fields are required.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO `USER` (username, email, password) VALUES (?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("sss", $username, $email, $hashedPassword);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Registration successful!";
            } else {
                if ($stmt->errno === 1062 || strpos($stmt->error, "Duplicate") !== false) {
                    $response['message'] = "Username or email already exists.";
                } else {
                    $response['message'] = "Error: " . $stmt->error;
                }
            }
            $stmt->close();
        } else {
            $response['message'] = "Database prepare error: " . $conn->error;
        }
    }
} else {
    $response['message'] = "Invalid request method.";
}

echo json_encode($response);
?>