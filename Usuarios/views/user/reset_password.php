<?php
session_start();
require_once "../../config/db.php";
$conn = db::conexion();

if (!isset($_SESSION["reset_user_id"])) {
    header("Location: recuperar_contra.php");
    exit;
}

$msg = "";
$style = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $pass1 = $_POST["password"] ?? "";
    $pass2 = $_POST["password2"] ?? "";

    if ($pass1 !== $pass2) {
        $msg = "Las contraseñas no coinciden.";
        $style = "alert-danger";
    } elseif (strlen($pass1) < 6) {
        $msg = "La contraseña debe tener al menos 6 caracteres.";
        $style = "alert-warning";
    } else {
        // HASH seguro
        $hash = password_hash($pass1, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE usuarios SET password = :pass WHERE id = :id");
        $stmt->execute([
            ":pass" => $hash,
            ":id"   => $_SESSION["reset_user_id"]
        ]);

        // Eliminar datos temporales
        unset($_SESSION["reset_user_id"]);

        $msg = "Tu contraseña ha sido actualizada. Ya puedes iniciar sesión.";
        $style = "alert-success";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Restablecer contraseña</title>
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
        box-shadow: 0 0 25px #00f0ff55;
    }
</style>
</head>
<body>

<div class="box">
    <h3 class="text-center text-info">Restablecer Contraseña</h3>

    <?php if ($msg): ?>
        <div class="alert <?= $style ?>"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST">
        <label class="form-label">Nueva contraseña:</label>
        <input type="password" name="password" class="form-control mb-3" required>

        <label class="form-label">Confirmar contraseña:</label>
        <input type="password" name="password2" class="form-control mb-3" required>

        <button class="btn btn-info w-100">Guardar nueva contraseña</button>
    </form>

    <div class="text-center mt-3">
        <a href="../../login.php" class="text-info">Volver al inicio de sesión</a>
    </div>
</div>

</body>
</html>