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
        // Solo permitir si el rol es 'usuario'
        if ($usuarioEncontrado->rol !== 'usuario') {
            $mensajeExito = "No puedes enviar solicitudes de amistad a administradores ni moderadores.";
        } else {
            // Verificar que no exista ya amistad o solicitud
            $check = $conn->prepare("SELECT * FROM amigos WHERE 
                    (usuario_id = :yo AND amigo_id = :el) 
                 OR (usuario_id = :el AND amigo_id = :yo)");
            $check->execute([":yo" => $miId, ":el" => $usuarioEncontrado->id]);

            if ($check->rowCount() === 0) {
                $insert = $conn->prepare("INSERT INTO amigos (usuario_id, amigo_id, aceptado) 
                                          VALUES (:yo, :el, FALSE)");
                $insert->execute([":yo" => $miId, ":el" => $usuarioEncontrado->id]);
                $mensajeExito = "¡Invitación de amistad enviada con éxito a <b>" . htmlspecialchars($usuarioBuscado) . "</b>!";
            } else {
                $mensajeExito = "Ya existe una invitación o amistad con <b>" . htmlspecialchars($usuarioBuscado) . "</b>.";
            }
        }
    } else {
        $mensajeExito = "Usuario no encontrado o no válido.";
    }
}

// Amigos aceptados
$sql = "SELECT u.* FROM usuarios u
        JOIN amigos a ON (
            (a.usuario_id = :yo AND a.amigo_id = u.id) OR
            (a.amigo_id = :yo AND a.usuario_id = u.id)
        )
        WHERE a.aceptado = TRUE AND u.id != :yo";
$amigos = $conn->prepare($sql);
$amigos->execute([":yo" => $miId]);
$lista_amigos = $amigos->fetchAll(PDO::FETCH_OBJ);

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
    <title>Amigos - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(120deg, #0d0f1c 60%, #202437 100%);
            color: #e0f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 1.08rem;
            min-height: 100vh;
            margin: 0;
        }
        .contenedor-principal {
            width: 100%;
            max-width: 720px;
            margin: 0 auto;
            padding: 3rem 1.5rem 2rem 1.5rem;
            min-height: 80vh;
            background: rgba(24,32,54,0.98);
            border-radius: 20px;
            box-shadow: 0 0 32px #00f0ff33;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h2 {
            color: #00f0ff;
            text-shadow: 0 0 10px #00f0ff99;
            margin-bottom: 2.2rem;
            letter-spacing: 1px;
            text-align: center;
            font-weight: 700;
        }
        .card {
            background-color: #181e2c;
            border-radius: 16px;
            box-shadow: 0 0 20px #00f0ff22;
            border: 2px solid #23263a;
            margin-bottom: 2rem;
            padding: 1.5rem 2rem;
            width: 100%;
            max-width: 560px;
            color: #e0f7fa;
        }
        .card-header {
            background-color: transparent;
            border-bottom: 2px solid #00f0ff44;
            font-weight: 600;
            font-size: 1.13rem;
            padding-bottom: 0.8rem;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
            color: #00f0ff;
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }
        .listado-grupo {
            background-color: #23263a;
            border: none;
            color: #e0f7fa;
            transition: background 0.3s;
            padding: 1rem 1.2rem;
            margin-bottom: 0.7rem;
            border-radius: 12px;
            font-size: 1.08rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            box-shadow: 0 0 10px #00f0ff11;
        }
        .listado-grupo:last-child {
            margin-bottom: 0;
        }
        .listado-grupo:hover {
            background-color: #26304a;
            box-shadow: 0 0 18px #00f0ff55;
        }
        .amigo-nombre {
            font-weight: 500;
            font-size: 1.08rem;
            word-break: break-word;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex-grow: 1;
        }
        .amigo-nombre img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 0 6px #00f0ff88;
        }
        .amigo-acciones {
            display: flex;
            gap: 0.7rem;
            white-space: nowrap;
        }
        .btn {
            border-radius: 10px;
            font-weight: 500;
            padding: 0.6rem 1.1rem;
            font-size: 1.02rem;
            transition: all 0.18s;
            border: none;
            cursor: pointer;
            box-shadow: 0 0 8px #00f0ff33;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-agregar {
            background-color: #00f0ff;
            color: #0d0f1c;
        }
        .btn-agregar:hover {
            background-color: #00b8d9;
            color: #fff;
        }
        .btn-info {
            background-color: #00c853;
            color: #fff;
        }
        .btn-info:hover {
            background-color: #43e97b;
            color: #0d0f1c;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #22263a;
        }
        .btn-warning:hover {
            background-color: #ffe082;
            color: #22263a;
        }
        .btn-peligro {
            border: 1.5px solid #ff4f4f;
            color: #ff4f4f;
            background: transparent;
        }
        .btn-peligro:hover {
            background: #ff4f4f;
            color: #fff;
        }
        .msj-alerta {
            background: #00f0ff22;
            color: #00f0ff;
            border: 1.5px solid #00f0ff88;
            font-weight: 600;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            letter-spacing: 0.5px;
            text-align: left;
            box-shadow: 0 0 10px #00f0ff55;
        }
        .texto-silenciado {
            color: #7ae4ff !important;
        }
        a.btn {
            transition: all 0.18s ease-in-out;
            text-decoration: none !important;
        }
        a.btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 8px #00f0ff66;
        }
        .card-body {
            padding: 1.2rem 0.5rem;
        }
        .form-control, .btn {
            margin-right: 0.5rem;
        }
        .form-section {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            margin-bottom: 2rem;
        }
        .form-section form {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            max-width: 560px;
            gap: 0.7rem;
        }
        .form-control {
            background-color: #23263a;
            border: 1.5px solid #00f0ff44;
            color: #e0f7fa;
            border-radius: 10px;
            padding: 0.7rem 1rem;
            font-size: 1.08rem;
            box-shadow: 0 0 8px #00f0ff11;
        }
        .form-control::placeholder {
            color: #7ae4ff;
        }
        .btn-outline-light {
            border: 2px solid #00f0ff;
            color: #00f0ff;
            background: transparent;
            margin-top: 2rem;
            font-size: 1.15rem;
            font-weight: bold;
            padding: 12px 35px !important;
            border-radius: 12px;
            box-shadow: 0 0 10px #00f0ff33;
            transition: all 0.25s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-outline-light i {
            margin-right: 10px;
        }
        .btn-outline-light:hover {
            background-color: #00f0ff22;
            color: #fff;
            border-color: #00f0ff;
            box-shadow: 0 0 18px #00f0ff66;
            text-decoration: none;
        }
        @media (max-width: 900px) {
            .contenedor-principal {
                max-width: 99vw;
                padding: 2rem 0.4rem 2rem 0.4rem;
            }
            .card {
                max-width: 99vw;
                padding: 1.1rem 0.5rem;
            }
            .form-section form {
                max-width: 100%;
            }
        }
      @media (max-width: 700px) {
  .amigos-vertical .listado-grupo {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.3rem;
    text-align: left;
  }
  .amigos-vertical .amigo-nombre {
    width: 100%;
    justify-content: flex-start;
    font-size: 1rem;
    word-break: break-word;
    margin-bottom: 0.2rem;
  }
  .amigos-vertical .amigo-acciones {
    width: 100%;
    justify-content: flex-start;
    gap: 0.5rem;
    margin-top: 0.2rem;
  }
}
    </style>
</head>
<body>
<div class="contenedor-principal">

    <h2>
        <i class="fa-solid fa-user-group"></i> Gestión de Amigos
    </h2>

    <!-- Aviso de éxito -->
    <?php if ($mensajeExito): ?>
        <div class="alert msj-alerta">
            <i class="fa-solid fa-paper-plane"></i> <?= $mensajeExito ?>
        </div>
    <?php endif; ?>

    <!-- Formulario Agregar -->
    <div class="form-section">
        <form method="POST">
            <input type="text" name="usuario" class="form-control" placeholder="Buscar usuario por nombre" required>
            <button class="btn btn-agregar">
                <i class="fa-solid fa-user-plus"></i> Agregar
            </button>
        </form>
    </div>

    <!-- Solicitudes Pendientes -->
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-user-clock"></i> Solicitudes Pendientes
        </div>
        <ul class="list-group list-group-flush">
            <?php if (count($solicitudes) === 0): ?>
                <li class="listado-grupo texto-silenciado">No tienes solicitudes pendientes.</li>
            <?php else: ?>
                <?php foreach ($solicitudes as $s): ?>
                    <li class="listado-grupo">
                        <span class="amigo-nombre">
                            <img src="<?= htmlspecialchars($s->foto ?: '/GameNation/assets/img/default-avatar.png') ?>" alt="Foto de <?= htmlspecialchars($s->usuario) ?>">
                            <?= htmlspecialchars($s->usuario) ?>
                        </span>
                        <div class="amigo-acciones">
                            <a href="?aceptar=<?= $s->id ?>" class="btn btn-sm btn-info">
                                <i class="fa-solid fa-check"></i> Aceptar
                            </a>
                            <a href="?eliminar=<?= $s->id ?>" class="btn btn-sm btn-peligro">
                                <i class="fa-solid fa-trash"></i> Eliminar
                            </a>
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
                <li class="listado-grupo texto-silenciado">No tienes solicitudes enviadas pendientes.</li>
            <?php else: ?>
                <?php foreach ($solicitudes_enviadas as $e): ?>
                    <li class="listado-grupo">
                        <span class="amigo-nombre">
                            <img src="<?= htmlspecialchars($e->foto ?: '/GameNation/assets/img/default-avatar.png') ?>" alt="Foto de <?= htmlspecialchars($e->usuario) ?>">
                            <?= htmlspecialchars($e->usuario) ?>
                        </span>
                        <div class="amigo-acciones">
                            <a href="?eliminar=<?= $e->id ?>" class="btn btn-sm btn-peligro">
                                <i class="fa-solid fa-xmark"></i> Cancelar
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Lista de Amigos -->
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-users"></i> Tus Amigos
        </div>
        <ul class="list-group list-group-flush amigos-vertical">
            <?php if (count($lista_amigos) === 0): ?>
                <li class="listado-grupo texto-silenciado">Aún no tienes amigos. Agrega alguno arriba.</li>
            <?php else: ?>
                <?php foreach ($lista_amigos as $a): ?>
                    <li class="listado-grupo">
                        <span class="amigo-nombre">
                            <img src="<?= htmlspecialchars($a->foto ?: '/GameNation/assets/img/default-avatar.png') ?>" alt="Foto de <?= htmlspecialchars($a->usuario) ?>">
                            <?= htmlspecialchars($a->usuario) ?>
                        </span>
                        <div class="amigo-acciones">
                            <a href="chat.php?amigo_id=<?= $a->id ?>" class="btn btn-sm btn-warning">
                                <i class="fa-solid fa-comments"></i> Chatear
                            </a>
                            <a href="?eliminar=<?= $a->id ?>" class="btn btn-sm btn-peligro">
                                <i class="fa-solid fa-trash"></i> Eliminar
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Botón volver -->
    <div class="text-center mt-4">
        <a href="../../index.php" class="btn-outline-light">
            <i class="fa-solid fa-arrow-left"></i> Volver al Inicio
        </a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>