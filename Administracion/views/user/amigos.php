<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}

$conn = db::conexion();
$miId = $_SESSION["usuario"]->id;
$miRol = $_SESSION["usuario"]->rol;

$mensajeExito = null;

// ACEPTAR solicitud
if (isset($_GET["aceptar"])) {
    $sql = "UPDATE amigos SET aceptado = TRUE WHERE usuario_id = :amigo AND amigo_id = :yo";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":amigo" => $_GET["aceptar"],
        ":yo" => $miId
    ]);
    header("Location: amigos.php");
    exit();
}

// ELIMINAR amistad o solicitud
if (isset($_GET["eliminar"])) {
    $sql = "DELETE FROM amigos 
            WHERE (usuario_id = :yo AND amigo_id = :otro) 
               OR (usuario_id = :otro AND amigo_id = :yo)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":yo" => $miId,
        ":otro" => $_GET["eliminar"]
    ]);
    header("Location: amigos.php");
    exit();
}

// ENVIAR nueva solicitud
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["usuario"])) {
    $usuarioBuscado = $_POST["usuario"];

    // Buscar usuario y obtener su rol
    $stmt = $conn->prepare("SELECT id, rol FROM usuarios WHERE usuario = :nombre");
    $stmt->execute([":nombre" => $usuarioBuscado]);
    $usuarioEncontrado = $stmt->fetch(PDO::FETCH_OBJ);

    if ($usuarioEncontrado && $usuarioEncontrado->id != $miId) {
        // En administración: admins/mods solo pueden enviar solicitudes a otros admins/mods
        if (
            ($miRol === 'admin' || $miRol === 'moderador') &&
            $usuarioEncontrado->rol === 'usuario'
        ) {
            $mensajeExito = "No puedes enviar solicitudes a usuarios normales.";
        }
        // Si el destino es admin o moderador, sí se permite
        else if ($usuarioEncontrado->rol === 'usuario' && $miRol === 'usuario') {
            $mensajeExito = "No puedes enviar solicitudes de amistad a administradores ni moderadores.";
        }
        else {
            // Verificar que no exista ya amistad o solicitud
            $check = $conn->prepare("SELECT * FROM amigos WHERE 
                    (usuario_id = :yo AND amigo_id = :el) 
                 OR (usuario_id = :el AND amigo_id = :yo)");
            $check->execute([":yo" => $miId, ":el" => $usuarioEncontrado->id]);

            if ($check->rowCount() === 0) {
                $insert = $conn->prepare("INSERT INTO amigos (usuario_id, amigo_id, aceptado) 
                                          VALUES (:yo, :el, FALSE)");
                $insert->execute([":yo" => $miId, ":el" => $usuarioEncontrado->id]);
                $mensajeExito = "¡Invitación enviada a <b>" . htmlspecialchars($usuarioBuscado) . "</b>!";
            } else {
                $mensajeExito = "Ya existe una invitación o contacto con <b>" . htmlspecialchars($usuarioBuscado) . "</b>.";
            }
        }
    } else {
        $mensajeExito = "Usuario no encontrado o no válido.";
    }
}

// Contactos aceptados
$sql = "SELECT u.* FROM usuarios u
        JOIN amigos a ON (
            (a.usuario_id = :yo AND a.amigo_id = u.id) OR
            (a.amigo_id = :yo AND a.usuario_id = u.id)
        )
        WHERE a.aceptado = TRUE AND u.id != :yo";
$contactos = $conn->prepare($sql);
$contactos->execute([":yo" => $miId]);
$lista_contactos = $contactos->fetchAll(PDO::FETCH_OBJ);

// Solicitudes recibidas (no aceptadas)
$sql = "SELECT u.* FROM usuarios u
        JOIN amigos a ON a.usuario_id = u.id
        WHERE a.amigo_id = :yo AND a.aceptado = FALSE";
$pendientes = $conn->prepare($sql);
$pendientes->execute([":yo" => $miId]);
$solicitudes = $pendientes->fetchAll(PDO::FETCH_OBJ);

// Solicitudes enviadas (no aceptadas)
$sql = "SELECT u.* FROM usuarios u
        JOIN amigos a ON a.amigo_id = u.id
        WHERE a.usuario_id = :yo AND a.aceptado = FALSE";
$enviadas = $conn->prepare($sql);
$enviadas->execute([":yo" => $miId]);
$solicitudes_enviadas = $enviadas->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contactos - Administración | Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/GameNation/assets/css/dashboard.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #121826, #1a2033 80%);
            color: #cfd8dc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 1.08rem;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .contenedor-principal {
            width: 100%;
            max-width: 860px;
            min-width: 320px;
            margin: 0 auto;
            padding: 3rem 2.5rem 2rem 2.5rem;
            min-height: 80vh;
            background: #1f2738;
            border-radius: 18px;
            box-shadow: 0 0 32px #00b8d966;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }
        h2 {
            color: #00b8d9;
            text-shadow: 0 0 10px #00b8d966;
            margin-bottom: 2rem;
            letter-spacing: 1px;
            text-align: center;
            font-weight: 700;
            width: 100%;
        }
        .card {
            background-color: #273447;
            border-radius: 16px;
            box-shadow: 0 0 20px #00b8d922;
            border: none;
            margin-bottom: 2rem;
            padding: 1.5rem 2rem;
            width: 100%;
            max-width: 760px;
            color: #cfd8dc;
        }
        .card-header {
            background-color: transparent;
            border-bottom: 2px solid #00b8d944;
            font-weight: 600;
            font-size: 1.13rem;
            padding-bottom: 0.8rem;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
            color: #00b8d9;
        }
        .apartados {
            background-color: #334158;
            border: none;
            color: #cfd8dc;
            transition: background 0.3s;
            padding: 1rem 1.2rem;
            margin-bottom: 0.6rem;
            border-radius: 10px;
            font-size: 1.08rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
        }
        .apartados:last-child {
            margin-bottom: 0;
        }
        .apartados:hover {
            background-color: #3f506a;
        }
        .nombre-contacto {
            flex-grow: 1;
            font-weight: 500;
            font-size: 1.08rem;
            word-break: break-all;
        }
        .acciones-contacto {
            display: flex;
            gap: 0.7rem;
        }
        input.form-control {
            background-color: #2e3a53;
            border: 1px solid #4a90e2;
            color: #cfd8dc;
            border-radius: 10px;
            padding: 0.7rem 1rem;
            font-size: 1.08rem;
        }
        input.form-control::placeholder {
            color: #7aa9d9;
        }
        .btn {
            border-radius: 10px;
            font-weight: 500;
            padding: 0.6rem 1.1rem;
            font-size: 1.02rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-agregar {
            background-color: #00b8d9;
            color: #d7eaf7;
        }
        .btn-agregar:hover {
            background-color: #0096c7;
            color: #e0f7ff;
        }
        .btn-info {
            background-color: #00f0ff;
            color: #1f2738;
        }
        .btn-info:hover {
            background-color: #00c7e6;
            color: #e0f7ff;
        }
        .btn-chat {
            background-color: #ffc107;
            color: #1f2738;
        }
        .btn-chat:hover {
            background-color: #e0a800;
            color: #fff;
        }
        .btn-eliminar {
            border: 1px solid #ff6b6b;
            color: #ff6b6b;
            background: transparent;
        }
        .btn-eliminar:hover {
            background: #ff6b6b;
            color: #1f2738;
        }
        .text-muted {
            color: #7aa9d9 !important;
        }
        a.btn {
            transition: all 0.2s ease-in-out;
            text-decoration: none !important;
        }
        a.btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 8px #00b8d922;
        }
        .card-body {
            padding: 1.2rem 0.5rem;
        }
        .form-control, .btn {
            margin-right: 0.5rem;
        }
        .mensaje-alerta {
            background: #2e3a53;
            color: #00b8d9;
            border: 1px solid #00b8d9aa;
            font-weight: 600;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            letter-spacing: 0.5px;
            text-align: left;
            box-shadow: 0 0 10px #00b8d9aa;
        }
        .text-center .btn-volver {
            margin-top: 2rem;
            display: inline-block;
            background: #00b8d9;
            color: #1f2738;
            border: none;
            border-radius: 14px;
            padding: 0.8rem 2.2rem;
            font-weight: 600;
            font-size: 1.08rem;
            box-shadow: 0 0 10px #00b8d9aa;
            transition: background 0.3s, color 0.3s, box-shadow 0.3s;
            text-decoration: none !important;
            margin-left: auto;
            margin-right: auto;
        }
        .text-center .btn-volver:hover {
            background: #0096c7;
            color: #e0f7ff;
            box-shadow: 0 0 18px #00b8d9cc;
        }
        /* Centrar el formulario de búsqueda/agregar */
        .form-section {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        .form-section form {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            gap: 0.7rem;
        }
        @media (max-width: 900px) {
            .contenedor-principal {
                max-width: 98vw;
                padding: 2rem 0.4rem 2rem 0.4rem;
            }
            .card {
                max-width: 99vw;
                padding: 1.1rem 0.5rem;
            }
        }
        @media (max-width: 600px) {
            .contenedor-principal {
                padding: 1.5rem 0.1rem 1.5rem 0.1rem;
            }
            h2 {
                font-size: 1.4rem;
            }
            .form-section form {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
<div class="contenedor-principal">

    <h2>
        <i class="fa-solid fa-address-book"></i> Gestión de Contactos
    </h2>

    <!-- Aviso de éxito -->
    <?php if ($mensajeExito): ?>
        <div class="alert mensaje-alerta">
            <i class="fa-solid fa-paper-plane"></i> <?= $mensajeExito ?>
        </div>
    <?php endif; ?>

    <!-- Formulario Agregar -->
    <div class="card form-section">
        <div class="card-body">
            <form method="POST">
                <input type="text" name="usuario" class="form-control" placeholder="Buscar usuario por nombre" required>
                <button class="btn btn-agregar">
                    <i class="fa-solid fa-user-plus"></i> Agregar
                </button>
            </form>
        </div>
    </div>

    <!-- Solicitudes Pendientes -->
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-user-clock"></i> Solicitudes Pendientes
        </div>
        <ul class="list-group list-group-flush">
            <?php if (count($solicitudes) === 0): ?>
                <li class="apartados text-muted">No hay solicitudes pendientes.</li>
            <?php else: ?>
                <?php foreach ($solicitudes as $s): ?>
                    <li class="apartados">
                        <span class="nombre-contacto"><?= htmlspecialchars($s->usuario) ?></span>
                        <div class="acciones-contacto">
                            <a href="?aceptar=<?= $s->id ?>" class="btn btn-sm btn-info">Aceptar</a>
                            <a href="?eliminar=<?= $s->id ?>" class="btn btn-sm btn-eliminar">Eliminar</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Solicitudes Enviadas -->
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-paper-plane"></i> Solicitudes Enviadas
        </div>
        <ul class="list-group list-group-flush">
            <?php if (count($solicitudes_enviadas) === 0): ?>
                <li class="apartados text-muted">No hay solicitudes enviadas.</li>
            <?php else: ?>
                <?php foreach ($solicitudes_enviadas as $e): ?>
                    <li class="apartados">
                        <span class="nombre-contacto"><?= htmlspecialchars($e->usuario) ?></span>
                        <div class="acciones-contacto">
                            <a href="?eliminar=<?= $e->id ?>" class="btn btn-sm btn-eliminar">Cancelar</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Lista de Contactos -->
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-users"></i> Contactos
        </div>
        <ul class="list-group list-group-flush">
            <?php if (count($lista_contactos) === 0): ?>
                <li class="apartados text-muted">Aún no tienes contactos.</li>
            <?php else: ?>
                <?php foreach ($lista_contactos as $a): ?>
                    <li class="apartados">
                        <span class="nombre-contacto"><?= htmlspecialchars($a->usuario) ?></span>
                        <div class="acciones-contacto">
                            <a href="chat.php?amigo_id=<?= $a->id ?>" class="btn btn-sm btn-chat">
                                <i class="fa-solid fa-comments"></i> Chatear
                            </a>
                            <a href="?eliminar=<?= $a->id ?>" class="btn btn-sm btn-eliminar">Eliminar</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Botón volver -->
    <div class="text-center mt-4">
        <a href="../../index.php" class="btn-volver">
            <i class="fa-solid fa-arrow-left"></i> Volver al Inicio
        </a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>