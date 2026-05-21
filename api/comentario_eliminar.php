<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Administracion/config/db.php";

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id <= 0) {
        $response["message"] = "Falta el identificador del comentario a eliminar";
        echo json_encode($response);
        exit;
    }

    try {
        $conn = db::conexion();
        
        // Primero, por integridad referencial, eliminamos sub-respuestas
        $stmtHijos = $conn->prepare("UPDATE comentarios SET respuesta_a_id = NULL WHERE respuesta_a_id = :id");
        $stmtHijos->execute([":id" => $id]);

        // Eliminamos el comentario principal
        $stmt = $conn->prepare("DELETE FROM comentarios WHERE id = :id");
        $exito = $stmt->execute([":id" => $id]);

        if ($exito && $stmt->rowCount() > 0) {
            $response["success"] = true;
            $response["message"] = "Comentario eliminado de manera satisfactoria";
        } else {
            $response["message"] = "El comentario no existe o ya fue eliminado";
        }

    } catch (PDOException $e) {
        $response["message"] = "Error de base de datos: " . $e->getMessage();
    }
} else {
    $response["message"] = "Método no permitido";
}

echo json_encode($response);