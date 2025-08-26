<?php
session_start();
header('Content-Type: application/json');

// Verificar si el archivo de conexión existe
if (!file_exists('connect.php')) {
    echo json_encode(['success' => false, 'message' => 'Error de configuración del servidor']);
    exit();
}

include('connect.php');

// Verificar método de solicitud
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// Verificar sesión y tipo de usuario
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit();
}

if ($_SESSION['user_type'] !== 'teacher') {
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit();
}

// Obtener y validar datos JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit();
}

if (empty($data['title']) || empty($data['questions'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // Insertar quiz
    $stmt = $conn->prepare("INSERT INTO quiz (title, created_by) VALUES (?, ?)");
    $stmt->bind_param("si", $data['title'], $_SESSION['user_id']);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al guardar el quiz: " . $stmt->error);
    }
    
    $quizId = $conn->insert_id;
    $stmt->close();

    // Insertar preguntas
    $stmt = $conn->prepare("INSERT INTO preguntas (quiz_id, pregunta, respuesta1, respuesta2, respuesta3, respuesta_correcta) VALUES (?, ?, ?, ?, ?, '1')");
    
    foreach ($data['questions'] as $question) {
        $stmt->bind_param("issss", $quizId, $question['text'], $question['option1'], $question['option2'], $question['option3']);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al guardar pregunta: " . $stmt->error);
        }
    }
    
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Examen guardado correctamente', 'quiz_id' => $quizId]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->close();
    $conn->close();
}
?>