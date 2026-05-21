<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}

$conn = db::conexion();

$stmt = $conn->prepare("SELECT n.*, u.usuario AS autor 
                        FROM noticias n
                        JOIN usuarios u ON n.autor_id = u.id
                        ORDER BY fecha_publicacion DESC");
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
    <style>
        body {
            background: linear-gradient(to right, #12141e, #252a3a);
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 2rem;
        }

        h2 {
            color: #7ae4ff;
            text-shadow: 0 0 6px #7ae4ff88;
            margin-bottom: 2rem;
            text-align: center;
        }

        .card {
            background-color: #1d1f2f;
            border-radius: 12px;
            box-shadow: 0 0 15px #00f0ff22;
            padding: 2rem;
            margin: 0 auto 2rem auto;
            max-width: 800px;
        }

        .noticia {
            background-color: #2a2d3f;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            margin-top: 3rem;
            box-shadow: 0 0 10px #00f0ff11;
            text-align: center;
        }

        .noticia h5 {
            color: #00f0ff;
        }

        .noticia img {
            display: block;
            margin: 0 auto 1rem auto;
            max-width: 80%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 0 10px #00f0ff22;
        }

        .noticia .autor {
            font-size: 0.9rem;
            color: #aaa;
        }

        .btn-outline-light {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 10px 20px;
            border-radius: 10px;
            background-color: #333;
            color: #f1f1f1;
            border: 1px solid #555;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-outline-light:hover {
            background-color: #00f0ff22;
            color: #00f0ff;
            border-color: #00f0ff;
            box-shadow: 0 0 10px #00f0ff88;
        }
        .comentario {
            display: flex;
            gap: 15px;
            background-color: #1e2235;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 0 10px #00f0ff22;
            align-items: flex-start;
        }

        .comentario .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 0 5px #00f0ff55;
        }

        .comentario .contenido {
            flex-grow: 1;
        }

        .comentario .info {
            margin-bottom: 5px;
        }

        .comentario p {
            margin: 0;
            color: #ccc;
        }
        .comentario-form {
            background-color: #1d1f2f;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 0 10px #00f0ff22;
            margin-top: 2rem;
        }

        .comentario-textarea {
            width: 97%;
            background-color: #2a2d3f;
            color: #f1f1f1;
            border: 1px solid #444;
            border-radius: 8px;
            padding: 10px;
            resize: vertical;
            min-height: 80px;
            font-family: inherit;
            transition: border 0.3s ease;
        }

        .comentario-textarea:focus {
            border-color: #00f0ff;
            outline: none;
            box-shadow: 0 0 8px #00f0ff55;
        }

        .comentario-btn {
            margin-top: 10px;
            background-color: #00f0ff22;
            color: #00f0ff;
            border: 1px solid #00f0ff55;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .comentario-btn:hover {
            background-color: #00f0ff44;
            box-shadow: 0 0 10px #00f0ff88;
            color: #fff;
        }

        @media (max-width: 600px) {
            .card { padding: 1rem; }
            .noticia { padding: 1rem; }
            .comentario { flex-direction: column; align-items: stretch; }
            .comentario .avatar { margin-bottom: 10px; }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2><i class="fa-solid fa-newspaper"></i> Noticias de Game Nation</h2>

    <div class="card">
        <?php foreach ($noticias as $noticia): ?>
            <div class="noticia">
                <?php if (!empty($noticia->imagen)): ?>
                    <img src="<?= htmlspecialchars($noticia->imagen) ?>" alt="Imagen de la noticia">
                <?php endif; ?>
                <h5><?= htmlspecialchars($noticia->titulo) ?></h5>
                <p class="autor">Por <?= htmlspecialchars($noticia->autor) ?> | <?= $noticia->fecha_publicacion ?></p>
                <p><?= nl2br(htmlspecialchars($noticia->contenido)) ?></p>
            </div>
            <!-- Mostrar comentarios -->
            <div class="mt-3">
                <h6>Comentarios:</h6>
                <?php
                $cStmt = $conn->prepare("SELECT c.*, u.usuario, u.foto FROM comentarios c 
                                         JOIN usuarios u ON c.usuario_id = u.id 
                                         WHERE c.noticia_id = :noticia_id 
                                         ORDER BY c.fecha DESC");
                $cStmt->execute([":noticia_id" => $noticia->id]);
                $comentarios = $cStmt->fetchAll(PDO::FETCH_OBJ);

                foreach ($comentarios as $comentario): ?>
                    <div class="comentario">
                        <img class="avatar"
                             src="<?= !empty($comentario->foto) ? htmlspecialchars($comentario->foto) : '/GameNation/assets/img/default-user.png' ?>"
                             alt="avatar">
                        <div class="contenido">
                            <div class="info">
                                <strong><?= htmlspecialchars($comentario->usuario) ?></strong>
                                <small class="text-muted"> - <?= $comentario->fecha ?></small>
                            </div>
                            <p><?= nl2br(htmlspecialchars($comentario->contenido)) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Formulario de comentario -->
            <form method="POST" action="comentar.php" class="comentario-form mt-4">
                <input type="hidden" name="noticia_id" value="<?= $noticia->id ?>">
                <textarea name="contenido" class="comentario-textarea" placeholder="Escribe un comentario..." required></textarea>
                <button class="comentario-btn" type="submit">
                    <i class="fa-solid fa-paper-plane"></i> Comentar
                </button>
            </form>
        <?php endforeach; ?>

        <div class="text-center">
            <a href="../../index.php" class="btn btn-outline-light">
                <i class="fa-solid fa-arrow-left"></i> Volver al Inicio
            </a>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>