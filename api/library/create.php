<?php
header('Content-Type: application/json');
session_start();
include '../../config.php';

// Apenas para utilizadores logados
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$name = trim($data['name'] ?? '');

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Name is required']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Inserir na BD
$stmt = $conn->prepare("INSERT INTO LIBRARY (user_id, name) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $name);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'List created successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>