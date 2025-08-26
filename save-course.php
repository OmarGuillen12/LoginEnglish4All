<?php
session_start();
include('connect.php');

// Verificar que el usuario es profesor/admin
if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] != 'teacher' && $_SESSION['user_type'] != 'admin')) {
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit();
}

// Obtener datos JSON
$data = json_decode(file_get_contents('php://input'), true);

try {
    // Iniciar transacción
    $conn->begin_transaction();
    
    // 1. Insertar el curso
    $stmt = $conn->prepare("INSERT INTO curso (id_creador, titulo, descripcion, estado) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $_SESSION['user_id'], $data['title'], $data['description'], $data['status']);
    $stmt->execute();
    $courseId = $conn->insert_id;
    
    // 2. Insertar las secciones
    $stmt = $conn->prepare("INSERT INTO seccion (id_curso, titulo, definicion, ejemplo) VALUES (?, ?, ?, ?)");
    
    foreach ($data['sections'] as $section) {
        $stmt->bind_param("isss", $courseId, $section['title'], $section['definition'], $section['example']);
        $stmt->execute();
    }
    
    // Confirmar transacción
    $conn->commit();
    
    echo json_encode(['success' => true, 'message' => 'Curso guardado con éxito', 'course_id' => $courseId]);
    
} catch (Exception $e) {
    // Revertir en caso de error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error al guardar el curso: ' . $e->getMessage()]);
}
?>