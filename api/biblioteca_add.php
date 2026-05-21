<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false];

if (!isset($_POST["usuario_id"], $_POST["titulo"], $_POST["estado"])) {
    echo json_encode($response);
    exit;
}

$usuarioId = (int) $_POST["usuario_id"];
$titulo = $_POST["titulo"];
$saga = $_POST["saga"] ?? null;
$estado = $_POST["estado"];
$opinion = $_POST["opinion"] ?? null;
$valoracion = $_POST["valoracion"] ?? null;

$rutaImagen = null;

//Subida de imagen
if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === 0) {

    $directorio = __DIR__ . "/../Usuarios/uploads/";
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $extension = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
    $nombreArchivo = uniqid("juego_") . "." . $extension;

    if (move_uploaded_file(
        $_FILES["imagen"]["tmp_name"],
        $directorio . $nombreArchivo
    )) {
        $rutaImagen = "/GameNation/Usuarios/uploads/" . $nombreArchivo;
    }
}

try {
    $conexion = db::conexion();

    $sql = "
        INSERT INTO biblioteca
        (usuario_id, titulo, saga, estado, opinion, valoracion, imagen, fecha_agregado)
        VALUES
        (:usuario_id, :titulo, :saga, :estado, :opinion, :valoracion, :imagen, NOW())
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ":usuario_id" => $usuarioId,
        ":titulo" => $titulo,
        ":saga" => $saga,
        ":estado" => $estado,
        ":opinion" => $opinion,
        ":valoracion" => $valoracion,
        ":imagen" => $rutaImagen
    ]);

    $response["success"] = true;

} catch (Exception $e) {
}

echo json_encode($response);