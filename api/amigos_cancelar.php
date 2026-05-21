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

    $sql = "
        DELETE FROM amigos 
        WHERE usuario_id = :usuario_id 
          AND amigo_id = :amigo_id 
          AND aceptado = 0
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ":usuario_id" => $usuarioId,
        ":amigo_id" => $amigoId
    ]);

    if ($stmt->rowCount() > 0) {
        $response["success"] = true;
    }

} catch (Exception $e) {
}

echo json_encode($response);