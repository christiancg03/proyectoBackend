<?php
require_once __DIR__ . '/../../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}

$conn = db::conexion();
$miId = $_SESSION["usuario"]->id;
$mensajeEnviado = false;
$error = "";

// Envío del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if ($nombre && $email && $mensaje && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("INSERT INTO contacto (nombre, email, mensaje, usuario_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $mensaje, $miId]);
        $mensajeEnviado = true;
    } else {
        $error = "Por favor, rellena todos los campos correctamente.";
    }
}

// Consulta de mensajes enviados por el usuario
$stmt = $conn->prepare("SELECT * FROM contacto WHERE usuario_id = ? ORDER BY fecha DESC");
$stmt->execute([$miId]);
$mensajes = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Contacto - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            background: linear-gradient(to right, #12141e, #252a3a);
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .contenido-contacto {
            background-color: #1d1f2f;
            border-radius: 14px;
            box-shadow: 0 0 18px #00f0ff22;
            padding: 2.5rem 2rem;
            margin: 3rem auto 2rem auto;
            max-width: 600px;
        }
        .titulo-contenido {
            color: #00f0ff;
            text-shadow: 0 0 5px #00f0ff44;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .icono-contenido {
            color: #00f0ff;
            font-size: 2.2rem;
            margin-bottom: 1.2rem;
            display: block;
            text-align: center;
        }
        .form-table {
            margin: 0 auto;
            width: 90%;
            border-collapse: separate;
            border-spacing: 0 1.1rem;
        }
        .form-table td {
            vertical-align: middle;
        }
        .form-label {
            color: #7ae4ff;
            font-weight: 600;
            text-align: right;
            padding-right: 1.5rem;
            width: 140px;
            white-space: nowrap;
        }
        .form-input {
            width: 90%;
            min-width: 180px;
            max-width: 350px;
            margin-right: 0.5rem;
        }
        .form-control, textarea.form-control {
            background: #23263a;
            color: #f1f1f1;
            border: 1px solid #444;
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            font-size: 1rem;
            resize: vertical;
        }
        .form-control:focus, textarea.form-control:focus {
            border-color: #00f0ff;
            box-shadow: 0 0 8px #00f0ff55;
            outline: none;
        }
        .btn-info {
            background-color: #00f0ff;
            color: #1d1f2f;
            border: none;
            font-weight: bold;
            border-radius: 8px;
            transition: background 0.3s, color 0.3s;
            padding: 0.5rem 1.2rem;
            font-size: 1rem;
            width: auto;
            margin-top: 0.5rem;
            float: left;
        }
        .btn-info:hover {
            background-color: #00c2d6;
            color: #fff;
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
        .alerta-exito, .alerta-error {
            border-radius: 8px;
            margin-top: 1rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            font-size: 1.05rem;
            clear: both;
        }
        /* Bloques de mensajes */
        .msj-usuario {
            background: #23263a;
            border-radius: 10px;
            box-shadow: 0 0 10px #00f0ff22;
            padding: 1.2rem 1rem;
            margin-bottom: 1.7rem;
        }
        .respuesta-admin {
            background: #16202c;
            border-left: 4px solid #00f0ff;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 0.7rem;
            color: #7ae4ff;
        }
        .sinrespuesta-admin-block {
            background: #292c3e;
            border-radius: 8px;
            padding: 0.7rem 1rem;
            margin-top: 0.7rem;
            color: #ffc107;
        }
        @media (max-width: 700px) {
            .contenido-contacto { padding: 1.2rem 0.5rem; }
            .form-table { width: 100%; }
            .form-label { text-align: left; padding-right: 0.5rem; width: auto; }
            .form-input { width: 100%; max-width: 100%; }
            .btn-volver { margin-bottom: 1rem; }
        }
    </style>
</head>
<body>
    <div class="contenido-contacto">
        <a href="../../index.php" class="btn-volver">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
        <div class="icono-contenido">
            <i class="fa-solid fa-envelope-circle-check"></i>
        </div>
        <h2 class="titulo-contenido">Contacto con el Administrador</h2>
        <p class="text-center mb-4">
            ¿Tienes alguna duda, sugerencia o problema? Completa el siguiente formulario y nuestro administrador te responderá lo antes posible.
        </p>
        <?php if ($mensajeEnviado): ?>
            <div class="alert alerta-exito">
                <i class="fa-solid fa-circle-check"></i> ¡Tu mensaje ha sido enviado correctamente! El administrador se pondrá en contacto contigo si es necesario.
            </div>
        <?php endif; ?>

        <?php if (!$mensajeEnviado): ?>
            <?php if ($error): ?>
                <div class="alert alerta-error">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="contacto.php" autocomplete="off">
                <table class="form-table">
                    <tr>
                        <td class="form-label"><label for="nombre">Nombre</label></td>
                        <td><input type="text" class="form-control form-input" id="nombre" name="nombre" readonly
                            value="<?= htmlspecialchars($_SESSION['usuario']->nombre_completo ?? '') ?>" required /></td>
                    </tr>
                    <tr>
                        <td class="form-label"><label for="email">Correo electrónico</label></td>
                        <td><input type="email" class="form-control form-input" id="email" name="email" readonly
                            value="<?= htmlspecialchars($_SESSION['usuario']->email ?? '') ?>" required /></td>
                    </tr>
                    <tr>
                        <td class="form-label" style="vertical-align: top;"><label for="mensaje">Mensaje</label></td>
                        <td><textarea class="form-control form-input" id="mensaje" name="mensaje" rows="5" required></textarea></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <button type="submit" class="btn btn-info">
                                <i class="fa-solid fa-paper-plane"></i> Enviar mensaje
                            </button>
                        </td>
                    </tr>
                </table>
            </form>
        <?php endif; ?>

        <!-- HISTORIAL DE MENSAJES Y RESPUESTAS -->
        <?php if (!empty($mensajes)): ?>
            <h4 class="mt-5 mb-3" style="color:#00f0ff;">Tus mensajes enviados</h4>
            <?php foreach ($mensajes as $msg): ?>
                <div class="msj-usuario">
                    <div>
                        <strong>Tu mensaje:</strong><br>
                        <?= nl2br(htmlspecialchars($msg->mensaje)) ?>
                    </div>
                    <div class="text-muted small mb-2"><?= $msg->fecha ?></div>
                    <?php if ($msg->respuesta): ?>
                        <div class="respuesta-admin">
                            <strong>Respuesta del administrador:</strong><br>
                            <?= nl2br(htmlspecialchars($msg->respuesta)) ?>
                            <div class="text-muted small mt-1"><?= $msg->fecha_respuesta ?></div>
                        </div>
                    <?php else: ?>
                        <div class="sinrespuesta-admin-block">
                            <i class="fa-solid fa-hourglass-half"></i> Aún no hay respuesta del administrador.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>