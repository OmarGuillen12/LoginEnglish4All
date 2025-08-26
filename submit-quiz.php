<?php
session_start();
include('connect.php');

// Verificar si el usuario es estudiante
if ($_SESSION['user_type'] != 'student') {
    die("Acceso no autorizado");
}

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: homepage-student.php");
    exit();
}

$quizId = $_POST['quiz_id'] ?? null;
$respuestas = $_POST['respuesta'] ?? [];

if (!$quizId || empty($respuestas)) {
    die("Datos incompletos");
}

// Verificar permiso para realizar este quiz
$permisoQuery = "SELECT q.id 
                FROM quiz q
                WHERE q.id = ? AND q.created_by IN (
                    SELECT id_creador FROM curso WHERE id_curso IN (
                        SELECT id_curso FROM matriculaciones WHERE id_estudiante = ?
                    )
                )";
$stmt = $conn->prepare($permisoQuery);
$stmt->bind_param("ii", $quizId, $_SESSION['user_id']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    die("No tienes permiso para realizar este examen");
}
$stmt->close();

// Obtener respuestas correctas y calcular puntaje
$preguntasQuery = "SELECT id, respuesta_correcta, respuesta1, respuesta2, respuesta3 FROM preguntas WHERE quiz_id = ?";
$stmt = $conn->prepare($preguntasQuery);
$stmt->bind_param("i", $quizId);
$stmt->execute();
$result = $stmt->get_result();

$totalPreguntas = $result->num_rows;
$correctas = 0;
$preguntasData = [];

while ($pregunta = $result->fetch_assoc()) {
    $preguntasData[$pregunta['id']] = $pregunta;
    if (isset($respuestas[$pregunta['id']])) {
        if ($respuestas[$pregunta['id']] == $pregunta['respuesta_correcta']) {
            $correctas++;
        }
    }
}

$puntaje = round(($correctas / $totalPreguntas) * 100, 2);

// Guardar resultados
$insertQuery = "INSERT INTO resultados_quizzes 
               (id_estudiante, id_quiz, puntaje, total_preguntas, fecha_completado)
               VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("iidi", $_SESSION['user_id'], $quizId, $puntaje, $totalPreguntas);

if ($stmt->execute()) {
    // Registrar respuestas individuales
    foreach ($respuestas as $preguntaId => $respuesta) {
        if (!isset($preguntasData[$preguntaId])) continue;
        
        $pregunta = $preguntasData[$preguntaId];
        $esCorrecta = ($respuesta == $pregunta['respuesta_correcta']) ? 1 : 0;
        
        $insertRespuesta = "INSERT INTO respuestas_estudiantes
                          (id_estudiante, id_quiz, id_pregunta, respuesta_dada, es_correcta, fecha_respuesta)
                          VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt2 = $conn->prepare($insertRespuesta);
        $stmt2->bind_param("iiiii", $_SESSION['user_id'], $quizId, $preguntaId, $respuesta, $esCorrecta);
        $stmt2->execute();
        $stmt2->close();
    }
    
    header("Location: quiz-results.php?id=$quizId");
    exit();
} else {
    die("Error al guardar los resultados: " . $conn->error);
}

$stmt->close();
$conn->close();
?>