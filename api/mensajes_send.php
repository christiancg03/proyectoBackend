<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$conn = db::conexion();

$emisor = $_POST["emisor_id"] ?? null;
$receptor = $_POST["receptor_id"] ?? null;
$contenido = $_POST["contenido"] ?? null;

if (!$emisor || !$receptor || !$contenido) {
    echo json_encode(["success" => false]);
    exit;
}

$sql = $conn->prepare("
    INSERT INTO mensajes (emisor_id, receptor_id, contenido, enviado_en)
    VALUES (:e, :r, :c, NOW())
");

$result = $sql->execute([
    ":e" => $emisor,
    ":r" => $receptor,
    ":c" => $contenido
]);

echo json_encode(["success" => $result]);