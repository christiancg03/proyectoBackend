<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../Usuarios/config/db.php";

try {
    $conexion = db::conexion();

    $sql = "
        SELECT 
            id,
            titulo,
            contenido,
            imagen,
            autor_id,
            fecha_publicacion
        FROM noticias
        ORDER BY fecha_publicacion DESC
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $noticias
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => "Error al obtener las noticias"
    ]);
}