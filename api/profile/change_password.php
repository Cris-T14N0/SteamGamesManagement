<?php
header('Content-Type: application/json');
session_start();

// Verificar Autenticação
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (file_exists(__DIR__ . '/../../config.php')) {
    include __DIR__ . '/../../config.php';
} else {
    echo json_encode(['success' => false, 'message' => 'Config file not found']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Receber dados do POST (FormData do JS envia como $_POST)
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';

// Validações
if (empty($current_password) || empty($new_password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (strlen($new_password) < 6) {
    echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters']);
    exit;
}

try {
    // Buscar a password atual na BD para verificar
    $stmt = $conn->prepare("SELECT password FROM `USER` WHERE id_user = ?");
    if (!$stmt) {
        throw new Exception("Database prepare error: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Se o user não existir ou a password atual estiver errada
    if (!$user || !password_verify($current_password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Incorrect current password']);
        exit;
    }

    // Atualizar para a nova password
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    $stmtUpdate = $conn->prepare("UPDATE `USER` SET password = ? WHERE id_user = ?");
    $stmtUpdate->bind_param("si", $new_hash, $user_id);

    if ($stmtUpdate->execute()) {
        echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating password']);
    }

    $stmtUpdate->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>