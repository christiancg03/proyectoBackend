<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$conn = db::conexion();

$usuario1 = $_GET["usuario1"] ?? null;
$usuario2 = $_GET["usuario2"] ?? null;

if (!$usuario1 || !$usuario2) {
    echo json_encode(["success" => false, "data" => []]);
    exit;
}

$sql = $conn->prepare("
    SELECT * FROM mensajes
    WHERE (emisor_id = :u1 AND receptor_id = :u2)
       OR (emisor_id = :u2 AND receptor_id = :u1)
    ORDER BY enviado_en ASC
");

$sql->execute([
    ":u1" => $usuario1,
    ":u2" => $usuario2
]);

$mensajes = $sql->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "data" => $mensajes
]);