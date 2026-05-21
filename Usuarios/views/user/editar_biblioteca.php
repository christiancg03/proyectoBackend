<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}

$conn = db::conexion();
$miId = $_SESSION["usuario"]->id;
$mensaje = "";

$juegoId = $_GET["id"] ?? null;
if (!$juegoId) {
    header("Location: listar_biblioteca.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM biblioteca WHERE id = :id AND usuario_id = :usuario_id");
$stmt->execute([":id" => $juegoId, ":usuario_id" => $miId]);
$juego = $stmt->fetch(PDO::FETCH_OBJ);

if (!$juego) {
    header("Location: listar_biblioteca.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST["titulo"]);
    $saga = trim($_POST["saga"]);
    $estado = $_POST["estado"];
    $opinion = trim($_POST["opinion"]);
    $valoracion = (int)$_POST["valoracion"];
    $imagen = $juego->imagen;

    if (!empty($_FILES["imagen"]["name"])) {
        $directorio = __DIR__ . "/../../Usuarios/uploads/";
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $imagenNombre = uniqid("juego_") . "." . pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
        $rutaFisica = $directorio . $imagenNombre;

        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaFisica)) {
            $imagen = "/GameNation/Usuarios/uploads/" . $imagenNombre;
        }
    }

    if ($titulo !== "" && $valoracion >= 1 && $valoracion <= 10) {
        try {
            $stmt = $conn->prepare("UPDATE biblioteca 
                                    SET titulo = :titulo, saga = :saga, estado = :estado, 
                                        opinion = :opinion, valoracion = :valoracion, imagen = :imagen 
                                    WHERE id = :id AND usuario_id = :usuario_id");
            $stmt->execute([
                ":titulo" => $titulo,
                ":saga" => $saga ?: null,
                ":estado" => $estado,
                ":opinion" => $opinion ?: null,
                ":valoracion" => $valoracion,
                ":imagen" => $imagen ?: null,
                ":id" => $juegoId,
                ":usuario_id" => $miId
            ]);
            $mensaje = "<div class='alert alert-success mt-4'>🎮 El videojuego se ha actualizado correctamente.</div>";
        } catch (Exception $e) {
            $mensaje = "<div class='alert alert-danger mt-4'>❌ Error al actualizar el videojuego: " . $e->getMessage() . "</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-danger mt-4'>❌ Debes completar todos los campos correctamente.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Videojuego - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/GameNation/assets/css/dashboard.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #0d0f1c, #202437);
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        h2 {
            color: #00f0ff;
            text-shadow: 0 0 6px #00f0ff88;
            margin-bottom: 2rem;
            text-align: center;
        }
        .form-card {
            background-color: #1c1f2e;
            border-radius: 12px;
            box-shadow: 0 0 20px #00f0ff33;
            padding: 2.2rem 2.2rem 1.5rem 2.2rem;
            margin-bottom: 2rem;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .form-group {
            width: 100%;
            margin-bottom: 1.25rem;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .form-label {
            color: #7ae4ff;
            font-weight: 500;
            margin-bottom: 0.45rem;
            font-size: 1.05rem;
            padding-left: 2px;
        }
        input.form-control, textarea.form-control, select.form-select {
            background-color: #2a2d3e;
            border: 2px solid #555;
            color: #fff;
            border-radius: 5px;
            padding: 12px 13px;
            font-size: 1.05rem;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 0;
            box-shadow: none;
            transition: border-color 0.2s;
        }
        input.form-control:focus, textarea.form-control:focus, select.form-select:focus {
            border-color: #00f0ff;
            outline: none;
            box-shadow: 0 0 8px #00f0ff55;
        }
        textarea.form-control {
            min-height: 90px;
            resize: vertical;
        }
        .btn-guardar {
            border-radius: 5px;
            font-weight: 600;
            padding: 13px 0;
            background-color: #00f0ff;
            color: #0d0f1c;
            border: none;
            width: 100%;
            font-size: 1.13rem;
            box-shadow: 0 2px 12px #00f0ff33;
            transition: all 0.18s;
            margin-top: 0.6rem;
            margin-bottom: 0.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.7em;
        }
        .btn-guardar:hover {
            background-color: #00c7e6;
            color: #fff;
            box-shadow: 0 4px 18px #00f0ff88;
        }
        .btn-outline-light {
            border: 2px solid #00f0ff55;
            background-color: #23263a;
            color: #00f0ff;
            padding: 12px 36px;
            border-radius: 5px;
            margin-top: 1.6rem;
            font-weight: bold;
            font-size: 1.12rem;
            box-shadow: 0 2px 12px #00f0ff33;
            transition: all 0.18s;
            display: inline-flex;
            align-items: center;
            gap: 0.7em;
            text-decoration: none;
        }
        .btn-outline-light:hover {
            background-color: #00f0ff;
            color: #23263a;
            border-color: #00f0ff;
            box-shadow: 0 4px 18px #00f0ff88;
            text-decoration: none;
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
            padding: 15px;
            font-weight: bold;
            text-align: center;
            font-size: 1.08rem;
            box-shadow: 0 0 10px #00f0ff33;
        }
        @media (max-width: 600px) {
            .form-card {
                padding: 1.2rem 0.5rem 1.2rem 0.5rem;
                max-width: 99vw;
            }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-info mb-4"><i class="fa-solid fa-pen"></i> Editar Videojuego</h2>

    <?= $mensaje ?>

    <form method="POST" enctype="multipart/form-data" class="form-card">
        <div class="form-group">
            <label for="titulo" class="form-label"><i class="fa-solid fa-gamepad"></i> Título del juego</label>
            <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Ej: The Legend of Zelda" value="<?= htmlspecialchars($juego->titulo) ?>" required>
        </div>
        <div class="form-group">
            <label for="saga" class="form-label"><i class="fa-solid fa-layer-group"></i> Saga <span style="color:#aaa;">(opcional)</span></label>
            <input type="text" name="saga" id="saga" class="form-control" placeholder="Ej: Zelda, Mario..." value="<?= htmlspecialchars($juego->saga) ?>">
        </div>
        <div class="form-group">
            <label for="estado" class="form-label"><i class="fa-solid fa-star"></i> Estado</label>
            <select name="estado" id="estado" class="form-select">
                <option value="favorito" <?= $juego->estado === 'favorito' ? 'selected' : '' ?>>Favorito</option>
                <option value="completado" <?= $juego->estado === 'completado' ? 'selected' : '' ?>>Completado</option>
                <option value="deseado" <?= $juego->estado === 'deseado' ? 'selected' : '' ?>>Deseado</option>
            </select>
        </div>
        <div class="form-group">
            <label for="opinion" class="form-label"><i class="fa-solid fa-comment-dots"></i> Tu opinión</label>
            <textarea name="opinion" id="opinion" class="form-control" rows="3" placeholder="¿Qué te ha parecido?"><?= htmlspecialchars($juego->opinion) ?></textarea>
        </div>
        <div class="form-group">
            <label for="valoracion" class="form-label"><i class="fa-solid fa-star-half-stroke"></i> Valoración (1-10)</label>
            <input type="number" name="valoracion" id="valoracion" class="form-control" placeholder="Puntuación" min="1" max="10" value="<?= $juego->valoracion ?>">
        </div>
        <div class="form-group">
            <label for="imagen" class="form-label"><i class="fa-solid fa-image"></i> Imagen del videojuego (opcional)</label>
            <input type="file" name="imagen" id="imagen" class="form-control" accept="image/*">
        </div>
        <button class="btn-guardar" type="submit"><i class="fa-solid fa-save"></i> Guardar Cambios</button>
    </form>

    <div class="text-center">
        <a href="listar_biblioteca.php" class="btn btn-outline-light">
            <i class="fa-solid fa-arrow-left"></i> Volver a Biblioteca
        </a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>