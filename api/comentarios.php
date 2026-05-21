<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false, "data" => []];

if (!isset($_GET["noticia_id"])) {
    echo json_encode(["success" => false, "message" => "noticia_id es requerido"]);
    exit;
}

$noticiaId = (int) $_GET["noticia_id"];

try {
    $conexion = db::conexion();

    $sql = "
        SELECT c.id, c.noticia_id, c.usuario_id, c.contenido, c.fecha, c.respuesta_a_id,
               u.usuario as nombre_usuario, u.foto as foto_usuario,
               cp.contenido as respuesta_contenido, up.usuario as respuesta_usuario
        FROM comentarios c
        JOIN usuarios u ON c.usuario_id = u.id
        LEFT JOIN comentarios cp ON c.respuesta_a_id = cp.id
        LEFT JOIN usuarios up ON cp.usuario_id = up.id
        WHERE c.noticia_id = :noticia_id
        ORDER BY c.fecha ASC
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([":noticia_id" => $noticiaId]);

    $response["success"] = true;
    $response["data"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $response["message"] = "Error al obtener los comentarios";
}

echo json_encode($response);
?>