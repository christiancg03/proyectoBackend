<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false, "data" => null];

try {
    $conexion = db::conexion();

    // Obtener 1 palabra aleatoria de la tabla ahorcado
    $sql = "
        SELECT id, palabra, categoria 
        FROM ahorcado 
        ORDER BY RAND() 
        LIMIT 1
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $response["success"] = true;
        $response["data"] = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $response["message"] = "No se encontraron palabras en la base de datos.";
    }

} catch (Exception $e) {
    $response["message"] = "Error al obtener la palabra del ahorcado";
}

echo json_encode($response);
?>