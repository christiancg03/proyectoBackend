<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false, "data" => []];

if (!isset($_GET["usuario_id"])) {
    echo json_encode($response);
    exit;
}

$usuarioId = (int) $_GET["usuario_id"];

try {
    $conexion = db::conexion();

    $sql = "
        SELECT id, usuario_id, titulo, saga, estado, opinion,
               valoracion, imagen, fecha_agregado
        FROM biblioteca
        WHERE usuario_id = :usuario_id
        ORDER BY fecha_agregado DESC
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([":usuario_id" => $usuarioId]);

    $response["success"] = true;
    $response["data"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {}

echo json_encode($response);