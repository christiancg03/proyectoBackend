<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Administracion/config/db.php";

$response = ["success" => false];

try {
    $conexion = db::conexion();

    if (isset($_POST['id'], $_POST['titulo'], $_POST['contenido'])) {
        $id = $_POST['id'];
        $titulo = $_POST['titulo'];
        $contenido = $_POST['contenido'];
        
        $ruta_imagen = null;

        // Comprobamos si hay nueva imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $nombreArchivo = 'noticia_' . uniqid('', true) . '.' . $extension;
            $rutaDestinoFisica = __DIR__ . "/../Administracion/uploads/" . $nombreArchivo;
            
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestinoFisica)) {
                $ruta_imagen = '/GameNation/Administracion/uploads/' . $nombreArchivo;
            }
        }

        if ($ruta_imagen) {
            $sql = "UPDATE noticias SET titulo = :titulo, contenido = :contenido, imagen = :imagen WHERE id = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(":imagen", $ruta_imagen);
        } else {
            $sql = "UPDATE noticias SET titulo = :titulo, contenido = :contenido WHERE id = :id";
            $stmt = $conexion->prepare($sql);
        }

        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":contenido", $contenido);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            $response = ["success" => true];
        }
    }
} catch (Exception $e) {
    $response["error"] = $e->getMessage();
}

echo json_encode($response);