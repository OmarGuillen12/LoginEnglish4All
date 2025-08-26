<?php
header('Content-Type: application/json; charset=utf-8');

// Incluir el archivo de conexión
require_once 'connect.php';

// Consulta para obtener las frases
$sql = "SELECT phrase FROM pronunciacion";
$result = $conn->query($sql);

$phrases = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $phrases[] = $row["phrase"];
    }
}

// Cerrar conexión
$conn->close();

// Convertir a JSON para enviar al frontend
echo json_encode($phrases);
?>