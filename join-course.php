<?php
session_start();
include('connect.php');

// Verificar si el usuario es estudiante
if ($_SESSION['user_type'] != 'student') {
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit();
}

// Obtener datos del POST
$data = json_decode(file_get_contents('php://input'), true);
$courseId = $data['courseId'] ?? null;

if (!$courseId) {
    echo json_encode(['success' => false, 'message' => 'ID de curso no proporcionado']);
    exit();
}

// Verificar si el estudiante ya está matriculado
$checkQuery = "SELECT id_matricula FROM matriculaciones 
              WHERE id_estudiante = ? AND id_curso = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("ii", $_SESSION['user_id'], $courseId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Ya estás matriculado en este curso']);
    exit();
}
$stmt->close();

// Matricular al estudiante en el curso
$insertQuery = "INSERT INTO matriculaciones (id_estudiante, id_curso, fecha_matricula, estado)
               VALUES (?, ?, NOW(), 'activo')";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("ii", $_SESSION['user_id'], $courseId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Matriculación exitosa']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al matricular: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>