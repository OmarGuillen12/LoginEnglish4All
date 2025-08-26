<?php
session_start();
include('connect.php');

if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] != 'teacher' && $_SESSION['user_type'] != 'admin')) {
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['courseId']) || !is_numeric($data['courseId'])) {
    echo json_encode(['success' => false, 'message' => 'ID de curso no válido']);
    exit();
}

$userId = $_SESSION['user_id'];
$courseId = $data['courseId'];
$title = $data['title'];
$description = $data['description'];
$status = $data['status'];
$sections = $data['sections'];

try {
    // Verificar que el curso pertenece al usuario
    $query = "SELECT id_curso FROM curso WHERE id_curso = ? AND id_creador = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $courseId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Curso no encontrado o no tienes permiso']);
        exit();
    }
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    // Actualizar información básica del curso
    $query = "UPDATE curso SET titulo = ?, descripcion = ?, estado = ? WHERE id_curso = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $title, $description, $status, $courseId);
    $stmt->execute();
    
    // Eliminar secciones antiguas
    $query = "DELETE FROM seccion WHERE id_curso = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    
    // Insertar nuevas secciones
    $query = "INSERT INTO seccion (id_curso, titulo, definicion, ejemplo) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    foreach ($sections as $section) {
        $stmt->bind_param("isss", $courseId, $section['title'], $section['definition'], $section['example']);
        $stmt->execute();
    }
    
    // Confirmar transacción
    $conn->commit();
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el curso: ' . $e->getMessage()]);
}
?>