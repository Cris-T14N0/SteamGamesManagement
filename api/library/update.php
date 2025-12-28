<?php
header('Content-Type: application/json');
session_start();
include '../../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;
$name = trim($data['name'] ?? '');

if (empty($id) || empty($name)) {
    echo json_encode(['success' => false, 'message' => 'ID and Name are required']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Atualizar garantindo que a lista pertence ao user
$stmt = $conn->prepare("UPDATE LIBRARY SET name = ? WHERE id_library = ? AND user_id = ?");
$stmt->bind_param("sii", $name, $id, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'List updated successfully!']);
    } else {
        // Pode acontecer se o nome for igual ou se o ID não for do user
        echo json_encode(['success' => true, 'message' => 'No changes made or list not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>