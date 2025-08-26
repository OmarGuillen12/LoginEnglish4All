<?php
session_start();
include('connect.php');

// Verificar si el usuario es estudiante
if ($_SESSION['user_type'] != 'student') {
    header("Location: index.php");
    exit();
}

$quizId = $_GET['id'] ?? null;

if (!$quizId) {
    die("ID de examen no proporcionado");
}

// Obtener resultados del quiz
$resultadosQuery = "SELECT r.puntaje, r.total_preguntas, r.fecha_completado, q.title,
                   CONCAT(u.firstName, ' ', u.lastName) AS teacher
                   FROM resultados_quizzes r
                   JOIN quiz q ON r.id_quiz = q.id
                   JOIN users u ON q.created_by = u.id
                   WHERE r.id_quiz = ? AND r.id_estudiante = ?";
$stmt = $conn->prepare($resultadosQuery);
$stmt->bind_param("ii", $quizId, $_SESSION['user_id']);
$stmt->execute();
$resultados = $stmt->get_result();

if ($resultados->num_rows === 0) {
    die("No se encontraron resultados para este examen");
}

$quizData = $resultados->fetch_assoc();
$stmt->close();

// Obtener detalles de las respuestas con las opciones completas
$respuestasQuery = "SELECT p.id, p.pregunta, p.respuesta_correcta, 
                   p.respuesta1, p.respuesta2, p.respuesta3,
                   re.respuesta_dada, re.es_correcta
                   FROM respuestas_estudiantes re
                   JOIN preguntas p ON re.id_pregunta = p.id
                   WHERE re.id_quiz = ? AND re.id_estudiante = ?
                   ORDER BY p.id";
$stmt = $conn->prepare($respuestasQuery);
$stmt->bind_param("ii", $quizId, $_SESSION['user_id']);
$stmt->execute();
$respuestasResult = $stmt->get_result();
$respuestas = $respuestasResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados: <?php echo htmlspecialchars($quizData['title']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .results-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 10px;
        }
        .quiz-meta {
            text-align: center;
            margin-bottom: 30px;
            color: #7f8c8d;
        }
        .score-container {
            text-align: center;
            margin: 30px 0;
        }
        .score {
            font-size: 3em;
            font-weight: bold;
            color: <?php echo ($quizData['puntaje'] >= 70 ? '#27ae60' : '#e74c3c'); ?>;
        }
        .score-text {
            font-size: 1.2em;
            margin-top: 10px;
        }
        .answers-summary {
            display: flex;
            justify-content: space-around;
            margin: 30px 0;
        }
        .summary-item {
            text-align: center;
        }
        .summary-number {
            font-size: 2em;
            font-weight: bold;
            color: #2c3e50;
        }
        .summary-label {
            color: #7f8c8d;
        }
        .answers-details {
            margin-top: 40px;
        }
        .answer {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        .answer.correct {
            border-left: 4px solid #27ae60;
        }
        .answer.incorrect {
            border-left: 4px solid #e74c3c;
        }
        .question-text {
            font-weight: 600;
            margin-bottom: 10px;
        }
        .answer-info {
            display: flex;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .your-answer, .correct-answer {
            flex: 1;
            min-width: 200px;
            margin-bottom: 10px;
        }
        .label {
            font-weight: 600;
            color: #7f8c8d;
            margin-bottom: 5px;
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
        @media (max-width: 600px) {
            .answers-summary {
                flex-direction: column;
            }
            .summary-item {
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="results-container">
        <h1><?php echo htmlspecialchars($quizData['title']); ?></h1>
        <div class="quiz-meta">
            <p>Profesor: <?php echo htmlspecialchars($quizData['teacher']); ?></p>
            <p>Completado: <?php echo date('d/m/Y H:i', strtotime($quizData['fecha_completado'])); ?></p>
        </div>
        
        <div class="score-container">
            <div class="score"><?php echo $quizData['puntaje']; ?>%</div>
            <div class="score-text">
                <?php 
                if ($quizData['puntaje'] >= 90) {
                    echo "¡Excelente trabajo!";
                } elseif ($quizData['puntaje'] >= 70) {
                    echo "Buen trabajo, sigue así";
                } elseif ($quizData['puntaje'] >= 50) {
                    echo "Puedes mejorar";
                } else {
                    echo "Considera repasar el material";
                }
                ?>
            </div>
        </div>
        
        <div class="answers-summary">
            <div class="summary-item">
                <div class="summary-number"><?php echo $quizData['total_preguntas']; ?></div>
                <div class="summary-label">Preguntas</div>
            </div>
            <div class="summary-item">
                <div class="summary-number"><?php echo round($quizData['puntaje'] / 100 * $quizData['total_preguntas']); ?></div>
                <div class="summary-label">Correctas</div>
            </div>
            <div class="summary-item">
                <div class="summary-number"><?php echo $quizData['total_preguntas'] - round($quizData['puntaje'] / 100 * $quizData['total_preguntas']); ?></div>
                <div class="summary-label">Incorrectas</div>
            </div>
        </div>
        
        <div class="answers-details">
            <h2>Detalle de respuestas</h2>
            
            <?php foreach ($respuestas as $index => $respuesta): ?>
            <div class="answer <?php echo $respuesta['es_correcta'] ? 'correct' : 'incorrect'; ?>">
                <div class="question-text">
                    <?php echo ($index + 1) . '. ' . htmlspecialchars($respuesta['pregunta']); ?>
                </div>
                
                <div class="answer-info">
                    <div class="your-answer">
                        <div class="label">Tu respuesta:</div>
                        <div>
                            <?php 
                            // Mostrar la respuesta seleccionada por el estudiante
                            $respuestaEstudiante = '';
                            switch($respuesta['respuesta_dada']) {
                                case '1': $respuestaEstudiante = $respuesta['respuesta1']; break;
                                case '2': $respuestaEstudiante = $respuesta['respuesta2']; break;
                                case '3': $respuestaEstudiante = $respuesta['respuesta3']; break;
                                default: $respuestaEstudiante = "Respuesta no válida";
                            }
                            echo htmlspecialchars($respuestaEstudiante);
                            ?>
                        </div>
                    </div>
                    
                    <?php if (!$respuesta['es_correcta']): ?>
                    <div class="correct-answer">
                        <div class="label">Respuesta correcta:</div>
                        <div>
                            <?php 
                            // Mostrar la respuesta correcta
                            $respuestaCorrecta = '';
                            switch($respuesta['respuesta_correcta']) {
                                case '1': $respuestaCorrecta = $respuesta['respuesta1']; break;
                                case '2': $respuestaCorrecta = $respuesta['respuesta2']; break;
                                case '3': $respuestaCorrecta = $respuesta['respuesta3']; break;
                                default: $respuestaCorrecta = "Respuesta no válida";
                            }
                            echo htmlspecialchars($respuestaCorrecta);
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <a href="homepage-student.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Volver al panel
        </a>
    </div>
</body>
</html>
<?php
$conn->close();
?>