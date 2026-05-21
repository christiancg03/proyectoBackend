<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]->rol !== "admin") {
    header("Location: /GameNation/Administracion/principal.php");
    exit();
}

$conn = db::conexion();

// Marcar como leído/no leído
if (isset($_GET['marcar']) && isset($_GET['id'])) {
    $marcar = ($_GET['marcar'] === "leido") ? 1 : 0;
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("UPDATE contacto SET leido=? WHERE id=?");
    $stmt->execute([$marcar, $id]);
    header("Location: contacto_admin.php");
    exit();
}

// Borrar mensaje
if (isset($_GET['borrar']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM contacto WHERE id=?");
    $stmt->execute([$id]);
    header("Location: contacto_admin.php");
    exit();
}

// Responder mensaje
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_mensaje'], $_POST['respuesta'])) {
    $id = intval($_POST['id_mensaje']);
    $respuesta = trim($_POST['respuesta']);
    if ($respuesta !== "") {
        $stmt = $conn->prepare("UPDATE contacto SET respuesta=?, fecha_respuesta=NOW(), leido=1 WHERE id=?");
        $stmt->execute([$respuesta, $id]);
        header("Location: contacto_admin.php");
        exit();
    }
}

$stmt = $conn->prepare("SELECT * FROM contacto ORDER BY fecha DESC");
$stmt->execute();
$mensajes = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mensajes de Contacto - Game Nation Admin</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="/GameNation/Administracion/assets/css/dashboard.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }
        body {
            min-height: 100vh;
            background: linear-gradient(to right, #0f111a, #1c1f2f);
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .titulo-contacto {
            color: #00f0ff;
            text-shadow: 0 0 8px #00f0ff88;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .titulo-contacto i {
            margin-right: 0.4em;
        }
        .volver-btn {
            background-color: transparent;
            border: 1.5px solid #00f0ff;
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
        .volver-btn:hover {
            background-color: #00f0ff;
            color: #1d1f2f;
            text-decoration: none;
        }
        .mensaje-block {
            background: #23263a;
            border-radius: 14px;
            box-shadow: 0 2px 18px #00f0ff22;
            margin-bottom: 2.2rem;
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            position: relative;
            border-left: 5px solid #00f0ff;
        }
        .mensaje-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .mensaje-header-izq {
            display: flex;
            align-items: center;
            gap: 0.6rem; /* espacio entre nombre y email */
        }
        .mensaje-header-izq .nombre {
            font-weight: 600;
            font-size: 1.13rem;
            color: #00f0ff;
        }
        .mensaje-header-izq .email {
            color: #7ae4ff;
            font-size: 1.01rem;
            text-decoration: underline;
        }
        .mensaje-header-der {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .mensaje-fecha {
            font-size: 0.98rem;
            color: #aaa;
            margin-bottom: 0.7rem;
        }
        .mensaje-contenido {
            font-size: 1.07rem;
            color: #f1f1f1;
            background: #1d1f2b;
            border-radius: 8px;
            padding: 1rem 1.1rem;
            margin-bottom: 1.1rem;
            word-break: break-word;
            white-space: pre-line;
            line-height: 1.6;
        }
        .mensaje-leido {
            background-color: #28a745;
            color: #fff;
            font-size: 1rem;
            padding: 0.5em 1em;
            border-radius: 1em;
            box-shadow: 0 2px 8px #28a74544;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .mensaje-no-leido {
            background-color: #ffc107;
            color: #23263a;
            font-size: 1rem;
            padding: 0.5em 1em;
            border-radius: 1em;
            box-shadow: 0 2px 8px #ffc10744;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-accion {
            padding: 0.25rem 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 6px;
            transition: background-color 0.2s;
        }
        .btn-accion:hover {
            filter: brightness(1.1);
            text-decoration: none;
        }
        .btn-msj.btn-accion {
            background-color: #28a745;
            color: #fff;
            border: none;
        }
        .btn-error.btn-accion {
            background-color: #ffc107;
            color: #23263a;
            border: none;
        }
        .btn-borrar.btn-accion {
            background-color: #dc3545;
            color: #fff;
            border: none;
        }
        .respuesta {
            background: #16202c;
            border-left: 4px solid #00f0ff;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 0.7rem;
            color: #7ae4ff;
        }
        .respuesta-form {
            background: #23263a;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 0.8rem;
            box-shadow: 0 2px 8px #00f0ff11;
        }
        .respuesta-form textarea {
            background: #1d1f2b;
            color: #f1f1f1;
            border-radius: 8px;
            border: 1.5px solid #00f0ff44;
            padding: 1.1rem;
            width: 100%;
            min-height: 120px;
            font-size: 1.08rem;
            margin-bottom: 1.2rem;
            resize: vertical;
            box-shadow: 0 2px 8px #00f0ff11;
            transition: border-color 0.2s;
        }
        .respuesta-form textarea:focus {
            border-color: #00f0ff;
            outline: none;
            box-shadow: 0 0 10px #00f0ff44;
        }
        .respuesta-form .btn {
            background: #00f0ff;
            color: #22304a;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            padding: 0.7rem 1.6rem;
            transition: background 0.2s;
            box-shadow: 0 2px 8px #00f0ff33;
        }
        .respuesta-form .btn:hover {
            background: #00c7e6;
            color: #fff;
        }
        @media (max-width: 900px) {
            .mensaje-block { padding: 1.2rem 0.5rem; }
            .mensaje-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            .mensaje-header-der {
                justify-content: flex-start;
            }
        }
        @media (max-width: 600px) {
            .mensaje-block { padding: 0.7rem 0.2rem; }
        }
    </style>
</head>
<body>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 titulo-contacto"><i class="fa-solid fa-envelope"></i> Mensajes de Contacto</h1>
    </div>
    <a href="../../index.php" class="volver-btn">
        <i class="fa-solid fa-arrow-left"></i> Volver al panel principal
    </a>
    <?php if (empty($mensajes)): ?>
        <div class="alert alert-info mb-0 admin-content-bg">
            No hay mensajes de contacto por mostrar.
        </div>
    <?php else: ?>
        <?php foreach ($mensajes as $msg): ?>
            <div class="mensaje-block">
                <div class="mensaje-header">
                    <div class="mensaje-header-izq">
                        <span class="nombre"><?= htmlspecialchars($msg->nombre) ?></span>
                        <span class="email">
                            <a href="mailto:<?= htmlspecialchars($msg->email) ?>">
                                <?= htmlspecialchars($msg->email) ?>
                            </a>
                        </span>
                        <?php if ($msg->leido): ?>
                            <span class="mensaje-leido"><i class="fa-solid fa-check-circle"></i> Leído</span>
                        <?php else: ?>
                            <span class="mensaje-no-leido"><i class="fa-solid fa-envelope"></i> No leído</span>
                        <?php endif; ?>
                    </div>
                    <div class="mensaje-header-der">
                        <a href="?borrar=1&id=<?= $msg->id ?>" class="btn btn-borrar btn-accion" title="Borrar" onclick="return confirm('¿Seguro que quieres borrar este mensaje?');">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                        <?php if (!$msg->leido): ?>
                            <a href="?marcar=leido&id=<?= $msg->id ?>" class="btn btn-msj btn-accion" title="Marcar como leído">
                                <i class="fa-solid fa-envelope-open-text"></i>
                            </a>
                        <?php else: ?>
                            <a href="?marcar=noleido&id=<?= $msg->id ?>" class="btn btn-error btn-accion" title="Marcar como no leído">
                                <i class="fa-solid fa-envelope"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mensaje-fecha"><?= $msg->fecha ?></div>
                <div class="mensaje-contenido"><?= nl2br(htmlspecialchars($msg->mensaje)) ?></div>
                <?php if ($msg->respuesta): ?>
                    <div class="respuesta">
                        <div><strong>Respuesta:</strong></div>
                        <?= nl2br(htmlspecialchars($msg->respuesta)) ?>
                        <div class="text-muted small mt-1"><?= $msg->fecha_respuesta ?></div>
                    </div>
                <?php elseif (!empty($msg->email)): ?>
                    <button class="btn btn-info btn-sm btn-accion" onclick="document.getElementById('respuesta-<?= $msg->id ?>').style.display='block'">
                        <i class="fa-solid fa-reply"></i> Responder
                    </button>
                    <form method="POST" style="display:none;" id="respuesta-<?= $msg->id ?>" class="respuesta-form mt-2">
                        <input type="hidden" name="id_mensaje" value="<?= $msg->id ?>">
                        <textarea name="respuesta" class="form-control mb-2" rows="4" placeholder="Escribe tu respuesta aquí..." required></textarea>
                        <button type="submit" class="btn btn-msj btn-sm">
                            <i class="fa-solid fa-paper-plane"></i> Enviar respuesta
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('respuesta-<?= $msg->id ?>').style.display='none'">
                            Cancelar
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
</body>
</html>