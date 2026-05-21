<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false, "data" => []];

try {
    $conexion = db::conexion();

    // Obtener 10 preguntas aleatorias de la tabla quiz
    $sql = "
        SELECT id, pregunta, opcion_a, opcion_b, opcion_c, opcion_d, correcta, dificultad 
        FROM quiz 
        ORDER BY RAND() 
        LIMIT 10
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    $response["success"] = true;
    $response["data"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $response["message"] = "Error al obtener las preguntas del quiz";
}

echo json_encode($response);
?>