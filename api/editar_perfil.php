<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false];

if (!isset($_POST["usuario_id"])) {
    echo json_encode($response);
    exit;
}

$usuarioId = (int) $_POST["usuario_id"];
$nombre = $_POST["nombre_completo"] ?? null;
$email = $_POST["email"] ?? null;
$fechaNacimiento = $_POST["fecha_nacimiento"] ?? null;
$genero = $_POST["genero"] ?? null;
$pais = $_POST["pais"] ?? null;
$biografia = $_POST["biografia"] ?? null;

$rutaImagen = null;

//Subida Foto Perfil
if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === 0) {

    $directorio = __DIR__ . "/../Usuarios/uploads/";
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
    $nombreArchivo = uniqid("perfil_") . "." . $extension;

    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $directorio . $nombreArchivo)) {
        $rutaImagen = "/GameNation/Usuarios/uploads/" . $nombreArchivo;
    }
}

try {
    $conexion = db::conexion();

    $sql = "
        UPDATE usuarios SET
            nombre_completo = :nombre,
            email = :email,
            fecha_nacimiento = :fecha,
            genero = :genero,
            pais = :pais,
            biografia = :biografia
            " . ($rutaImagen ? ", foto = :foto" : "") . "
        WHERE id = :id
    ";

    $stmt = $conexion->prepare($sql);

    $params = [
        ":nombre" => $nombre,
        ":email" => $email,
        ":fecha" => $fechaNacimiento,
        ":genero" => $genero,
        ":pais" => $pais,
        ":biografia" => $biografia,
        ":id" => $usuarioId
    ];

    if ($rutaImagen) {
        $params[":foto"] = $rutaImagen;
    }

    $stmt->execute($params);

    $response["success"] = true;

} catch (Exception $e) {
}

echo json_encode($response);