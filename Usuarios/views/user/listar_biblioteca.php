<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}

$conn = db::conexion();
$miId = $_SESSION["usuario"]->id;

// Eliminar un juego
if (isset($_GET["eliminar"])) {
    $stmt = $conn->prepare("DELETE FROM biblioteca WHERE id = :id AND usuario_id = :usuario_id");
    $stmt->execute([
        ":id" => $_GET["eliminar"],
        ":usuario_id" => $miId
    ]);
    header("Location: listar_biblioteca.php");
    exit();
}

// Obtener todos los juegos del usuario
$stmt = $conn->prepare("SELECT * FROM biblioteca WHERE usuario_id = :usuario_id ORDER BY fecha_agregado DESC");
$stmt->execute([":usuario_id" => $miId]);
$juegos = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis Videojuegos - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/GameNation/assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, #0d0f1c, #202437);
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        h2 {
            color: #00f0ff;
            text-shadow: 0 0 6px #00f0ff88;
        }

        .listado-juegos {
            display: flex;
            flex-wrap: wrap;
            gap: 2.2rem;
            justify-content: center;
            margin-bottom: 2.5rem;
        }

        .contenido-juego {
            background: linear-gradient(135deg, #23263a 70%, #00f0ff22 100%);
            border-radius: 22px;
            box-shadow: 0 6px 32px #00f0ff33, 0 1.5px 0px #00f0ff11;
            padding: 2.1rem 1.5rem 1.6rem 1.5rem;
            width: 340px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
        }

        .contenido-juego:hover {
            transform: translateY(-8px) scale(1.025);
            box-shadow: 0 12px 48px #00f0ff77, 0 0px 0px #00f0ff11;
            border: 2.5px solid #00f0ff55;
        }

        .img-juego {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 14px;
            box-shadow: 0 0 18px #00f0ff33;
            margin-bottom: 1.1rem;
            background: #1c1f2e;
            border: 2.5px solid #00f0ff44;
        }

        .titulo-juego {
            font-size: 1.25rem;
            color: #00f0ff;
            font-weight: bold;
            margin-bottom: 0.4rem;
            text-shadow: 0 0 6px #00f0ff66;
            text-align: center;
        }

        .saga-juego {
            color: #7ae4ff;
            font-size: 1.05rem;
            margin-bottom: 0.8rem;
            font-weight: 500;
            text-align: center;
        }

        .info-juego {
            list-style: none;
            padding: 0;
            margin: 0 0 1.1rem 0;
            width: 100%;
        }
        .info-juego li {
            margin-bottom: 0.35rem;
            font-size: 1.03rem;
            color: #d2faff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .info-juego li i {
            color: #00f0ff;
            min-width: 20px;
        }

        .opinion-juego {
            background: #1c1f2e;
            color: #fff;
            border-radius: 10px;
            padding: 0.6rem 1rem;
            font-size: 0.99rem;
            margin-bottom: 1.1rem;
            min-height: 48px;
            width: 100%;
            box-shadow: 0 0 8px #00f0ff11;
        }

        .acciones-juego {
            display: flex;
            gap: 1.1rem;
            width: 100%;
            justify-content: center;
            margin-top: 0.4rem;
        }

        /* BOTONES SIMPLES */
        .btn-edit {
            background: #ffc107;
            color: #222d3d;
            border: none;
            border-radius: 12px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            font-size: 1.02rem;
            box-shadow: 0 0 8px #ffc10744;
            transition: background 0.18s, color 0.18s, box-shadow 0.18s, transform 0.13s;
            text-decoration: none;
        }
        .btn-edit:hover {
            background: #ffe082;
            color: #222d3d;
            box-shadow: 0 0 18px #ffc10788;
            transform: scale(1.06);
        }

        .btn-eliminar {
            background: #dc3545;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            font-size: 1.02rem;
            box-shadow: 0 0 8px #dc354544;
            transition: background 0.18s, color 0.18s, box-shadow 0.18s, transform 0.13s;
            text-decoration: none;
        }
        .btn-eliminar:hover {
            background: #ff6b81;
            color: #fff;
            box-shadow: 0 0 18px #dc354588;
            transform: scale(1.06);
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

        @media (max-width: 575px) {
            .listado-juegos {
                gap: 1.2rem;
            }
            .contenido-juego {
                width: 98vw;
                padding: 1.3rem 0.4rem 1.1rem 0.4rem;
            }
            .img-juego {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-info mb-4"><i class="fa-solid fa-list"></i> Mis Videojuegos</h2>

    <?php if (empty($juegos)): ?>
        <p class="text-muted text-center">Aún no tienes juegos en tu biblioteca.</p>
    <?php else: ?>
        <div class="listado-juegos">
            <?php foreach ($juegos as $juego): ?>
                <div class="contenido-juego">
                    <?php if (!empty($juego->imagen)): ?>
                        <img src="<?= htmlspecialchars($juego->imagen) ?>" alt="<?= htmlspecialchars($juego->titulo) ?>" class="img-juego">
                    <?php else: ?>
                        <div class="img-juego" style="display:flex;align-items:center;justify-content:center;background:#23263a;color:#00f0ff;font-size:2.2rem;">
                            <i class="fa-solid fa-gamepad"></i>
                        </div>
                    <?php endif; ?>
                    <div class="titulo-juego"><?= htmlspecialchars($juego->titulo) ?></div>
                    <div class="saga-juego"><?= htmlspecialchars($juego->saga) ?: 'Sin Saga' ?></div>
                    <ul class="info-juego">
                        <li><i class="fa-solid fa-check-circle"></i> <strong>Estado:</strong> <?= ucfirst($juego->estado) ?></li>
                        <li><i class="fa-solid fa-star"></i> <strong>Valoración:</strong> <?= $juego->valoracion ?>/10</li>
                    </ul>
                    <div class="opinion-juego">
                        <strong>Opinión:</strong><br>
                        <?= htmlspecialchars($juego->opinion) ?: '<span class="text-muted">Sin Opinión</span>' ?>
                    </div>
                    <div class="acciones-juego">
                        <a href="editar_biblioteca.php?id=<?= $juego->id ?>" class="btn-edit">
                            <i class="fa-solid fa-pen"></i> Editar
                        </a>
                        <a href="?eliminar=<?= $juego->id ?>" class="btn-eliminar" onclick="return confirm('¿Seguro que quieres eliminar este juego?');">
                            <i class="fa-solid fa-trash"></i> Eliminar
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="text-center">
        <a href="biblioteca.php" class="btn-outline-light">
            <i class="fa-solid fa-arrow-left"></i> Volver a Biblioteca
        </a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>