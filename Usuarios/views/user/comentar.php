<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}

$conn = db::conexion();
$usuario_id = $_SESSION["usuario"]->id;
$noticia_id = $_POST["noticia_id"];
$contenido = trim($_POST["contenido"]);

if ($contenido !== "") {
    $stmt = $conn->prepare("INSERT INTO comentarios (noticia_id, usuario_id, contenido) 
                            VALUES (:noticia_id, :usuario_id, :contenido)");
    $stmt->execute([
        ":noticia_id" => $noticia_id,
        ":usuario_id" => $usuario_id,
        ":contenido" => $contenido
    ]);
}

header("Location: noticias.php");
exit();
