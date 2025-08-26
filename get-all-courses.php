<?php
session_start();
include('connect.php');

// Verificar si el usuario es estudiante
if ($_SESSION['user_type'] != 'student') {
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

// Consulta para obtener todos los cursos activos con información del profesor
$query = "SELECT c.id_curso, c.titulo, c.descripcion, c.estado, c.fecha_creacion, 
                 CONCAT(u.firstName, ' ', u.lastName) AS creador
          FROM curso c
          JOIN users u ON c.id_creador = u.id
          WHERE c.estado = 'activo'";

$result = $conn->query($query);

if (!$result) {
    echo json_encode(['error' => 'Error al obtener los cursos: ' . $conn->error]);
    exit();
}

$courses = [];
while ($row = $result->fetch_assoc()) {
    // Verificar si el estudiante ya está matriculado en este curso
    $matriculaQuery = "SELECT id_matricula FROM matriculaciones 
                      WHERE id_estudiante = ? AND id_curso = ?";
    $stmt = $conn->prepare($matriculaQuery);
    $stmt->bind_param("ii", $_SESSION['user_id'], $row['id_curso']);
    $stmt->execute();
    $stmt->store_result();
    
    $row['ya_matriculado'] = $stmt->num_rows > 0;
    $stmt->close();
    
    // Obtener número de secciones para este curso
    $seccionesQuery = "SELECT COUNT(*) AS sections_count FROM seccion 
                      WHERE id_curso = ?";
    $stmt = $conn->prepare($seccionesQuery);
    $stmt->bind_param("i", $row['id_curso']);
    $stmt->execute();
    $seccionesResult = $stmt->get_result();
    $seccionesData = $seccionesResult->fetch_assoc();
    $row['sections_count'] = $seccionesData['sections_count'];
    $stmt->close();
    
    $courses[] = $row;
}

echo json_encode($courses);
$conn->close();
?>