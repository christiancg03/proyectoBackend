<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Administracion/config/db.php";

$response = ["success" => false];

try {
    $conexion = db::conexion();

    $sql = "SELECT c.*, COALESCE(u.usuario, c.nombre) AS nombre FROM contacto c LEFT JOIN usuarios u ON c.usuario_id = u.id ORDER BY c.fecha DESC";
    $stmt = $conexion->prepare($sql);
    
    if ($stmt->execute()) {
        $contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            "success" => true, 
            "data" => $contactos
        ];
    }
} catch (Exception $e) {
    $response["error"] = $e->getMessage();
}

echo json_encode($response);