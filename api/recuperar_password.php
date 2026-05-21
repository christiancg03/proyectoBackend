<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $response["message"] = "Método no permitido";
    echo json_encode($response);
    exit;
}

$action = $_POST["action"] ?? "";

try {
    $conexion = db::conexion();

    //1: Comprobar si el correo existe
    if ($action === "comprobar") {
        $email = trim($_POST["email"] ?? "");

        if (empty($email)) {
            $response["message"] = "El correo electrónico es obligatorio";
            echo json_encode($response);
            exit;
        }

        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute([":email" => $email]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if ($user) {
            $response["success"] = true;
            $response["message"] = "Usuario verificado";
            $response["user_id"] = (int)$user->id; 
        } else {
            $response["message"] = "No existe ninguna cuenta con ese email.";
        }
    } 
    // 2: Aplicar la nueva contraseña encriptada
    elseif ($action === "modificar") {
        $userId = $_POST["user_id"] ?? "";
        $password_plain = trim($_POST["password"] ?? "");

        if (empty($userId) || empty($password_plain)) {
            $response["message"] = "Datos insuficientes para el cambio de contraseña";
            echo json_encode($response);
            exit;
        }

        // Encriptar la contraseña con el estándar del sistema
        $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

        $stmt = $conexion->prepare("UPDATE usuarios SET password = :password WHERE id = :id");
        $exito = $stmt->execute([
            ":password" => $password_hashed,
            ":id" => $userId
        ]);

        if ($exito) {
            $response["success"] = true;
            $response["message"] = "Nueva contraseña generada correctamente";
        } else {
            $response["message"] = "Error al actualizar la contraseña en el sistema";
        }
    } else {
        $response["message"] = "Acción no definida o inválida";
    }

} catch (PDOException $e) {
    $response["message"] = "Error de base de datos: " . $e->getMessage();
}

echo json_encode($response);
?>