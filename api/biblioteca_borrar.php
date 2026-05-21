<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false];

if (!isset($_POST["id"])) {
    echo json_encode($response);
    exit;
}

$id = (int) $_POST["id"];

try {
    $conexion = db::conexion();

    $stmt = $conexion->prepare(
        "DELETE FROM biblioteca WHERE id = :id"
    );
    $stmt->execute([":id" => $id]);

    if ($stmt->rowCount() > 0) {
        $response["success"] = true;
    }

} catch (Exception $e) {}

echo json_encode($response);