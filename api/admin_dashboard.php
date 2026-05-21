<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../Administracion/config/db.php";

$response = [
    "success" => false
];

try {

    $conexion = db::conexion();

    // Total usuarios normales
    $sqlUsuarios = "
        SELECT COUNT(*) as total
        FROM usuarios
        WHERE rol = 'usuario'
    ";

    $stmtUsuarios = $conexion->prepare($sqlUsuarios);
    $stmtUsuarios->execute();

    $totalUsuarios = $stmtUsuarios->fetch(PDO::FETCH_ASSOC)["total"] ?? 0;

    // Total reseñas
    $sqlResenas = "
        SELECT COUNT(*) as total
        FROM resenas
    ";

    $stmtResenas = $conexion->prepare($sqlResenas);
    $stmtResenas->execute();

    $totalResenas = $stmtResenas->fetch(PDO::FETCH_ASSOC)["total"] ?? 0;

    // Total noticias
    $sqlNoticias = "
        SELECT COUNT(*) as total
        FROM noticias
    ";

    $stmtNoticias = $conexion->prepare($sqlNoticias);
    $stmtNoticias->execute();

    $totalNoticias = $stmtNoticias->fetch(PDO::FETCH_ASSOC)["total"] ?? 0;

    // Mensajes sin leer
    $sqlMensajes = "
        SELECT COUNT(*) as total
        FROM contacto
        WHERE leido = 0
    ";

    $stmtMensajes = $conexion->prepare($sqlMensajes);
    $stmtMensajes->execute();

    $mensajesSinLeer = $stmtMensajes->fetch(PDO::FETCH_ASSOC)["total"] ?? 0;

    $response = [
        "success" => true,
        "totalUsuarios" => (int)$totalUsuarios,
        "totalResenas" => (int)$totalResenas,
        "totalNoticias" => (int)$totalNoticias,
        "mensajesSinLeer" => (int)$mensajesSinLeer
    ];

} catch (Exception $e) {

    $response = [
        "success" => false
    ];

}

echo json_encode($response);