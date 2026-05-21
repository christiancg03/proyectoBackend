<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit();
}

$conn = db::conexion();
$miId = $_SESSION['usuario']->id;

// Eliminar noticia si es el autor
if (isset($_GET['eliminar'])) {
    $idEliminar = intval($_GET['eliminar']);
    // Verificar que la noticia es del usuario
    $stmt = $conn->prepare('SELECT * FROM noticias WHERE id = :id AND autor_id = :autor');
    $stmt->execute([':id' => $idEliminar, ':autor' => $miId]);
    if ($stmt->fetch()) {
        $stmt = $conn->prepare('DELETE FROM noticias WHERE id = :id');
        $stmt->execute([':id' => $idEliminar]);
    }
    header('Location: listar_noticias.php');
    exit();
}

// Obtener todas las noticias
$stmt = $conn->prepare('SELECT n.*, u.usuario AS autor FROM noticias n JOIN usuarios u ON n.autor_id = u.id ORDER BY fecha_publicacion DESC');
$stmt->execute();
$noticias = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Noticias - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, #0d0f1c, #202437);
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 2rem;
        }
        .noticias-listado {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: center;
        }
        .noticia-card {
            background: #23263a;
            border-radius: 16px;
            box-shadow: 0 2px 18px #00f0ff22;
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            max-width: 400px;
            width: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            margin-bottom: 1.5rem;
        }
        .img-noticia {
            width: 100%;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #1c1f2e;
            border-radius: 12px;
            margin-bottom: 1rem;
            border: 1px solid #222b36;
            position: relative;
        }
        .img-noticia img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* SIEMPRE cubre el área, nunca deja bordes */
            display: block;
            margin: 0;
            background: #222b36;
            /* Elimina cualquier borde o sombra especial para evitar marcos */
        }
        .noticia-card h5 {
            color: #00f0ff;
            margin-bottom: 10px;
            font-size: 1.22rem;
            font-weight: bold;
        }
        .noticia-card .autor {
            color: #7ae4ff;
            font-size: 1.01rem;
            margin-bottom: 0.7rem;
        }
        .noticia-card p {
            color: #ccc;
            margin-bottom: 10px;
        }
        .btn-editar, .btn-eliminar, .btn-visualizar {
            border: 1px solid #555;
            background-color: #333;
            color: #f1f1f1;
            padding: 10px 15px;
            transition: all 0.3s;
            border-radius: 12px;
            margin-top: 1.2rem;
            text-decoration: none;
            display: inline-block;
            margin-right: 0.5rem;
        }
        .btn-editar:hover, .btn-eliminar:hover, .btn-visualizar:hover {
            background-color: #00f0ff22;
            color: #00f0ff;
            border-color: #00f0ff;
            box-shadow: 0 0 15px #00f0ff88;
            text-decoration: none;
        }
        .btn-eliminar {
            border: 1px solid #dc3545;
            color: #dc3545;
            background-color: #333;
        }
        .btn-visualizar{
            border: 1px solid #a3da0cff;
            color: #a3da0cff;
        }
        .btn-eliminar:hover {
            background-color: #dc354522;
            color: #fff;
            border-color: #dc3545;
        }
        .btns-inferiores {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .btn-volver {
            border: 1px solid #555;
            background-color: #333;
            color: #f1f1f1;
            padding: 10px 15px;
            transition: all 0.3s;
            border-radius: 12px;
            text-decoration: none;
        }
        .btn-volver:hover {
            background-color: #00f0ff22;
            color: #00f0ff;
            border-color: #00f0ff;
            box-shadow: 0 0 15px #00f0ff88;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .noticias-listado { gap: 1rem; }
            .noticia-card { padding: 1.1rem 0.5rem 1rem 0.5rem; max-width: 98vw; }
            .img-noticia { height: 140px; }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-info mb-4">📰 Noticias Publicadas</h2>
    <div class="noticias-listado">
        <?php foreach ($noticias as $noticia): ?>
            <div class="noticia-card">
                <?php if (!empty($noticia->imagen)): ?>
                    <div class="img-noticia">
                        <img src="<?= htmlspecialchars($noticia->imagen) ?>" alt="Imagen de la noticia">
                    </div>
                <?php endif; ?>
                <h5><?= htmlspecialchars($noticia->titulo) ?></h5>
                <div class="autor">Por <?= htmlspecialchars($noticia->autor) ?> | <?= $noticia->fecha_publicacion ?></div>
                <p><?= nl2br(htmlspecialchars($noticia->contenido)) ?></p>
                <?php if ($noticia->autor_id == $miId): ?>
                    <div style="text-align: right;">
                        <a href="listar_comentarios.php?id=<?= $noticia->id ?>" class="btn-visualizar">
                            <i class="fa-solid fa-pen-to-square"></i>Ver Comentarios
                        </a>
                        <a href="editar_noticia.php?id=<?= $noticia->id ?>" class="btn-editar">
                            <i class="fa-solid fa-pen-to-square"></i> Editar Noticia
                        </a>
                        <a href="listar_noticias.php?eliminar=<?= $noticia->id ?>" class="btn-eliminar" onclick="return confirm('¿Seguro que quieres eliminar esta noticia?');">
                            <i class="fa-solid fa-trash"></i> Eliminar
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="btns-inferiores">
        <a href="añadir_noticias.php" class="btn btn-volver"><i class="fa-solid fa-plus"></i> Crear Noticia</a>
        <a href="../../index.php" class="btn btn-volver"><i class="fa-solid fa-arrow-left"></i> Volver al Inicio</a>
        <a href="noticias.php" class="btn btn-volver"><i class="fa-solid fa-arrow-left"></i> Atrás</a>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>