<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}

$conn = db::conexion();

// Obtener todas las reseñas
$stmt = $conn->prepare("
    SELECT r.*, u.usuario 
    FROM resenas r
    JOIN usuarios u ON r.usuario_id = u.id
    ORDER BY r.fecha_publicacion DESC
");
$stmt->execute();
$reseñas = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrar Reseñas - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, #0d0f1c, #202437);
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 2rem;
        }
        .reseña-container {
            max-width: 900px;
            margin: 0 auto 2.5rem auto;
            background: linear-gradient(135deg, #1c1f2e 0%, #23263a 100%);
            border-radius: 18px;
            box-shadow: 0 6px 32px #00f0ff33;
            padding: 2rem 2rem 1.5rem 2rem;
            border-left: 6px solid #00f0ff;
            position: relative;
            overflow: hidden;
        }
        .reseña-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #00f0ff33;
            padding-bottom: 1.2rem;
        }
        .reseña-img {
            width: 92%;
            max-width: 500px;
            height: 140px;
            object-fit: cover;
            border-radius: 14px;
            background: #1c1f2e;
            box-shadow: 0 0 20px #00f0ff22;
            margin-bottom: 1.1rem;
            display: block;
        }
        .reseña-titulo {
            color: #00f0ff;
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 0.3rem;
            text-align: center;
        }
        .reseña-usuario {
            color: #7ae4ff;
            font-size: 1rem;
            margin-bottom: 0.4rem;
            text-align: center;
        }
        .reseña-puntuacion {
            color: #ffc107;
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 0.4rem;
            text-align: center;
        }
        .reseña-center-section {
            display: flex;
            justify-content: center;
            width: 100%;
            margin-bottom: 1rem;
        }
        .ficha-tecnica {
            background: #222d3d;
            border-radius: 10px;
            padding: 1rem;
            font-size: 1rem;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            box-shadow: 0 0 12px #00f0ff11;
        }
        .ficha-tecnica strong {
            color: #00f0ff;
            font-weight: 500;
            min-width: 110px;
            display: inline-block;
        }
        .reseña-opinion-container {
            display: flex;
            justify-content: center;
            width: 100%;
            margin-bottom: 0.7rem;
        }
        .reseña-opinion {
            color: #fff;
            font-size: 1.07rem;
            line-height: 1.6;
            background: #1c1f2e;
            border-radius: 10px;
            padding: 1.1rem;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 0 12px #00f0ff11;
            margin: 0;
        }
        .btn-editar {
            background: #00f0ff22;
            color: #00f0ff;
            border: 1.5px solid #00f0ff88;
            padding: 7px 18px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1rem;
            transition: all 0.2s;
            text-decoration: none;
            margin-left: 10px;
        }
        .btn-editar:hover {
            background: #00f0ff44;
            color: #fff;
            border-color: #00f0ff;
            box-shadow: 0 0 10px #00f0ff88;
        }
        .btn-crear {
            background: #00f0ff;
            color: #23263a;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 1rem;
            transition: all 0.2s;
            text-decoration: none;
            margin-bottom: 2rem;
            margin-top: 1rem;
            display: inline-block;
        }
        .btn-crear:hover {
            background: #00c7e6;
            box-shadow: 0 0 12px #00f0ff88;
        }
        .btn-outline-light {
            border: 1.5px solid #00f0ff;
            background-color: #23263a;
            color: #00f0ff;
            padding: 12px 28px;
            transition: all 0.3s;
            border-radius: 12px;
            margin-top: 1rem;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.07rem;
        }
        .btn-outline-light:hover {
            background-color: #00f0ff22;
            color: #fff;
            border-color: #00f0ff;
            box-shadow: 0 0 15px #00f0ff88;
        }
        @media (max-width: 900px) {
            .reseña-container {
                padding: 1.2rem 0.5rem 1.2rem 0.5rem;
            }
            .reseña-img,
            .ficha-tecnica,
            .reseña-opinion {
                max-width: 98vw;
            }
        }
        @media (max-width: 600px) {
            .reseña-img {
                height: 85px;
            }
            .ficha-tecnica,
            .reseña-opinion {
                padding: 0.7rem;
            }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-info mb-4"><i class="fa-solid fa-star"></i> Administración de Reseñas</h2>
    <div class="text-center">
        <a href="crear_reseña.php" class="btn btn-crear"><i class="fa-solid fa-plus"></i> Crear Reseña</a>
    </div>

    <?php foreach ($reseñas as $reseña): ?>
        <div class="reseña-container">
            <div class="reseña-header">
                <?php if (!empty($reseña->imagen)): ?>
                    <img src="<?= htmlspecialchars($reseña->imagen) ?>" class="reseña-img" alt="Carátula de <?= htmlspecialchars($reseña->videojuego) ?>">
                <?php else: ?>
                    <img src="/GameNation/assets/img/default-game.png" class="reseña-img" alt="Sin imagen">
                <?php endif; ?>
                <div class="reseña-titulo"><?= htmlspecialchars($reseña->videojuego) ?></div>
                <div class="reseña-usuario">Por <?= htmlspecialchars($reseña->usuario) ?> - <?= $reseña->fecha_publicacion ?></div>
                <div class="reseña-puntuacion"><i class="fa-solid fa-star"></i> <?= $reseña->puntuacion ?>/10</div>
                <a href="editar_reseña.php?id=<?= $reseña->id ?>" class="btn-editar"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
            </div>
            <div class="reseña-center-section">
                <div class="ficha-tecnica">
                    <?php if ($reseña->fecha_lanzamiento) echo "<div><strong>Lanzamiento:</strong> " . htmlspecialchars($reseña->fecha_lanzamiento) . "</div>"; ?>
                    <?php if ($reseña->desarrollo) echo "<div><strong>Desarrollo:</strong> " . htmlspecialchars($reseña->desarrollo) . "</div>"; ?>
                    <?php if ($reseña->produccion) echo "<div><strong>Producción:</strong> " . htmlspecialchars($reseña->produccion) . "</div>"; ?>
                    <?php if ($reseña->distribucion) echo "<div><strong>Distribución:</strong> " . htmlspecialchars($reseña->distribucion) . "</div>"; ?>
                    <?php if ($reseña->precio) echo "<div><strong>Precio:</strong> " . htmlspecialchars($reseña->precio) . "</div>"; ?>
                    <?php if ($reseña->jugadores) echo "<div><strong>Jugadores:</strong> " . htmlspecialchars($reseña->jugadores) . "</div>"; ?>
                    <?php if ($reseña->formato) echo "<div><strong>Formato:</strong> " . htmlspecialchars($reseña->formato) . "</div>"; ?>
                    <?php if ($reseña->textos) echo "<div><strong>Textos:</strong> " . htmlspecialchars($reseña->textos) . "</div>"; ?>
                    <?php if ($reseña->voces) echo "<div><strong>Voces:</strong> " . htmlspecialchars($reseña->voces) . "</div>"; ?>
                    <?php if ($reseña->online) echo "<div><strong>Online:</strong> " . htmlspecialchars($reseña->online) . "</div>"; ?>
                </div>
            </div>
            <div class="reseña-opinion-container">
                <div class="reseña-opinion">
                    <?= nl2br(htmlspecialchars($reseña->opinion)) ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="text-center">
        <a href="../../index.php" class="btn btn-outline-light">
            <i class="fa-solid fa-arrow-left"></i> Volver al Inicio
        </a>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>