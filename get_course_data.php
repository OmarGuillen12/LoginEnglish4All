<?php
require_once 'connect.php';

header('Content-Type: application/json');

if(!isset($_GET['course_id'])) {
    echo json_encode(['error' => 'Course ID not provided']);
    exit;
}

$courseId = intval($_GET['course_id']);

// Obtener información del curso
$courseQuery = "SELECT * FROM curso WHERE id_curso = $courseId";
$courseResult = mysqli_query($conn, $courseQuery);
$course = mysqli_fetch_assoc($courseResult);

// Obtener secciones del curso
$sectionsQuery = "SELECT * FROM seccion WHERE id_curso = $courseId ORDER BY orden";
$sectionsResult = mysqli_query($conn, $sectionsQuery);
$sections = [];

while($section = mysqli_fetch_assoc($sectionsResult)) {
    $sections[] = $section;
}

echo json_encode([
    'course' => $course,
    'sections' => $sections
]);

mysqli_close($conn);
?>