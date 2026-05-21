<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";
require_once __DIR__ . "/../Usuarios/models/userModel.php";

$response = ["success" => false];

if (!isset($_POST["usuario"], $_POST["password"])) {
    echo json_encode($response);
    exit;
}

$usuario = trim($_POST["usuario"]);
$password = trim($_POST["password"]);

$userModel = new userModel();
$user = $userModel->login($usuario, $password);

if ($user) {
    $response = [
        "success" => true,
        "id" => $user->id,
        "usuario" => $user->usuario,
        "email" => $user->email ?? "",
        "foto" => $user->foto ?? "",
        "nombre_completo" => $user->nombre_completo ?? "",
        "fecha_nacimiento" => $user->fecha_nacimiento ?? "",
        "genero" => $user->genero ?? "",
        "pais" => $user->pais ?? "",
        "biografia" => $user->biografia ?? "",
        "rol" => $user->rol ?? ""
    ];
}

echo json_encode($response);