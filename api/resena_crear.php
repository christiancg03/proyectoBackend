<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Administracion/config/db.php";

$response = ["success" => false];

try {
    $conexion = db::conexion();

    if (isset($_POST['videojuego'], $_POST['opinion'], $_POST['puntuacion'], $_POST['usuario_id'])) {
        $videojuego = $_POST['videojuego'];
        $opinion = $_POST['opinion'];
        $puntuacion = $_POST['puntuacion'];
        $usuario_id = $_POST['usuario_id'];
        
        $fecha_lanzamiento = $_POST['fecha_lanzamiento'] ?? null;
        $desarrollo = $_POST['desarrollo'] ?? null;
        $produccion = $_POST['produccion'] ?? null;
        $distribucion = $_POST['distribucion'] ?? null;
        $precio = $_POST['precio'] ?? null;
        $jugadores = $_POST['jugadores'] ?? null;
        $formato = $_POST['formato'] ?? null;
        $textos = $_POST['textos'] ?? null;
        $voces = $_POST['voces'] ?? null;
        $online = $_POST['online'] ?? null;
        
        $ruta_imagen = null;

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $nombreArchivo = 'resena_' . uniqid('', true) . '.' . $extension;
            $rutaDestinoFisica = __DIR__ . "/../Administracion/uploads/" . $nombreArchivo;
            
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestinoFisica)) {
                $ruta_imagen = '/GameNation/Administracion/uploads/' . $nombreArchivo;
            }
        }

        $sql = "INSERT INTO resenas (usuario_id, videojuego, opinion, puntuacion, imagen, fecha_lanzamiento, desarrollo, produccion, distribucion, precio, jugadores, formato, textos, voces, online) 
                VALUES (:usuario_id, :videojuego, :opinion, :puntuacion, :imagen, :fecha_lanzamiento, :desarrollo, :produccion, :distribucion, :precio, :jugadores, :formato, :textos, :voces, :online)";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":videojuego", $videojuego);
        $stmt->bindParam(":opinion", $opinion);
        $stmt->bindParam(":puntuacion", $puntuacion);
        $stmt->bindParam(":imagen", $ruta_imagen);
        $stmt->bindParam(":fecha_lanzamiento", $fecha_lanzamiento);
        $stmt->bindParam(":desarrollo", $desarrollo);
        $stmt->bindParam(":produccion", $produccion);
        $stmt->bindParam(":distribucion", $distribucion);
        $stmt->bindParam(":precio", $precio);
        $stmt->bindParam(":jugadores", $jugadores);
        $stmt->bindParam(":formato", $formato);
        $stmt->bindParam(":textos", $textos);
        $stmt->bindParam(":voces", $voces);
        $stmt->bindParam(":online", $online);
        
        if ($stmt->execute()) {
            $response = ["success" => true];
        }
    }
} catch (Exception $e) {
    $response["error"] = $e->getMessage();
}

echo json_encode($response);