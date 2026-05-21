<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Administracion/config/db.php";

$response = ["success" => false, "data" => [], "message" => ""];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $noticia_id = isset($_GET['noticia_id']) ? (int)$_GET['noticia_id'] : 0;

    if ($noticia_id <= 0) {
        $response["message"] = "ID de noticia inválido o no suministrado";
        echo json_encode($response);
        exit;
    }

    try {
        $conn = db::conexion();
        
        // Seleccionamos los datos de los comentarios y cruzamos con los usuarios
        $sql = "SELECT c.id, c.noticia_id, c.usuario_id, c.contenido, c.fecha, c.respuesta_a_id,
                       u.usuario AS nombre_usuario, u.foto AS foto_usuario,
                       r.contenido AS respuesta_contenido, ur.usuario AS respuesta_usuario
                FROM comentarios c
                LEFT JOIN usuarios u ON c.usuario_id = u.id
                LEFT JOIN comentarios r ON c.respuesta_a_id = r.id
                LEFT JOIN usuarios ur ON r.usuario_id = ur.id
                WHERE c.noticia_id = :noticia_id
                ORDER BY c.fecha DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute([":noticia_id" => $noticia_id]);
        $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convertimos los tipos de datos ID a enteros requeridos por Kotlin
        foreach ($comentarios as &$com) {
            $com['id'] = (int)$com['id'];
            $com['noticia_id'] = (int)$com['noticia_id'];
            $com['usuario_id'] = (int)$com['usuario_id'];
            $com['respuesta_a_id'] = $com['respuesta_a_id'] !== null ? (int)$com['respuesta_a_id'] : null;
        }

        $response["success"] = true;
        $response["data"] = $comentarios;

    } catch (PDOException $e) {
        $response["message"] = "Error en el servidor: " . $e->getMessage();
    }
} else {
    $response["message"] = "Método HTTP no soportado";
}

echo json_encode($response);