<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Administracion/config/db.php";

$response = ["success" => false];

try {
    $conexion = db::conexion();

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        
        $sql = "DELETE FROM noticias WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            $response = ["success" => true];
        }
    }
} catch (Exception $e) {
    $response["error"] = $e->getMessage();
}

echo json_encode($response);