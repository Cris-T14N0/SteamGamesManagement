<?php
header('Content-Type: application/json');
session_start();

include __DIR__ . '/../../config.php';

// Recebe o JSON
$data = json_decode(file_get_contents("php://input"), true);

// Validação básica
if (!$data || !isset($data['email'], $data['password'])) {
    echo json_encode(["success" => false, "message" => "Email and password required"]);
    exit;
}

$email = $data['email'];
$password = $data['password'];

$stmt = $conn->prepare("SELECT * FROM `USER` WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "User not found"]);
    exit;
}

$user = $result->fetch_assoc();

// Verifica a password
if (password_verify($password, $user['password'])) {
    
    // A coluna na base de dados é 'id_user', não 'id'
    $_SESSION['user_id'] = $user['id_user']; 
    $_SESSION['username'] = $user['username'];
    
    echo json_encode(["success" => true, "message" => "Login successful"]);
}
else {
    echo json_encode(["success" => false, "message" => "Incorrect password"]);
}

$stmt->close();
$conn->close();
?>