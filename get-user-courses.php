<?php
session_start();
include('connect.php');

if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] != 'teacher' && $_SESSION['user_type'] != 'admin')) {
    echo json_encode([]);
    exit();
}

$userId = $_SESSION['user_id'];

// Obtener los cursos del usuario con conteo de secciones
$query = "SELECT c.id_curso, c.titulo, c.descripcion, c.estado, c.fecha_creacion,
          (SELECT COUNT(*) FROM seccion s WHERE s.id_curso = c.id_curso) as sections_count
          FROM curso c 
          WHERE c.id_creador = ?
          ORDER BY c.fecha_creacion DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

echo json_encode($courses);
?>