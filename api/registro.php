<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../Usuarios/config/db.php";

$response = ["success" => false, "message" => ""];

if (!isset($_POST["usuario"]) || !isset($_POST["email"]) || !isset($_POST["password"])) {
    $response["message"] = "Faltan campos obligatorios";
    echo json_encode($response);
    exit;
}

$usuario = trim($_POST["usuario"]);
$email = trim($_POST["email"]);
$password_plain = trim($_POST["password"]);

if (empty($usuario) || empty($email) || empty($password_plain)) {
    $response["message"] = "Los campos no pueden estar vacíos";
    echo json_encode($response);
    exit;
}

try {
    $conexion = db::conexion();

    // 1. Comprobar si el usuario o email ya existen
    $sql_check = "SELECT id FROM usuarios WHERE usuario = :usuario OR email = :email";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([":usuario" => $usuario, ":email" => $email]);

    if ($stmt_check->rowCount() > 0) {
        $response["message"] = "El nombre de usuario o el email ya están registrados.";
        echo json_encode($response);
        exit;
    }

    // 2. Encriptar contraseña
    $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

    // 3. Procesar imagen de perfil si se subió
    $foto_ruta = null;
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . "/../Usuarios/uploads/";
        
        // Crear carpeta si no existe
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $tmp_name = $_FILES["foto"]["tmp_name"];
        $extension = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
        
        $valid_extensions = ["jpg", "jpeg", "png", "gif", "webp"];
        if (in_array($extension, $valid_extensions)) {
            $nuevo_nombre = "perfil_" . uniqid() . "." . $extension;
            $destino = $upload_dir . $nuevo_nombre;

            if (move_uploaded_file($tmp_name, $destino)) {
                $foto_ruta = "/GameNation/Usuarios/uploads/" . $nuevo_nombre;
            }
        }
    }

    // Si no hay foto subida, usar la de por defecto
    if ($foto_ruta == null) {
        $foto_ruta = "/GameNation/assets/img/default-user.png";
    }

    // 4. Insertar nuevo usuario
    $sql_insert = "
        INSERT INTO usuarios (usuario, email, password, foto, rol, fecha_registro) 
        VALUES (:usuario, :email, :password, :foto, 'usuario', NOW())
    ";
    
    $stmt_insert = $conexion->prepare($sql_insert);
    $exito = $stmt_insert->execute([
        ":usuario" => $usuario,
        ":email" => $email,
        ":password" => $password_hashed,
        ":foto" => $foto_ruta
    ]);

    if ($exito) {
        $response["success"] = true;
        $response["message"] = "Usuario registrado correctamente";
    } else {
        $response["message"] = "Error al insertar en la base de datos";
    }

} catch (PDOException $e) {
    $response["message"] = "Error de base de datos: " . $e->getMessage();
} catch (Exception $e) {
    $response["message"] = "Error inesperado: " . $e->getMessage();
}

echo json_encode($response);
?>