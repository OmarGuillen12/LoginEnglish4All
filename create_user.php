<?php
session_start();
include('connect.php');

// Verificar que sea una solicitud POST y que el usuario sea admin
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("HTTP/1.1 403 Forbidden");
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit();
}

// Obtener datos del POST
$data = json_decode(file_get_contents('php://input'), true);

// Validar datos
if (empty($data['firstName']) || empty($data['lastName']) || empty($data['email']) || empty($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
    exit();
}

// Verificar si el email ya existe
$email = $data['email'];
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'El email ya está registrado']);
    exit();
}
$stmt->close();

// Hash de la contraseña
$hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

// Insertar nuevo profesor
$stmt = $conn->prepare("INSERT INTO users (firstName, lastName, email, password, user_type) VALUES (?, ?, ?, ?, ?)");
$user_type = 'teacher'; // Siempre se crea como profesor
$stmt->bind_param("sssss", $data['firstName'], $data['lastName'], $data['email'], $hashed_password, $user_type);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al crear el usuario']);
}

$stmt->close();
$conn->close();
?>