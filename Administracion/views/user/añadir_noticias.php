<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit();
}

$conn = db::conexion();
$miId = $_SESSION['usuario']->id;
$mensaje = '';

// Ruta donde se guardarán las imágenes subidas
$uploadDir = '/GameNation/Administracion/uploads/';
$uploadPath = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $imagenRuta = null;

    // Procesar imagen si se ha subido
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imagen']['tmp_name'];
        $fileName = basename($_FILES['imagen']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($fileExtension, $allowedExtensions)) {
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            // Evitar nombre duplicado
            $newFileName = uniqid('noticia_', true) . '.' . $fileExtension;
            $destPath = $uploadPath . $newFileName;
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imagenRuta = $uploadDir . $newFileName;
            } else {
                $mensaje = "<div class='alert alert-danger'>❌ Error al mover la imagen subida.</div>";
            }
        } else {
            $mensaje = "<div class='alert alert-danger'>❌ Formato de imagen no permitido.</div>";
        }
    }

    if ($titulo !== '' && $contenido !== '') {
        try {
            $stmt = $conn->prepare('INSERT INTO noticias (titulo, contenido, imagen, autor_id) VALUES (:titulo, :contenido, :imagen, :autor_id)');
            $stmt->execute([
                ':titulo' => $titulo,
                ':contenido' => $contenido,
                ':imagen' => $imagenRuta,
                ':autor_id' => $miId
            ]);
            $mensaje = "<div class='alert alert-success'>📰 Noticia publicada correctamente.</div>";
        } catch (Exception $e) {
            $mensaje = "<div class='alert alert-danger'>❌ Error al publicar la noticia: " . $e->getMessage() . "</div>";
        }
    } else if ($mensaje === '') {
        $mensaje = "<div class='alert alert-danger'>❌ Debes completar todos los campos correctamente.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Añadir Noticias - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/GameNation/assets/css/dashboard.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #0d0f1c, #202437);
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            background-color: #1c1f2e;
            border-radius: 15px;
            box-shadow: 0 0 20px #00f0ff33;
            padding: 2.5rem;
            margin-bottom: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        input.form-control, textarea.form-control {
            background-color: #2a2d3e;
            border: 2px solid #555;
            color: #fff;
            border-radius: 12px;
            padding: 15px;
            font-size: 1.1rem;
            transition: border 0.3s;
            margin-bottom: 1rem;
        }
        input[type="file"].form-control {
            padding: 0.5rem 1rem;
            color-scheme: dark;
        }
        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 12px 20px;
            background-color: #00f0ff;
            color: #000;
            border: none;
            transition: background-color 0.3s, box-shadow 0.3s;
            width: 100%;
            margin-bottom: 1rem;
        }
        .btn:hover {
            background-color: #00c7e6;
            box-shadow: 0 0 15px #00c7e688;
        }
        .acciones-btn {
            border: 1px solid #555;
            background-color: #333;
            color: #f1f1f1;
            padding: 10px 15px;
            transition: all 0.3s;
            border-radius: 12px;
            margin-top: 1rem;
            text-decoration: none;
        }
        .acciones-btn:hover {
            background-color: #00f0ff22;
            color: #00f0ff;
            border-color: #00f0ff;
            box-shadow: 0 0 15px #00f0ff88;
        }
        .alert {
            margin-bottom: 2rem;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            max-width: 800px;
            margin: 0 auto 2rem auto;
        }
        .alert-success {
            background-color: #28a745;
            color: #fff;
            border: 1px solid #1e7e34;
        }
        .alert-danger {
            background-color: #dc3545;
            color: #fff;
            border: 1px solid #a71d2a;
        }
        @media (max-width: 576px) {
    .text-center .acciones-btn {
            display: block;
            width: 50%;
            margin-bottom: 1rem;
    }
}

    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-info mb-4">📝 Crear Nueva Noticia</h2>
    <?= $mensaje ?>
    <form method="POST" class="card" enctype="multipart/form-data">
        <input type="text" name="titulo" class="form-control" placeholder="Título de la noticia" required>
        <textarea name="contenido" class="form-control" rows="4" placeholder="Contenido de la noticia" required></textarea>
        <input type="file" name="imagen" class="form-control" accept="image/*">
        <button class="btn"><i class="fa-solid fa-save"></i> Publicar Noticia</button>
    </form>
    <div class="text-center">
        <a href="listar_noticias.php" class="btn acciones-btn"><i class="fa-solid fa-list"></i> Ver Noticias</a>
        <a href="../../index.php" class="btn acciones-btn"><i class="fa-solid fa-arrow-left"></i> Volver al Inicio</a>
        <a href="noticias.php" class="btn acciones-btn"><i class="fa-solid fa-arrow-left"></i> Atrás</a>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>