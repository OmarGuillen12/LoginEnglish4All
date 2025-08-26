<?php
session_start();
include('connect.php');

if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] != 'teacher' && $_SESSION['user_type'] != 'admin')) {
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'ID de examen no válido']);
    exit();
}

$quizId = $_GET['id'];
$userId = $_SESSION['user_id'];

try {
    // Obtener información básica del examen
    $query = "SELECT id, title, created_at FROM quiz WHERE id = ? AND created_by = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $quizId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['error' => 'Examen no encontrado o no tienes permiso']);
        exit();
    }
    
    $quizData = $result->fetch_assoc();
    
    // Obtener las preguntas del examen
    $query = "SELECT id, pregunta as text, respuesta1 as option1, respuesta2 as option2, respuesta3 as option3, respuesta_correcta 
              FROM preguntas 
              WHERE quiz_id = ? 
              ORDER BY id";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questions[] = [
            'text' => $row['text'],
            'option1' => $row['option1'],
            'option2' => $row['option2'],
            'option3' => $row['option3'],
            'correct_answer' => $row['respuesta_correcta']
        ];
    }
    
    $quizData['questions'] = $questions;
    
    echo json_encode($quizData);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>