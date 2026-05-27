<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false, "data" => []];

try {
    $conexion = db::conexion();

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = (int) $_GET['id'];
        
        $sql = "
            SELECT 
                id,
                titulo,
                contenido,
                imagen,
                autor_id,
                fecha_publicacion 
            FROM noticias 
            WHERE id = :id
        ";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $response["data"] = $resultado ? [$resultado] : [];
        $response["success"] = true;
        
    } else {
        $sql = "
            SELECT 
                id,
                titulo,
                contenido,
                imagen,
                autor_id,
                fecha_publicacion
            FROM noticias
            ORDER BY fecha_publicacion DESC
        ";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        
        $response["data"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response["success"] = true;
    }

} catch (Exception $e) {
    $response["message"] = "Error al obtener las noticias";
}

echo json_encode($response);
?>