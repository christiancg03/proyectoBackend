<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Administracion/config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $conexion = db::conexion();
    
    try {
        // 1. Limpiar amigos
        $delAmigos = $conexion->prepare("DELETE FROM amigos WHERE usuario_id = :id OR amigo_id = :id");
        $delAmigos->execute([":id" => $id]);
        
        // 2. Limpiar biblioteca
        $delBiblioteca = $conexion->prepare("DELETE FROM biblioteca WHERE usuario_id = :id");
        $delBiblioteca->execute([":id" => $id]);

        // 3. Limpiar mensajes
        $delMensajes = $conexion->prepare("DELETE FROM mensajes WHERE emisor_id = :id OR receptor_id = :id");
        $delMensajes->execute([":id" => $id]);

        // 4. Limpiar comentarios
        $delComentarios = $conexion->prepare("DELETE FROM comentarios WHERE usuario_id = :id");
        $delComentarios->execute([":id" => $id]);

        // 5. Limpiar registros de contacto
        $delContacto = $conexion->prepare("DELETE FROM contacto WHERE usuario_id = :id");
        $delContacto->execute([":id" => $id]);

        // 6. Por último, borrar al usuario si tiene rol 'usuario'
        $sql = "DELETE FROM usuarios WHERE id = :id AND rol = 'usuario'";
        $stmt = $conexion->prepare($sql);
        $result = $stmt->execute([':id' => $id]);

        echo json_encode(["success" => $result]);

    } catch (PDOException $e) {
        // Si algo falla en la base de datos, capturamos el error y respondemos con JSON
        echo json_encode([
            "success" => false, 
            "message" => "Error al eliminar el usuario en la base de datos.",
            "error" => $e->getMessage()
        ]);
    }
} else {
    echo json_encode(["success" => false, "message" => "ID no proporcionado"]);
}