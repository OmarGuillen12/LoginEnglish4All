<?php
session_start();
include('connect.php');

if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] != 'teacher' && $_SESSION['user_type'] != 'admin')) {
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['quizId']) || !is_numeric($data['quizId'])) {
    echo json_encode(['success' => false, 'message' => 'ID de examen no válido']);
    exit();
}

$userId = $_SESSION['user_id'];
$quizId = $data['quizId'];

try {
    // Verificar que el examen pertenece al usuario
    $query = "SELECT id FROM quiz WHERE id = ? AND created_by = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $quizId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Examen no encontrado o no tienes permiso']);
        exit();
    }
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    // Eliminar preguntas primero (por la clave foránea)
    $query = "DELETE FROM preguntas WHERE quiz_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    
    // Luego eliminar el examen
    $query = "DELETE FROM quiz WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    
    // Confirmar transacción
    $conn->commit();
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el examen: ' . $e->getMessage()]);
}
?>