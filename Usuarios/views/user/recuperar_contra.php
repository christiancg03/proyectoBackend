<?php
session_start();
require_once "../../config/db.php";
$conn = db::conexion();

$msg = "";
$style = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"] ?? "";

    // Comprobar si existe el usuario
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->execute([":email" => $email]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if ($user) {
        // Guardar el ID en sesión para usarlo en reset_password.php
        $_SESSION["reset_user_id"] = $user->id;
        header("Location: reset_password.php");
        exit;
    } else {
        $msg = "No existe ninguna cuenta con ese email.";
        $style = "alert-danger";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Recuperar Contraseña</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background: linear-gradient(135deg, #0d0f1c 0%, #23263a 100%);
        color: white;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', sans-serif;
    }
    .box {
        background: #1c1f2e;
        padding: 2rem;
        border-radius: 15px;
        width: 420px;
        box-shadow: 0 0 30px #00f0ff44;
        }
        h2 {
            color: #00f0ff;
            text-align: center;
            margin-bottom: 1.5rem;
        }
    .btn-volver {
            background-color: transparent;
            border: 1px solid #00f0ff;
            color: #00f0ff;
            border-radius: 8px;
            padding: 0.4rem 1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 1.5rem;
            transition: background-color 0.3s, color 0.3s;
        }
        .btn-volver:hover {
            background-color: #00f0ff;
            color: #1d1f2f;
            text-decoration: none;
        }
        .btn-enviar {
            width: 100%;
            background: #00f0ff;
            color: #0d0f1c;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            padding: 0.8rem;
            transition: 0.3s;
        }
        .btn-enviar:hover {
            background: #00c7e6;
            box-shadow: 0 0 10px #00f0ff77;
        }
        label {
            color: #ccc;
        }
</style>
</head>
<body>

<div class="box">
    <a href="../../login.php" class="btn-volver">
            <i class="fa-solid fa-arrow-left"></i> Volver
    </a>
    <h2><i class="fa-solid fa-key"></i> Recuperar contraseña</h2>

    <?php if ($msg): ?>
        <div class="alert <?= $style ?>"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST">
        <label class="form-label">Introduce tu correo electrónico:</label>
        <input type="email" name="email" class="form-control mb-3" required>

        <button class="btn btn-enviar w-100">Continuar</button>
    </form>
</div>

</body>
</html>
