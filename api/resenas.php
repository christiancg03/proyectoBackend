<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false, "data" => []];

try {
    $conexion = db::conexion();

    $sql = "
        SELECT 
            r.id,
            r.usuario_id,
            r.videojuego,
            r.opinion,
            r.puntuacion,
            r.fecha_publicacion,
            r.imagen,
            r.fecha_lanzamiento,
            r.desarrollo,
            r.produccion,
            r.distribucion,
            r.precio,
            r.jugadores,
            r.formato,
            r.textos,
            r.voces,
            r.online,
            u.usuario
        FROM resenas r
        JOIN usuarios u ON r.usuario_id = u.id
        ORDER BY r.fecha_publicacion DESC
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    $response["success"] = true;
    $response["data"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
}

echo json_encode($response);