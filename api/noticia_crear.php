<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Administracion/config/db.php";

$response = ["success" => false];

try {
    $conexion = db::conexion();

    if (isset($_POST['titulo'], $_POST['contenido'], $_POST['autor_id'])) {
        $titulo = $_POST['titulo'];
        $contenido = $_POST['contenido'];
        $autor_id = $_POST['autor_id'];
        
        $ruta_imagen = null;

        // Manejo de la subida de imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $nombreArchivo = 'noticia_' . uniqid('', true) . '.' . $extension;
            $rutaDestinoFisica = __DIR__ . "/../Administracion/uploads/" . $nombreArchivo;
            
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestinoFisica)) {
                $ruta_imagen = '/GameNation/Administracion/uploads/' . $nombreArchivo;
            }
        }

        $sql = "INSERT INTO noticias (titulo, contenido, imagen, autor_id, fecha_publicacion) 
                VALUES (:titulo, :contenido, :imagen, :autor_id, CURRENT_TIMESTAMP)";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":contenido", $contenido);
        $stmt->bindParam(":imagen", $ruta_imagen);
        $stmt->bindParam(":autor_id", $autor_id);
        
        if ($stmt->execute()) {
            $response = ["success" => true];
        }
    }
} catch (Exception $e) {
    $response["error"] = $e->getMessage();
}

echo json_encode($response);