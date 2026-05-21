<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Administracion/config/db.php";

$response = ["success" => false];

try {
    $conexion = db::conexion();

    if (isset($_POST['id'], $_POST['respuesta'])) {
        $id = $_POST['id'];
        $respuesta = $_POST['respuesta'];
        
        $sql = "UPDATE contacto SET respuesta = :respuesta, fecha_respuesta = CURRENT_TIMESTAMP, leido = 1 WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":respuesta", $respuesta);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            $response = ["success" => true];
        }
    }
} catch (Exception $e) {
    $response["error"] = $e->getMessage();
}

echo json_encode($response);

