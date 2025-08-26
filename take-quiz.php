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

// Verificar si el estudiante puede tomar este quiz
$permisoQuery = "SELECT q.id, q.title 
                FROM quiz q
                JOIN users u ON q.created_by = u.id
                WHERE q.id = ? AND q.created_by IN (
                    SELECT id_creador FROM curso WHERE id_curso IN (
                        SELECT id_curso FROM matriculaciones WHERE id_estudiante = ?
                    )
                )";
$stmt = $conn->prepare($permisoQuery);
$stmt->bind_param("ii", $quizId, $_SESSION['user_id']);
$stmt->execute();
$quizResult = $stmt->get_result();

if ($quizResult->num_rows === 0) {
    die("No tienes permiso para realizar este examen o no existe");
}

$quiz = $quizResult->fetch_assoc();
$stmt->close();

// Verificar si ya completó este quiz
$completadoQuery = "SELECT id_resultado FROM resultados_quizzes 
                   WHERE id_estudiante = ? AND id_quiz = ?";
$stmt = $conn->prepare($completadoQuery);
$stmt->bind_param("ii", $_SESSION['user_id'], $quizId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    header("Location: quiz-results.php?id=$quizId");
    exit();
}
$stmt->close();

// Obtener preguntas del quiz
$preguntasQuery = "SELECT id, pregunta, respuesta1, respuesta2, respuesta3 
                  FROM preguntas WHERE quiz_id = ?";
$stmt = $conn->prepare($preguntasQuery);
$stmt->bind_param("i", $quizId);
$stmt->execute();
$preguntasResult = $stmt->get_result();
$preguntas = $preguntasResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Examen: <?php echo htmlspecialchars($quiz['title']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .quiz-container {
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
            margin-bottom: 30px;
        }
        .question {
            margin-bottom: 30px;
            padding: 15px;
            border-left: 4px solid #9d50bb;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        .question-text {
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 1.1em;
        }
        .options {
            margin-left: 20px;
        }
        .option {
            margin-bottom: 10px;
        }
        .option input {
            margin-right: 10px;
        }
        .timer {
            text-align: center;
            font-size: 1.2em;
            margin: 20px 0;
            color: #e74c3c;
            font-weight: bold;
        }
        .submit-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #6e48aa 0%, #9d50bb 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1.1em;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(44, 62, 80, 0.3);
        }
        .submit-btn:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div class="quiz-container">
        <h1><?php echo htmlspecialchars($quiz['title']); ?></h1>
        
        <div class="timer" id="quiz-timer">
            Tiempo restante: 30:00
        </div>
        
        <form id="quiz-form" action="submit-quiz.php" method="POST">
            <input type="hidden" name="quiz_id" value="<?php echo $quizId; ?>">
            
            <?php foreach ($preguntas as $index => $pregunta): ?>
            <div class="question">
                <div class="question-text">
                    <?php echo ($index + 1) . '. ' . htmlspecialchars($pregunta['pregunta']); ?>
                </div>
                <div class="options">
                    <div class="option">
                        <input type="radio" name="respuesta[<?php echo $pregunta['id']; ?>]" 
                               id="p<?php echo $pregunta['id']; ?>-1" value="1" required>
                        <label for="p<?php echo $pregunta['id']; ?>-1">
                            <?php echo htmlspecialchars($pregunta['respuesta1']); ?>
                        </label>
                    </div>
                    <div class="option">
                        <input type="radio" name="respuesta[<?php echo $pregunta['id']; ?>]" 
                               id="p<?php echo $pregunta['id']; ?>-2" value="2">
                        <label for="p<?php echo $pregunta['id']; ?>-2">
                            <?php echo htmlspecialchars($pregunta['respuesta2']); ?>
                        </label>
                    </div>
                    <div class="option">
                        <input type="radio" name="respuesta[<?php echo $pregunta['id']; ?>]" 
                               id="p<?php echo $pregunta['id']; ?>-3" value="3">
                        <label for="p<?php echo $pregunta['id']; ?>-3">
                            <?php echo htmlspecialchars($pregunta['respuesta3']); ?>
                        </label>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <button type="submit" class="submit-btn">
                <i class="fas fa-paper-plane"></i> Enviar Examen
            </button>
        </form>
    </div>

    <script>
    // Temporizador del examen (30 minutos)
    let timeLeft = 30 * 60; // 30 minutos en segundos
    
    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        document.getElementById('quiz-timer').textContent = 
            `Tiempo restante: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        
        if (timeLeft <= 0) {
            // Tiempo agotado, enviar formulario automáticamente
            document.getElementById('quiz-form').submit();
        } else {
            timeLeft--;
            setTimeout(updateTimer, 1000);
        }
    }
    
    // Iniciar temporizador
    updateTimer();
    
    // Prevenir recarga de página accidental
    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        e.returnValue = '¿Estás seguro de que quieres salir? Tu progreso se perderá.';
    });
    </script>
</body>
</html>