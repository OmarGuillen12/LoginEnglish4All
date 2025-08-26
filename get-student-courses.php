<?php
session_start();
include('connect.php');

// Verificar si el usuario es estudiante
if ($_SESSION['user_type'] != 'student') {
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

// Consulta para obtener los cursos del estudiante
$query = "SELECT c.id_curso, c.titulo, c.descripcion, c.estado, c.fecha_creacion,
                 CONCAT(u.firstName, ' ', u.lastName) AS creador,
                 m.fecha_matricula, m.calificacion_final
          FROM curso c
          JOIN users u ON c.id_creador = u.id
          JOIN matriculaciones m ON c.id_curso = m.id_curso
          WHERE m.id_estudiante = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo json_encode(['error' => 'Error al obtener los cursos: ' . $conn->error]);
    exit();
}

$courses = [];
while ($row = $result->fetch_assoc()) {
    // Obtener número de secciones para este curso
    $seccionesQuery = "SELECT COUNT(*) AS sections_count FROM seccion 
                      WHERE id_curso = ?";
    $stmt2 = $conn->prepare($seccionesQuery);
    $stmt2->bind_param("i", $row['id_curso']);
    $stmt2->execute();
    $seccionesResult = $stmt2->get_result();
    $seccionesData = $seccionesResult->fetch_assoc();
    $row['sections_count'] = $seccionesData['sections_count'];
    $stmt2->close();
    
    $courses[] = $row;
}

echo json_encode($courses);
$conn->close();
?>