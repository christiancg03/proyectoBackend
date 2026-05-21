<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false, "data" => []];

if (!isset($_GET["user_id"])) {
    echo json_encode($response);
    exit;
}

$userId = (int) $_GET["user_id"];

try {
    $conexion = db::conexion();

    $sql = "
        SELECT 
            a.id,
            a.usuario_id,
            a.amigo_id,
            a.aceptado,
            a.creado_en,
            u.id AS user_relacionado_id,
            u.usuario,
            u.foto
        FROM amigos a
        JOIN usuarios u 
            ON u.id = IF(
                a.usuario_id = :userId,
                a.amigo_id,
                a.usuario_id
            )
        WHERE a.usuario_id = :userId
           OR a.amigo_id = :userId
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([":userId" => $userId]);

    $response["success"] = true;
    $response["data"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
}

echo json_encode($response);