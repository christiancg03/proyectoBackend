<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Administracion/config/db.php";

$conexion = db::conexion();
// Solo usuarios con rol 'usuario'
$sql = "SELECT id, usuario, email, foto, rol FROM usuarios WHERE rol = 'usuario' ORDER BY usuario ASC";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["success" => true, "usuarios" => $usuarios]);