<?php
header('Content-Type: application/json');
session_start();
include '../../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Buscar todas as listas do utilizador
$stmt = $conn->prepare("SELECT id_library, name FROM LIBRARY WHERE user_id = ? ORDER BY id_library ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$lists = [];
while ($row = $result->fetch_assoc()) {
    $lists[] = $row;
}

echo json_encode(['success' => true, 'lists' => $lists]);

$stmt->close();
$conn->close();
?>