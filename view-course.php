<?php
session_start();
include('connect.php');

// Verificar si el usuario es estudiante
if ($_SESSION['user_type'] != 'student') {
    header("Location: index.php");
    exit();
}

$courseId = $_GET['id'] ?? null;

if (!$courseId) {
    die("ID de curso no proporcionado");
}

// Verificar si el estudiante está matriculado en este curso
$matriculaQuery = "SELECT m.id_matricula, c.titulo, c.descripcion, 
                  CONCAT(u.firstName, ' ', u.lastName) AS creador
                  FROM matriculaciones m
                  JOIN curso c ON m.id_curso = c.id_curso
                  JOIN users u ON c.id_creador = u.id
                  WHERE m.id_curso = ? AND m.id_estudiante = ?";
$stmt = $conn->prepare($matriculaQuery);
$stmt->bind_param("ii", $courseId, $_SESSION['user_id']);
$stmt->execute();
$cursoResult = $stmt->get_result();

if ($cursoResult->num_rows === 0) {
    die("No estás matriculado en este curso o no existe");
}

$curso = $cursoResult->fetch_assoc();
$stmt->close();

// Obtener secciones del curso
$seccionesQuery = "SELECT id_seccion, titulo, definicion, ejemplo, orden 
                  FROM seccion 
                  WHERE id_curso = ?
                  ORDER BY orden";
$stmt = $conn->prepare($seccionesQuery);
$stmt->bind_param("i", $courseId);
$stmt->execute();
$seccionesResult = $stmt->get_result();
$secciones = $seccionesResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Obtener quizzes asociados a este curso
$quizzesQuery = "SELECT q.id, q.title, r.puntaje
                FROM quiz q
                LEFT JOIN resultados_quizzes r ON r.id_quiz = q.id AND r.id_estudiante = ?
                WHERE q.created_by = (
                    SELECT id_creador FROM curso WHERE id_curso = ?
                )";
$stmt = $conn->prepare($quizzesQuery);
$stmt->bind_param("ii", $_SESSION['user_id'], $courseId);
$stmt->execute();
$quizzesResult = $stmt->get_result();
$quizzes = $quizzesResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curso: <?php echo htmlspecialchars($curso['titulo']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .course-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .course-meta {
            color: #7f8c8d;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ecf0f1;
        }
        .course-description {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
            border-left: 4px solid #9d50bb;
        }
        .sections-container {
            margin-top: 40px;
        }
        .section {
            margin-bottom: 30px;
            padding: 15px;
            border-radius: 4px;
            background-color: #f9f9f9;
            border-left: 4px solid #2ecc71;
        }
        .section-title {
            font-size: 1.3em;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        .section-content {
            margin-bottom: 15px;
        }
        .section-example {
            padding: 10px;
            background-color: #e8f4fc;
            border-radius: 4px;
            margin-top: 15px;
            border-left: 3px solid #9d50bb;
        }
        .example-label {
            font-weight: 600;
            color: #9d50bb;
            margin-bottom: 5px;
        }
        .quizzes-container {
            margin-top: 40px;
        }
        .quiz-item {
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
            border-left: 4px solid #e67e22;
        }
        .quiz-title {
            font-weight: 600;
            margin-bottom: 10px;
        }
        .quiz-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            background-color: <?php echo (isset($quiz['puntaje']) ? '#27ae60' : '#e74c3c'); ?>;
            color: white;
        }
        .take-quiz-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 15px;
            background: linear-gradient(135deg, #e67e22 0%, #f39c12 100%);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9em;
            transition: all 0.3s;
        }
        .take-quiz-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(230, 126, 34, 0.3);
        }
        .back-btn {
            display: block;
            text-align: center;
            margin-top: 30px;
            padding: 10px;
            background: linear-gradient(135deg, #6e48aa 0%, #9d50bb 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(44, 62, 80, 0.3);
        }
    </style>
</head>
<body>
    <div class="course-container">
        <h1><?php echo htmlspecialchars($curso['titulo']); ?></h1>
        <div class="course-meta">
            <p>Profesor: <?php echo htmlspecialchars($curso['creador']); ?></p>
        </div>
        
        <div class="course-description">
            <?php echo nl2br(htmlspecialchars($curso['descripcion'])); ?>
        </div>
        
        <div class="sections-container">
            <h2>Contenido del Curso</h2>
            
            <?php if (empty($secciones)): ?>
                <p>Este curso aún no tiene contenido disponible.</p>
            <?php else: ?>
                <?php foreach ($secciones as $seccion): ?>
                <div class="section">
                    <div class="section-title"><?php echo htmlspecialchars($seccion['titulo']); ?></div>
                    <div class="section-content">
                        <?php echo nl2br(htmlspecialchars($seccion['definicion'])); ?>
                    </div>
                    
                    <?php if (!empty($seccion['ejemplo'])): ?>
                    <div class="section-example">
                        <div class="example-label">Ejemplo:</div>
                        <?php echo nl2br(htmlspecialchars($seccion['ejemplo'])); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($quizzes)): ?>
        <div class="quizzes-container">
            <h2>Evaluaciones</h2>
            
            <?php foreach ($quizzes as $quiz): ?>
            <div class="quiz-item">
                <div class="quiz-title"><?php echo htmlspecialchars($quiz['title']); ?></div>
                
                <?php if (isset($quiz['puntaje'])): ?>
                    <span class="quiz-status">Completado: <?php echo $quiz['puntaje']; ?>%</span>
                    <a href="quiz-results.php?id=<?php echo $quiz['id']; ?>" class="take-quiz-btn">
                        <i class="fas fa-eye"></i> Ver Resultados
                    </a>
                <?php else: ?>
                    <span class="quiz-status">Pendiente</span>
                    <a href="take-quiz.php?id=<?php echo $quiz['id']; ?>" class="take-quiz-btn">
                        <i class="fas fa-play"></i> Realizar Examen
                    </a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <a href="homepage-student.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Volver al panel
        </a>
    </div>
</body>
</html>
<?php
$conn->close();
?>