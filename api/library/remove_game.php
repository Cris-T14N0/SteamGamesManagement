<?php
header('Content-Type: application/json');
session_start();
include '../../config.php';

// 1. Verificar Login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// 2. Receber dados
$data = json_decode(file_get_contents("php://input"), true);
$game_id = $data['game_id'] ?? null;

if (!$game_id) {
    echo json_encode(['success' => false, 'message' => 'Game ID is required']);
    exit;
}

$user_id = $_SESSION['user_id'];

// 3. APAGAR DE TODAS AS LISTAS DO UTILIZADOR
// Usamos um DELETE com JOIN para garantir que apagamos o jogo de qualquer biblioteca
$sql = "
    DELETE LIBRARY_GAME 
    FROM LIBRARY_GAME 
    INNER JOIN LIBRARY ON LIBRARY_GAME.library_id = LIBRARY.id_library
    WHERE LIBRARY.user_id = ? AND LIBRARY_GAME.game_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $game_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Game removed from all your lists!']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Game was not in any list.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>