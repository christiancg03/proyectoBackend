<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$conn = db::conexion();

$usuario_id = $_POST["usuario_id"] ?? null;
$mensaje = trim($_POST["mensaje"] ?? "");

if (!$usuario_id || $mensaje === "") {
    echo json_encode(["success" => false]);
    exit;
}

/* Obtener nombre y email del usuario */
$sqlUser = $conn->prepare("
    SELECT nombre_completo, email 
    FROM usuarios 
    WHERE id = :id
");
$sqlUser->execute([":id" => $usuario_id]);
$usuario = $sqlUser->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo json_encode(["success" => false]);
    exit;
}

/* Insertar mensaje de contacto */
$sql = $conn->prepare("
    INSERT INTO contacto (
        usuario_id, 
        nombre, 
        email, 
        mensaje, 
        fecha, 
        leido
    )
    VALUES (
        :usuario_id,
        :nombre,
        :email,
        :mensaje,
        NOW(),
        0
    )
");

$ok = $sql->execute([
    ":usuario_id" => $usuario_id,
    ":nombre" => $usuario["nombre_completo"],
    ":email" => $usuario["email"],
    ":mensaje" => $mensaje
]);

echo json_encode([
    "success" => $ok
]);