<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit();
}

$conn = db::conexion();
$miId = $_SESSION['usuario']->id;

if (!isset($_GET['id'])) {
    die("Error: ID de noticia no especificado.");
}
$noticia_id = intval($_GET['id']);

// --- LÓGICA PARA ELIMINAR COMENTARIO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_comentario_id'])) {
    $comentario_a_eliminar = intval($_POST['eliminar_comentario_id']);
    
    $dStmt = $conn->prepare("DELETE FROM comentarios WHERE id = :id AND noticia_id = :noticia_id");
    $dStmt->execute([
        ':id' => $comentario_a_eliminar,
        ':noticia_id' => $noticia_id
    ]);
    
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $noticia_id);
    exit();
}

// --- OBTENER EL TÍTULO DE LA NOTICIA ---
$nStmt = $conn->prepare("SELECT titulo FROM noticias WHERE id = :noticia_id");
$nStmt->execute([':noticia_id' => $noticia_id]);
$noticia = $nStmt->fetch(PDO::FETCH_OBJ);
$tituloNoticia = $noticia ? $noticia->titulo : "Noticia Desconocida";

// --- OBTENER TODOS LOS COMENTARIOS ---
$cStmt = $conn->prepare("SELECT c.*, u.usuario, u.foto FROM comentarios c 
                         JOIN usuarios u ON c.usuario_id = u.id 
                         WHERE c.noticia_id = :noticia_id 
                         ORDER BY c.fecha DESC");
$cStmt->execute([':noticia_id' => $noticia_id]);
$comentarios = $cStmt->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comentarios - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, #0d0f1c, #202437);
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 3rem;
            display: flex;
            justify-content: center;  
            align-items: center;      
            min-height: 100vh;        
            margin: 0;                
        }

        h2 {
            color: #00f0ff;
            text-shadow: 0 0 6px #00f0ff88;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .subtitle {
            color: #aaa;
            text-align: center;
            margin-bottom: 2.5rem;
            font-size: 1.1rem;
        }

        .noticia-titulo {
            color: #fff;
            font-weight: bold;
            border-bottom: 2px solid #00f0ff33;
            padding-bottom: 4px;
        }

        .comentario-card {
            background-color: #1c1f2e;
            border-radius: 14px;
            box-shadow: 0 0 20px #00f0ff15;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid #252a3a;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .comentario-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px #00f0ff25;
        }

        .avatar {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #00f0ff;
        }

        .info strong {
            color: #00f0ff;
            font-size: 1.05rem;
        }

        /* Bloque interno donde va el texto del comentario */
        .contenido-texto {
            background-color: #131522;
            padding: 0.8rem 1.2rem;
            border-radius: 8px;
            border-left: 3px solid #00f0ff;
            color: #e0e0e0;
            margin-top: 0.5rem;
        }

        .btn-container {
            max-width: 100%;
            margin-top: 2rem;
        }

        .btn-acciones {
            border: 1px solid #555;
            background-color: #333;
            color: #f1f1f1;
            padding: 12px 15px;
            transition: all 0.3s;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
        }

        .btn-acciones:hover {
            background-color: #00f0ff22;
            color: #00f0ff;
            border-color: #00f0ff;
            box-shadow: 0 0 15px #00f0ff88;
        }

        .btn-delete {
            background: transparent;
            border: none;
            color: #ff4d4d;
            transition: color 0.2s, transform 0.2s;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-delete:hover {
            color: #ff1a1a;
            transform: scale(1.15);
        }
    </style>
</head>
<body>
<div class="container" style="max-width: 650px; width: 100%;">
    
    <h2><i class="fa-solid fa-comments"></i> Moderación de Contenido</h2>
    <div class="subtitle">Comentarios de: <span class="noticia-titulo"><?= htmlspecialchars($tituloNoticia) ?></span></div>

    <?php if (empty($comentarios)): ?>
        <div class="text-center my-5 p-4" style="background-color: #1c1f2e; border-radius: 12px; color: #888;">
            <i class="fa-solid fa-comment-slash fa-2x mb-3"></i>
            <p>Esta noticia no tiene comentarios aún.</p>
        </div>
    <?php else: ?>
        <?php foreach ($comentarios as $comentario): ?>
            <div class="comentario-card d-flex justify-content-between align-items-center gap-3">
                
                <div class="d-flex flex-column flex-grow-1">
                    
                    <div class="d-flex align-items-center gap-2">
                        <img class="avatar"
                             src="<?= !empty($comentario->foto) ? htmlspecialchars($comentario->foto) : '/GameNation/assets/img/default-user.png' ?>"
                             alt="avatar">
                        <div class="info">
                            <strong class="d-inline-block"><?= htmlspecialchars($comentario->usuario) ?></strong>
                            <span class="text-muted ms-2" style="font-size: 0.8rem;">
                                <i class="fa-regular fa-clock"></i> <?= $comentario->fecha ?>
                            </span>
                        </div>
                    </div>

                    <div class="contenido-texto">
                        <p class="mb-0" style="font-size: 0.95rem;"><?= nl2br(htmlspecialchars($comentario->contenido)) ?></p>
                    </div>

                </div>
                
                <div class="ms-2">
                    <form method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este comentario?');" class="mb-0">
                        <input type="hidden" name="eliminar_comentario_id" value="<?= $comentario->id ?>">
                        <button type="submit" class="btn-delete" title="Eliminar Comentario">
                            <i class="fa-solid fa-trash-can fa-lg"></i>
                        </button>
                    </form>
                </div>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="btn-container">
        <a href="listar_noticias.php" class="btn btn-acciones"><i class="fa-solid fa-arrow-left"></i> Volver a Noticias</a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>