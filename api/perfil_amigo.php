<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false, "usuario" => null, "biblioteca" => []];

if (!isset($_GET["amigo_id"])) {
    echo json_encode(["success" => false, "message" => "amigo_id es requerido"]);
    exit;
}

$amigoId = (int) $_GET["amigo_id"];

try {
    $conexion = db::conexion();

    // 1. Obtener datos del usuario (amigo)
    $sqlUser = "
        SELECT id, usuario, email, foto, nombre_completo, fecha_nacimiento, genero, pais, biografia, rol 
        FROM usuarios 
        WHERE id = :id
    ";
    $stmtUser = $conexion->prepare($sqlUser);
    $stmtUser->execute([":id" => $amigoId]);
    $usuarioData = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$usuarioData) {
        $response["message"] = "Usuario no encontrado";
        echo json_encode($response);
        exit;
    }

    // 2. Obtener su biblioteca de videojuegos
    $sqlLib = "
        SELECT id, usuario_id, titulo, saga, estado, opinion, valoracion, imagen, fecha_agregado 
        FROM biblioteca 
        WHERE usuario_id = :id 
        ORDER BY fecha_agregado DESC
    ";
    $stmtLib = $conexion->prepare($sqlLib);
    $stmtLib->execute([":id" => $amigoId]);
    $bibliotecaData = $stmtLib->fetchAll(PDO::FETCH_ASSOC);

    $response["success"] = true;
    $response["usuario"] = $usuarioData;
    $response["biblioteca"] = $bibliotecaData;

} catch (Exception $e) {
    $response["message"] = "Error al obtener el perfil del amigo: " . $e->getMessage();
}

echo json_encode($response);
?>