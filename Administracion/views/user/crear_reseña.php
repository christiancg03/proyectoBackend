<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}

$conn = db::conexion();
$mensaje = "";

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
    $imagen = null;

    if (!empty($_FILES["imagen"]["name"])) {
        $directorio = __DIR__ . "/../../Administracion/uploads/";
        if (!is_dir($directorio)) mkdir($directorio, 0755, true);
        $imagenNombre = uniqid("resena_") . "." . pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
        $rutaFisica = $directorio . $imagenNombre;
        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaFisica)) {
            $imagen = "/GameNation/Administracion/uploads/" . $imagenNombre;
        }
    }

    if ($videojuego !== "" && $puntuacion >= 1 && $puntuacion <= 10 && $opinion !== "") {
        try {
            $stmt = $conn->prepare("
                INSERT INTO resenas (usuario_id, videojuego, opinion, puntuacion, imagen,
                    fecha_lanzamiento, desarrollo, produccion, distribucion, precio, jugadores,
                    formato, textos, voces, online, fecha_publicacion)
                VALUES (:usuario_id, :videojuego, :opinion, :puntuacion, :imagen,
                    :fecha_lanzamiento, :desarrollo, :produccion, :distribucion, :precio, :jugadores,
                    :formato, :textos, :voces, :online, NOW())
            ");
            $stmt->execute([
                ":usuario_id" => $_SESSION["usuario"]->id,
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
                ":online" => $online ?: null
            ]);
            $mensaje = "<div class='alert alert-success'>✅ Reseña creada correctamente.</div>";
        } catch (Exception $e) {
            $mensaje = "<div class='alert alert-danger'>❌ Error al crear la reseña: " . $e->getMessage() . "</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-danger'>❌ Debes completar todos los campos obligatorios correctamente.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear Reseña - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, #0d0f1c, #202437);
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 2rem;
        }
        .form-container {
            max-width: 700px;
            margin: 2.5rem auto;
            background: linear-gradient(135deg, #1c1f2e 0%, #23263a 100%);
            border-radius: 18px;
            box-shadow: 0 6px 32px #00f0ff33;
            padding: 2.5rem 2rem 2rem 2rem;
            border-left: 6px solid #00f0ff;
        }
        h2 {
            color: #00f0ff;
            text-shadow: 0 0 6px #00f0ff88;
            margin-bottom: 2rem;
            text-align: center;
        }
        .form-label {
            color: #00f0ff;
            font-weight: bold;
            margin-bottom: 0.4rem;
            letter-spacing: 0.03em;
        }
        .form-group {
            margin-bottom: 1.15rem;
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
            width: 100%;
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
        .btn-info {
            background-color: #00f0ff;
            color: #23263a;
            border: none;
            font-weight: bold;
            border-radius: 10px;
            padding: 10px 24px;
            margin-top: 1rem;
            box-shadow: 0 2px 10px #00f0ff22;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .btn-info:hover {
            background-color: #00c7e6;
            box-shadow: 0 0 12px #00f0ff88;
        }
        .btn-volver {
            background-color: #444;
            color: #f1f1f1;
            border: none;
            border-radius: 10px;
            padding: 10px 24px;
            margin-left: 10px;
            text-decoration: none;
        }
        .btn-volver:hover {
            background-color: #666;
        }
        @media (max-width: 900px) {
            .form-container { padding: 1.2rem 0.5rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="form-container">
        <h2 class="mb-4 text-info"><i class="fa-solid fa-plus"></i> Crear Reseña</h2>
        <?= $mensaje ?>
        <form method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="form-group">
                <label class="form-label">Videojuego</label>
                <input type="text" name="videojuego" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Puntuación (1-10)</label>
                <input type="number" name="puntuacion" class="form-control" min="1" max="10" required>
            </div>
            <div class="form-group">
                <label class="form-label">Opinión</label>
                <textarea name="opinion" class="form-control" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Imagen</label>
                <input type="file" name="imagen" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">Fecha de lanzamiento</label>
                <input type="date" name="fecha_lanzamiento" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Desarrollo</label>
                <input type="text" name="desarrollo" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Producción</label>
                <input type="text" name="produccion" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Distribución</label>
                <input type="text" name="distribucion" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Precio</label>
                <input type="text" name="precio" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Jugadores</label>
                <input type="text" name="jugadores" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Formato</label>
                <input type="text" name="formato" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Textos</label>
                <input type="text" name="textos" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Voces</label>
                <input type="text" name="voces" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Online</label>
                <input type="text" name="online" class="form-control">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-info"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
                <a href="reseñas.php" class="btn btn-volver"><i class="fa-solid fa-arrow-left"></i> Volver a Reseñas</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>