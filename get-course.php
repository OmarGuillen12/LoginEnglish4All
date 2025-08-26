<?php
session_start();
include('connect.php');

if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] != 'teacher' && $_SESSION['user_type'] != 'admin')) {
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'ID de curso no válido']);
    exit();
}

$courseId = $_GET['id'];
$userId = $_SESSION['user_id'];

try {
    // Obtener información básica del curso
    $query = "SELECT id_curso, titulo, descripcion, estado, fecha_creacion 
              FROM curso 
              WHERE id_curso = ? AND id_creador = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $courseId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['error' => 'Curso no encontrado o no tienes permiso']);
        exit();
    }
    
    $courseData = $result->fetch_assoc();
    
    // Obtener las secciones del curso
    $query = "SELECT id_seccion, titulo, definicion, ejemplo 
              FROM seccion 
              WHERE id_curso = ? 
              ORDER BY id_seccion";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sections = [];
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }
    
    $courseData['sections'] = $sections;
    
    echo json_encode($courseData);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>