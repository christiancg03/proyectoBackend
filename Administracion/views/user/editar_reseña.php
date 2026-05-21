<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}

$conn = db::conexion();
$mensaje = "";

$resenaId = $_GET["id"] ?? null;
if (!$resenaId) {
    header("Location: reseñas.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM resenas WHERE id = :id");
$stmt->execute([":id" => $resenaId]);
$resena = $stmt->fetch(PDO::FETCH_OBJ);

if (!$resena) {
    header("Location: reseñas.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $videojuego = trim($_POST["videojuego"]);
    $opinion = trim($_POST["opinion"]);
    $puntuacion = (int)$_POST["puntuacion"];
    $fecha_lanzamiento = trim($_POST["fecha_lanzamiento"]);
    $desarrollo = trim($_POST["desarrollo"]);
    $produccion = trim($_POST["produccion"]);
    $distribucion = trim($_POST["distribucion"]);
    $precio = trim($_POST["precio"]);
    $jugadores = trim($_POST["jugadores"]);
    $formato = trim($_POST["formato"]);
    $textos = trim($_POST["textos"]);
    $voces = trim($_POST["voces"]);
    $online = trim($_POST["online"]);
    $imagen = $resena->imagen;

    if (!empty($_FILES["imagen"]["name"])) {
        $directorio = __DIR__ . "/../../Administracion/uploads/";
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }
        $imagenNombre = uniqid("resena_") . "." . pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
        $rutaFisica = $directorio . $imagenNombre;

        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaFisica)) {
            $imagen = "/GameNation/Administracion/uploads/" . $imagenNombre;
        }
    }

    if ($videojuego !== "" && $puntuacion >= 1 && $puntuacion <= 10 && $opinion !== "") {
        try {
            $stmt = $conn->prepare("UPDATE resenas 
                SET videojuego = :videojuego, opinion = :opinion, puntuacion = :puntuacion, imagen = :imagen,
                    fecha_lanzamiento = :fecha_lanzamiento, desarrollo = :desarrollo, produccion = :produccion,
                    distribucion = :distribucion, precio = :precio, jugadores = :jugadores, formato = :formato,
                    textos = :textos, voces = :voces, online = :online
                WHERE id = :id");
            $stmt->execute([
                ":videojuego" => $videojuego,
                ":opinion" => $opinion,
                ":puntuacion" => $puntuacion,
                ":imagen" => $imagen ?: null,
                ":fecha_lanzamiento" => $fecha_lanzamiento ?: null,
                ":desarrollo" => $desarrollo ?: null,
                ":produccion" => $produccion ?: null,
                ":distribucion" => $distribucion ?: null,
                ":precio" => $precio ?: null,
                ":jugadores" => $jugadores ?: null,
                ":formato" => $formato ?: null,
                ":textos" => $textos ?: null,
                ":voces" => $voces ?: null,
                ":online" => $online ?: null,
                ":id" => $resenaId
            ]);
            $mensaje = "<div class='alert alert-success mt-4'>⭐ La reseña se ha actualizado correctamente.</div>";
        } catch (Exception $e) {
            $mensaje = "<div class='alert alert-danger mt-4'>❌ Error al actualizar la reseña: " . $e->getMessage() . "</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-danger mt-4'>❌ Debes completar todos los campos obligatorios correctamente.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Reseña - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, #0d0f1c, #202437);
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        h2 {
            color: #00f0ff;
            text-shadow: 0 0 6px #00f0ff88;
            margin-bottom: 2.2rem;
            text-align: center;
        }
        .alert {
            border-radius: 10px;
            font-size: 1rem;
        }
        .form-label {
            color: #7ae4ff;
            font-weight: 500;
            margin-bottom: 0.45rem;
            font-size: 1.05rem;
            padding-left: 2px;
        }
        .form-group {
            width: 100%;
            margin-bottom: 1.25rem;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .form-control, .form-select {
            background-color: #222d3d;
            color: #f1f1f1;
            border: 1.5px solid #00f0ff55;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 1.08rem;
            box-shadow: 0 1px 6px #00f0ff11;
            transition: border 0.2s, box-shadow 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #00f0ff;
            box-shadow: 0 0 10px #00f0ff55;
            background-color: #222d3d;
            color: #f1f1f1;
        }
        textarea.form-control {
            min-height: 90px;
            resize: vertical;
        }
        .img-actual {
            max-width: 220px;
            max-height: 120px;
            border-radius: 10px;
            margin-bottom: 1rem;
            box-shadow: 0 0 10px #00f0ff44;
            display: block;
        }
        .text-muted {
            color: #aaa !important;
        }
        .btn-info {
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
        .btn-info:hover {
            background-color: #00c7e6;
            box-shadow: 0 0 12px #00f0ff88;
        }
        .btn-volver {
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
        .btn-volver:hover {
            background-color: #666;
        }
        @media (max-width: 600px) {
            .form-container { padding: 1.2rem 0.5rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="form-container">
        <h2 class="mb-4 text-info"><i class="fa-solid fa-pen-to-square"></i> Editar Reseña</h2>
        <?= $mensaje ?>
        <form method="POST" enctype="multipart/form-data" autocomplete="off" class="form-card">
            <div class="form-group">
                <label class="form-label">Videojuego</label>
                <input type="text" name="videojuego" class="form-control" value="<?= htmlspecialchars($resena->videojuego) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Puntuación (1-10)</label>
                <input type="number" name="puntuacion" class="form-control" min="1" max="10" value="<?= htmlspecialchars($resena->puntuacion) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Opinión</label>
                <textarea name="opinion" class="form-control" rows="8" required><?= htmlspecialchars($resena->opinion) ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Imagen actual</label><br>
                <?php if ($resena->imagen): ?>
                    <img src="<?= htmlspecialchars($resena->imagen) ?>" class="img-actual" alt="Imagen actual">
                <?php else: ?>
                    <span class="text-muted">Sin imagen</span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label class="form-label">Cambiar imagen</label>
                <input type="file" name="imagen" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">Fecha de lanzamiento</label>
                <input type="date" name="fecha_lanzamiento" class="form-control" value="<?= htmlspecialchars($resena->fecha_lanzamiento) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Desarrollo</label>
                <input type="text" name="desarrollo" class="form-control" value="<?= htmlspecialchars($resena->desarrollo) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Producción</label>
                <input type="text" name="produccion" class="form-control" value="<?= htmlspecialchars($resena->produccion) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Distribución</label>
                <input type="text" name="distribucion" class="form-control" value="<?= htmlspecialchars($resena->distribucion) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Precio</label>
                <input type="text" name="precio" class="form-control" value="<?= htmlspecialchars($resena->precio) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Jugadores</label>
                <input type="text" name="jugadores" class="form-control" value="<?= htmlspecialchars($resena->jugadores) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Formato</label>
                <input type="text" name="formato" class="form-control" value="<?= htmlspecialchars($resena->formato) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Textos</label>
                <input type="text" name="textos" class="form-control" value="<?= htmlspecialchars($resena->textos) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Voces</label>
                <input type="text" name="voces" class="form-control" value="<?= htmlspecialchars($resena->voces) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Online</label>
                <input type="text" name="online" class="form-control" value="<?= htmlspecialchars($resena->online) ?>">
            </div>
            <button type="submit" class="btn btn-info"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button>
            <a href="reseñas.php" class="btn btn-volver"><i class="fa-solid fa-arrow-left"></i> Volver a reseñas</a>
        </form>
    </div>
</div>
</body>
</html>