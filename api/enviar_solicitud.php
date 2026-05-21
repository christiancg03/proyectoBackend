<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false, "message" => ""];

if (!isset($_POST["usuario_id"], $_POST["amigo_id"])) {
    $response["message"] = "Datos incompletos";
    echo json_encode($response);
    exit;
}

$usuarioId = (int) $_POST["usuario_id"];
$amigoId   = (int) $_POST["amigo_id"];

if ($usuarioId === $amigoId) {
    $response["message"] = "No te puedes enviar una solicitud a ti mismo";
    echo json_encode($response);
    exit;
}

try {
    $conexion = db::conexion();

    // Comprobar si ya existe relación o solicitud
    $check = $conexion->prepare("
        SELECT id, aceptado 
        FROM amigos
        WHERE (usuario_id = :u AND amigo_id = :a)
           OR (usuario_id = :a AND amigo_id = :u)
        LIMIT 1
    ");
    $check->execute([
        ":u" => $usuarioId,
        ":a" => $amigoId
    ]);

    if ($relacion = $check->fetch(PDO::FETCH_ASSOC)) {
        if ((int)$relacion["aceptado"] === 1) {
            $response["message"] = "Este usuario ya lo tienes agregado como amigo";
        } else {
            $response["message"] = "Ya existe una solicitud pendiente con este usuario";
        }
        echo json_encode($response);
        exit;
    }

    // Insertar solicitud
    $stmt = $conexion->prepare("
        INSERT INTO amigos (usuario_id, amigo_id, aceptado, creado_en)
        VALUES (:u, :a, 0, NOW())
    ");

    $stmt->execute([
        ":u" => $usuarioId,
        ":a" => $amigoId
    ]);

    $response["success"] = true;
    $response["message"] = "Solicitud enviada correctamente";

} catch (Exception $e) {
    $response["message"] = "Error interno";
}

echo json_encode($response);