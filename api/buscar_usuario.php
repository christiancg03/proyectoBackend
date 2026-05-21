<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false, "message" => "El usuario no existe"];

if (!isset($_GET["usuario"], $_GET["mi_id"])) {
    echo json_encode($response);
    exit;
}

$usuario = trim($_GET["usuario"]);
$miId = (int) $_GET["mi_id"];

try {
    $conexion = db::conexion();

    // Solo buscamos si el nombre existe
    $stmt = $conexion->prepare("
        SELECT id, usuario, foto
        FROM usuarios
        WHERE usuario = :usuario
    ");
    $stmt->execute([":usuario" => $usuario]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $response["success"] = true;
        $response["data"] = $user;
        unset($response["message"]);
    } else {
        // Si no entra en el IF, enviará el mensaje por defecto: "El usuario no existe"
        $response["success"] = false;
    }

} catch (Exception $e) {
    $response["message"] = "Error en el servidor";
}

echo json_encode($response);