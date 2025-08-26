<?php
// Desactivar visualización de errores en producción
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Iniciar buffer de salida
ob_start();

session_start();
include('connect.php');

// Verificar sesión y permisos
if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] != 'teacher' && $_SESSION['user_type'] != 'admin')) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

$userId = $_SESSION['user_id'];
$response = [];

try {
    // Verificar conexión a la base de datos
    if ($conn->connect_error) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // Consulta para obtener los quizzes
    $query = "SELECT q.id, q.title, q.created_at,  
             (SELECT COUNT(*) FROM preguntas p WHERE p.quiz_id = q.id) as questions_count
             FROM quiz q 
             WHERE q.created_by = ?
             ORDER BY q.created_at DESC";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param("i", $userId);
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $quizzes = [];

    while ($row = $result->fetch_assoc()) {
        $quizzes[] = $row;
    }

    $response = $quizzes;

} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}

// Limpiar buffer y enviar respuesta
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>