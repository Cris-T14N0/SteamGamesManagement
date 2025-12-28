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

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'ID is required']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Apagar garantindo que a lista pertence ao user
// Como tens ON DELETE CASCADE na BD, os jogos na tabela de ligação apagam-se sozinhos
$stmt = $conn->prepare("DELETE FROM LIBRARY WHERE id_library = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'List deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'List not found or permission denied.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>