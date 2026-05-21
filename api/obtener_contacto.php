<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$conn = db::conexion();

$usuario_id = $_GET["usuario_id"] ?? null;

if (!$usuario_id) {
    echo json_encode(["success" => false, "data" => []]);
    exit;
}

$sql = $conn->prepare("
    SELECT 
        id,
        mensaje,
        fecha,
        respuesta,
        fecha_respuesta
    FROM contacto
    WHERE usuario_id = :usuario_id
    ORDER BY fecha DESC
");

$sql->execute([
    ":usuario_id" => $usuario_id
]);

$mensajes = $sql->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "data" => $mensajes
]);