<?php
session_start();
include('connect.php');

// Verificar si el usuario es estudiante
if ($_SESSION['user_type'] != 'student') {
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

// Consulta para obtener los quizzes asignados al estudiante
$query = "SELECT q.id, q.title, q.created_at, 
                 CONCAT(u.firstName, ' ', u.lastName) AS teacher,
                 COUNT(p.id) AS questions_count,
                 r.puntaje, r.fecha_completado
          FROM quiz q
          JOIN users u ON q.created_by = u.id
          JOIN preguntas p ON p.quiz_id = q.id
          LEFT JOIN resultados_quizzes r ON r.id_quiz = q.id AND r.id_estudiante = ?
          WHERE q.id IN (
              SELECT DISTINCT p.quiz_id 
              FROM preguntas p
              JOIN curso c ON p.quiz_id IN (
                  SELECT q2.id FROM quiz q2 WHERE q2.created_by IN (
                      SELECT id_creador FROM curso WHERE id_curso IN (
                          SELECT id_curso FROM matriculaciones WHERE id_estudiante = ?
                      )
                  )
              )
          )
          GROUP BY q.id, q.title, q.created_at, teacher, r.puntaje, r.fecha_completado";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $_SESSION['user_id'], $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo json_encode(['error' => 'Error al obtener los exámenes: ' . $conn->error]);
    exit();
}

$quizzes = [];
while ($row = $result->fetch_assoc()) {
    $row['completado'] = !is_null($row['puntaje']);
    $quizzes[] = $row;
}

echo json_encode($quizzes);
$conn->close();
?>