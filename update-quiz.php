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
$title = $data['title'];
$questions = $data['questions'];

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
    
    // Actualizar título del examen
    $query = "UPDATE quiz SET title = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $title, $quizId);
    $stmt->execute();
    
    // Eliminar preguntas antiguas
    $query = "DELETE FROM preguntas WHERE quiz_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    
    // Insertar nuevas preguntas
    $query = "INSERT INTO preguntas (quiz_id, pregunta, respuesta1, respuesta2, respuesta3, respuesta_correcta) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    foreach ($questions as $question) {
        $correctAnswer = isset($question['correct_answer']) ? $question['correct_answer'] : '1';
        $stmt->bind_param("isssss", 
            $quizId, 
            $question['text'], 
            $question['option1'], 
            $question['option2'], 
            $question['option3'],
            $correctAnswer
        );
        $stmt->execute();
    }
    
    // Confirmar transacción
    $conn->commit();
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el examen: ' . $e->getMessage()]);
}
?>