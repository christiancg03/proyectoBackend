<?php
require_once "config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"]);
    $email = trim($_POST["email"]);
    $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);
    $rol = 'usuario'; // Por defecto, todos son "usuario"

    // Procesar imagen de perfil
    $foto = "/GameNation/assets/img/default-user.png";
    if (!empty($_FILES["foto"]["name"])) {
        $nombreArchivo = uniqid("perfil_") . "." . pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $rutaFisica = __DIR__ . "/uploads/";
        if (!is_dir($rutaFisica)) {
            mkdir($rutaFisica, 0755, true);
        }
        $rutaCompleta = $rutaFisica . $nombreArchivo;
        $rutaWeb = "/GameNation/Administracion/uploads/" . $nombreArchivo;
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $rutaCompleta)) {
            $foto = $rutaWeb;
        }
    }

    // Evita que los usuarios puedan registrarse como admin o moderador
    if (isset($_POST["rol"]) && in_array($_POST["rol"], ['admin', 'moderador'])) {
        $rol = $_POST["rol"];
    }

    $conn = db::conexion();
    try {
        $stmt = $conn->prepare("INSERT INTO usuarios (usuario, email, password, rol, foto) 
                                VALUES (:usuario, :email, :password, :rol, :foto)");
        $stmt->execute([
            ":usuario" => $usuario,
            ":email" => $email,
            ":password" => $password,
            ":rol" => $rol,
            ":foto" => $foto
        ]);
        header("Location: login.php");
        exit();
    } catch (Exception $e) {
        echo "Error al crear la cuenta: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #0d0f1c, #202437);
      color: #f1f1f1;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .container {
      max-width: 500px;
      margin-top: 50px;
      background: #1c1f2e;
      border-radius: 12px;
      box-shadow: 0 0 18px #00f0ff33;
      padding: 2.5rem 2rem;
    }
    h2 {
      color: #00f0ff;
      text-align: center;
      margin-bottom: 1.5rem;
      text-shadow: 0 0 6px #00f0ff88;
    }
    .form-label {
      color: #00f0ff;
    }
    .boton-registro {
      background-color: #00f0ff;
      border: none;
      color: #181c2f;
      font-weight: bold;
      border-radius: 8px;
      padding: 10px 0;
      margin-top: 10px;
      transition: background 0.2s;
    }
    .boton-registro:hover {
      background-color: #00c0cc;
      color: #fff;
    }
    .form-control, .form-select {
      border-radius: 8px;
      background: #23263a;
      color: #f1f1f1;
      border: 1px solid #00f0ff33;
    }
    .form-control:focus, .form-select:focus {
      border-color: #00f0ff;
      box-shadow: 0 0 8px #00f0ff55;
      background: #23263a;
      color: #fff;
    }
    .alert-danger {
      border-radius: 8px;
      background: #dc3545cc;
      color: #fff;
      border: none;
      margin-bottom: 1rem;
      text-align: center;
    }
  </style>
</head>
<body class="bg-light">
<div class="container">
  <h2>Crear Cuenta</h2>
  <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="usuario" class="form-label">Nombre de usuario</label>
        <input type="text" class="form-control" id="usuario" name="usuario" required maxlength="20">
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="email" name="email" required maxlength="50">
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" required minlength="6">
    </div>

    <div class="mb-3">
        <label for="rol" class="form-label">Rol</label>
        <select name="rol" id="rol" class="form-select">
            <option value="moderador">Moderador</option>
            <option value="admin">Administrador</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="foto" class="form-label">Foto de perfil</label>
        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
    </div>

    <button type="submit" class="btn boton-registro w-100">Registrarse</button>
  </form>
</div>
</body>
</html>
