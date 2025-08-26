<?php
session_start();
include('connect.php');

// Verificar que el usuario es profesor
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'teacher') {
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'ID de examen no válido']);
    exit();
}

$quizId = intval($_GET['id']);

try {
    // 1. Obtener información básica del examen
    $stmt = $conn->prepare("SELECT title FROM quiz WHERE id = ? AND created_by = ?");
    $stmt->bind_param("ii", $quizId, $_SESSION['user_id']);
    $stmt->execute();
    $quiz = $stmt->get_result()->fetch_assoc();
    
    if (!$quiz) {
        echo json_encode(['error' => 'Examen no encontrado o no tienes permiso']);
        exit();
    }
    
    // 2. Obtener todas las preguntas del examen
    $questions = [];
    $stmt = $conn->prepare("SELECT id, pregunta FROM preguntas WHERE quiz_id = ?");
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
    $questions[$row['id']] = [
        'text' => $row['pregunta'],
        'correct_answers' => 0,
        'option1_count' => 0,
        'option2_count' => 0,
        'option3_count' => 0
    ];
}
    
    if (empty($questions)) {
        echo json_encode([
            'questions' => [],
            'total_students' => 0,
            'average_correct' => 0
        ]);
        exit();
    }
    
    // 3. Obtener el número total de estudiantes que han realizado el examen
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT id_estudiante) as total 
        FROM respuestas_estudiantes 
        WHERE id_quiz = ?
    ");
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $totalStudents = $stmt->get_result()->fetch_assoc()['total'];
    
    // 4. Obtener estadísticas por pregunta
    $stmt = $conn->prepare("
        SELECT 
            id_pregunta,
            respuesta_dada,
            COUNT(*) as count
        FROM respuestas_estudiantes
        WHERE id_quiz = ?
        GROUP BY id_pregunta, respuesta_dada
    ");
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $questionId = $row['id_pregunta'];
        $answer = $row['respuesta_dada'];
        $count = $row['count'];
        
        if (!isset($questions[$questionId])) continue;
        
        // Contar respuestas por opción
        if ($answer == '1') {
            $questions[$questionId]['option1_count'] = $count;
            // Verificar si es la respuesta correcta
            $correctStmt = $conn->prepare("SELECT respuesta_correcta FROM preguntas WHERE id = ?");
            $correctStmt->bind_param("i", $questionId);
            $correctStmt->execute();
            $correctAnswer = $correctStmt->get_result()->fetch_assoc()['respuesta_correcta'];
            
            if ($correctAnswer == '1') {
                $questions[$questionId]['correct_answers'] = $count;
            }
        } 
        elseif ($answer == '2') {
            $questions[$questionId]['option2_count'] = $count;
            // Verificar si es la respuesta correcta
            $correctStmt = $conn->prepare("SELECT respuesta_correcta FROM preguntas WHERE id = ?");
            $correctStmt->bind_param("i", $questionId);
            $correctStmt->execute();
            $correctAnswer = $correctStmt->get_result()->fetch_assoc()['respuesta_correcta'];
            
            if ($correctAnswer == '2') {
                $questions[$questionId]['correct_answers'] = $count;
            }
        } 
        elseif ($answer == '3') {
            $questions[$questionId]['option3_count'] = $count;
            // Verificar si es la respuesta correcta
            $correctStmt = $conn->prepare("SELECT respuesta_correcta FROM preguntas WHERE id = ?");
            $correctStmt->bind_param("i", $questionId);
            $correctStmt->execute();
            $correctAnswer = $correctStmt->get_result()->fetch_assoc()['respuesta_correcta'];
            
            if ($correctAnswer == '3') {
                $questions[$questionId]['correct_answers'] = $count;
            }
        }
    }
    
    // 5. Calcular porcentajes y preparar datos para respuesta
    $processedQuestions = [];
    $totalCorrect = 0;
    $totalQuestions = 0;
    
    foreach ($questions as $id => $question) {
        $totalQuestions++;
        $totalCorrect += $question['correct_answers'];
        
        $processedQuestions[] = [
            'text' => $question['text'],
            'correct_answers' => $question['correct_answers'],
            'option1_percentage' => $totalStudents > 0 ? round(($question['option1_count'] / $totalStudents) * 100) : 0,
            'option2_percentage' => $totalStudents > 0 ? round(($question['option2_count'] / $totalStudents) * 100) : 0,
            'option3_percentage' => $totalStudents > 0 ? round(($question['option3_count'] / $totalStudents) * 100) : 0
        ];
    }
    
    $averageCorrect = $totalQuestions > 0 ? round(($totalCorrect / ($totalQuestions * max($totalStudents, 1))) * 100) : 0;
    
    echo json_encode([
        'questions' => $processedQuestions,
        'total_students' => $totalStudents,
        'average_correct' => $averageCorrect
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}