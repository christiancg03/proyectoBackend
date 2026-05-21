<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false];

if (!isset($_POST["usuario_id"], $_POST["amigo_id"])) {
    echo json_encode($response);
    exit;
}

$usuarioId = (int) $_POST["usuario_id"];
$amigoId   = (int) $_POST["amigo_id"];

try {
    $conexion = db::conexion();

    $stmt = $conexion->prepare("
        DELETE FROM amigos
        WHERE usuario_id = :amigo
          AND amigo_id = :usuario
          AND aceptado = 0
    ");

    $stmt->execute([
        ":amigo" => $amigoId,
        ":usuario" => $usuarioId
    ]);

    if ($stmt->rowCount() > 0) {
        $response["success"] = true;
    }

} catch (Exception $e) {}

echo json_encode($response);