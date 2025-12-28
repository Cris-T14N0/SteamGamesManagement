<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not authenticated"]);
    exit;
}

include __DIR__ . '/../../config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['password'])) {
    echo json_encode(["success" => false, "message" => "Password required for confirmation"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$password = $data['password'];

$stmt = $conn->prepare("SELECT password FROM `USER` WHERE id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(["success" => false, "message" => "User not found"]);
    exit;
}

// Verify password
if (!password_verify($password, $user['password'])) {
    echo json_encode(["success" => false, "message" => "Incorrect password"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM `USER` WHERE id_user = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    session_destroy();
    echo json_encode(["success" => true, "message" => "Account deleted successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to delete account"]);
}

$stmt->close();
$conn->close();
?>