<?php
require_once "config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"]);
    $email = trim($_POST["email"]);
    $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);
    $rol = 'usuario';

    // Ruta por defecto si no sube imagen
    $foto = "/GameNation/assets/img/default-user.png";

    if (!empty($_FILES["foto"]["name"])) {
        $nombreArchivo = uniqid("perfil_") . "." . pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);

        // Ruta del sistema para mover el archivo (carpeta dentro de Usuarios/)
        $rutaFisica = __DIR__ . "/uploads/";
        if (!is_dir($rutaFisica)) {
            mkdir($rutaFisica, 0755, true); // Crea el directorio si no existe
        }

        $rutaCompleta = $rutaFisica . $nombreArchivo;
        $rutaWeb = "/GameNation/Usuarios/uploads/" . $nombreArchivo;

        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $rutaCompleta)) {
            $foto = $rutaWeb;
        }
    }

    $conn = db::conexion();

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico no es válido.";
    } elseif (strlen($usuario) < 3) {
        $error = "El nombre de usuario debe tener al menos 3 caracteres.";
    } elseif (strlen($_POST["password"]) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
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
            $error = "Error al crear la cuenta: " . $e->getMessage();
        }
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      background: linear-gradient(120deg, #0d0f1c 0%, #202437 100%);
      min-height: 100vh;
      color: #f1f1f1;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .register-container {
      background: #1c1f2e;
      border-radius: 16px;
      box-shadow: 0 0 24px #00f0ff33;
      padding: 2.5rem 2rem 2rem 2rem;
      max-width: 420px;
      width: 100%;
      margin: 40px auto;
    }
    .register-container h2 {
      color: #00f0ff;
      text-shadow: 0 0 6px #00f0ff88;
      font-weight: bold;
      margin-bottom: 1.5rem;
      text-align: center;
    }
    .form-label {
      color: #00f0ff;
      font-weight: 500;
    }
    .form-control, .form-control:focus {
      border-radius: 8px;
      background: #23263a;
      color: #f1f1f1;
      border: 1.5px solid #00f0ff33;
      box-shadow: none;
    }
    .form-control:focus {
      border-color: #00f0ff;
      box-shadow: 0 0 8px #00f0ff55;
    }
    .btn-primary {
      background-color: #00f0ff;
      border: none;
      color: #181c2f;
      font-weight: bold;
      border-radius: 10px;
      padding: 12px 0;
      margin-top: 10px;
      transition: background 0.2s;
      font-size: 1.11rem;
      letter-spacing: 1px;
    }
    .btn-primary:hover {
      background-color: #00c0cc;
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
    .profile-preview {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 1.1rem;
    }
    .profile-preview img {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      object-fit: cover;
      box-shadow: 0 0 10px #00f0ff55;
      margin-bottom: 0.3rem;
      border: 2px solid #00f0ff55;
      background: #23263a;
    }
    .register-link {
      color: #00f0ff;
      text-align: center;
      display: block;
      margin-top: 1.2rem;
      font-size: 1rem;
    }
    .register-link a {
      color: #00f0ff;
      text-decoration: underline;
      transition: color 0.2s;
    }
    .register-link a:hover {
      color: #fff;
    }
  </style>
</head>
<body>
<div class="register-container">
  <h2><i class="fa-solid fa-user-plus"></i> Crear Cuenta</h2>
  <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
  <form method="POST" enctype="multipart/form-data" autocomplete="off">
    <div class="mb-3">
        <label for="usuario" class="form-label">Nombre de usuario</label>
        <input type="text" class="form-control" id="usuario" name="usuario" maxlength="20" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="email" name="email" maxlength="50" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" minlength="6" required>
    </div>
    <div class="mb-3">
        <label for="foto" class="form-label">Foto de perfil</label>
        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
    </div>
    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-user-plus"></i> Registrarse</button>
  </form>
  <div class="register-link">
    ¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a>
  </div>
</div>
<script>
  // Previsualización de imagen de perfil
  document.getElementById('foto').addEventListener('change', function(e) {
    const [file] = e.target.files;
    if (file) {
      document.getElementById('imgPreview').src = URL.createObjectURL(file);
    } else {
      document.getElementById('imgPreview').src = "/GameNation/assets/img/default-user.png";
    }
  });
</script>
</body>
</html>
