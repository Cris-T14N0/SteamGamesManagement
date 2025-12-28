<?php
header('Content-Type: application/json');
session_start();
include '../../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$game_id = $data['game_id'] ?? null;
$list_ids = $data['list_ids'] ?? [];

if (!$game_id) {
    echo json_encode(['success' => false, 'message' => 'Game ID is required']);
    exit;
}

if (empty($list_ids)) {
    echo json_encode(['success' => false, 'message' => 'No lists selected']);
    exit;
}

$user_id = $_SESSION['user_id'];
$successCount = 0;

// Prepara a inserção
$stmt = $conn->prepare("INSERT IGNORE INTO LIBRARY_GAME (library_id, game_id) VALUES (?, ?)");

foreach ($list_ids as $library_id) {
    // Validação de segurança extra: garantir que a lista pertence ao user
    $check = $conn->prepare("SELECT id_library FROM LIBRARY WHERE id_library = ? AND user_id = ?");
    $check->bind_param("ii", $library_id, $user_id);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        // Se a lista é do user, insere o jogo
        $stmt->bind_param("ii", $library_id, $game_id);
        if ($stmt->execute()) {
            $successCount++;
        }
    }
    $check->close();
}

if ($successCount > 0) {
    echo json_encode(['success' => true, 'message' => "Game added to $successCount lists!"]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add to lists']);
}

$stmt->close();
$conn->close();
?>