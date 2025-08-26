<?php
header('Content-Type: application/json; charset=utf-8');

// Incluir el archivo de conexión
require_once 'connect.php';

// Consulta para obtener los audios
$sql = "SELECT text, correct_option, wrong_options FROM audios";
$result = $conn->query($sql);

$audioDatabase = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Convertir JSON a array
        $wrongOptions = json_decode($row["wrong_options"], true);
        
        $audioDatabase[] = array(
            "text" => $row["text"],
            "correctOption" => $row["correct_option"],
            "wrongOptions" => $wrongOptions
        );
    }
}

// Cerrar conexión
$conn->close();

// Convertir a JSON para enviar al frontend
echo json_encode($audioDatabase);
?>