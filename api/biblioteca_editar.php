<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false];

if (!isset($_POST["id"], $_POST["titulo"], $_POST["estado"])) {
    echo json_encode($response);
    exit;
}

$id = (int) $_POST["id"];
$titulo = trim($_POST["titulo"]);
$saga = $_POST["saga"] ?? null;
$estado = $_POST["estado"];
$opinion = $_POST["opinion"] ?? null;
$valoracion = $_POST["valoracion"] ?? null;

try {
    $conn = db::conexion();

    $imagenSql = "";
    $params = [
        ":id" => $id,
        ":titulo" => $titulo,
        ":saga" => $saga,
        ":estado" => $estado,
        ":opinion" => $opinion,
        ":valoracion" => $valoracion
    ];

    // Imagen nueva (opcional)
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === 0) {
        $ruta = "/GameNation/Usuarios/uploads/";
        $nombre = uniqid() . "_" . basename($_FILES["imagen"]["name"]);
        $destino = $_SERVER["DOCUMENT_ROOT"] . $ruta . $nombre;

        move_uploaded_file($_FILES["imagen"]["tmp_name"], $destino);

        $imagenSql = ", imagen = :imagen";
        $params[":imagen"] = $ruta . $nombre;
    }

    $sql = $conn->prepare("
        UPDATE biblioteca
        SET titulo = :titulo,
            saga = :saga,
            estado = :estado,
            opinion = :opinion,
            valoracion = :valoracion
            $imagenSql
        WHERE id = :id
    ");

    $ok = $sql->execute($params);

    $response["success"] = $ok;

} catch (Exception $e) {
}

echo json_encode($response);