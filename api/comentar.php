<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false, "message" => ""];

if (!isset($_POST["noticia_id"]) || !isset($_POST["usuario_id"]) || !isset($_POST["contenido"])) {
    $response["message"] = "Faltan campos obligatorios";
    echo json_encode($response);
    exit;
}

$noticiaId = (int) $_POST["noticia_id"];
$usuarioId = (int) $_POST["usuario_id"];
$contenido = trim($_POST["contenido"]);
$respuestaAId = isset($_POST["respuesta_a_id"]) ? (int)$_POST["respuesta_a_id"] : null;

if (empty($contenido)) {
    $response["message"] = "El comentario no puede estar vacío";
    echo json_encode($response);
    exit;
}

try {
    $conexion = db::conexion();

    $sql = "
    INSERT INTO comentarios (noticia_id, usuario_id, contenido, fecha, respuesta_a_id) 
            VALUES (:noticia_id, :usuario_id, :contenido, NOW(), :respuesta_a_id)";

    $stmt = $conexion->prepare($sql);
    $exito = $stmt->execute([
        ":noticia_id" => $noticiaId,
        ":usuario_id" => $usuarioId,
        ":contenido" => $contenido,
        ":respuesta_a_id" => $respuestaAId
    ]);

    if ($exito) {
        $response["success"] = true;
        $response["message"] = "Comentario insertado correctamente";
    } else {
        $response["message"] = "Error al insertar en la base de datos";
    }

} catch (Exception $e) {
    $response["message"] = "Error de base de datos: " . $e->getMessage();
}

echo json_encode($response);
?>