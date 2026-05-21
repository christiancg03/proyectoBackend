<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}

$conn = db::conexion();
$usuario = $_SESSION["usuario"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre_completo = $_POST["nombre_completo"];
    $email = $_POST["email"];
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $genero = $_POST["genero"];
    $pais = $_POST["pais"];
    $biografia = $_POST["biografia"];

    $foto = $usuario->foto;

    if (!empty($_FILES["foto"]["name"])) {
        $directorio = __DIR__ . "/../../uploads/";
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $fotoNombre = uniqid("perfil_") . "." . pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $rutaFinal = $directorio . $fotoNombre;

        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $rutaFinal)) {
            $foto = "/GameNation/Administracion/uploads/" . $fotoNombre; // ESTA es la URL que se guarda en la base de datos
        } else {
            echo "Error al mover la imagen.";
        }
    }

    $sql = "UPDATE usuarios SET nombre_completo = :nombre_completo, email = :email, foto = :foto,
            fecha_nacimiento = :fecha_nacimiento, genero = :genero, pais = :pais, biografia = :biografia
            WHERE id = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":nombre_completo" => $nombre_completo,
        ":email" => $email,
        ":foto" => $foto,
        ":fecha_nacimiento" => $fecha_nacimiento,
        ":genero" => $genero,
        ":pais" => $pais,
        ":biografia" => $biografia,
        ":id" => $usuario->id
    ]);

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute([":id" => $usuario->id]);
    $_SESSION["usuario"] = $stmt->fetch(PDO::FETCH_OBJ);
    $msg = "Perfil actualizado correctamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perfil de Usuario - Game Nation</title>
    <link href="/GameNation/Administracion/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, #0d0f1c, #202437);
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .contenido {
            background-color: #1c1f2e;
            border-radius: 12px;
            box-shadow: 0 0 20px #00f0ff33;
            border: 1px solid #2a2d3f;
            padding: 2rem;
        }
        .img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            box-shadow: 0 0 12px #00f0ff55;
            border: 2px solid #00f0ff;
        }
        .btn-guardar {
            background-color: #00f0ff;
            color: #0d0f1c;
            border: none;
            font-weight: bold;
            padding: 10px 24px;
            border-radius: 10px;
            transition: all 0.2s;
        }
        .btn-guardar:hover {
            background-color: #00c7e6;
            box-shadow: 0 0 12px #00f0ff88;
        }
        .form-control-custom, .form-select-custom, .textarea-custom {
            background-color: #2a2d3f;
            color: #f1f1f1;
            border: 1px solid #444;
            border-radius: 8px;
        }
        .form-control-custom:focus, .form-select-custom:focus, .textarea-custom:focus {
            background-color: #2a2d3f;
            color: #f1f1f1;
            border-color: #00f0ff;
            box-shadow: 0 0 8px #00f0ff55;
        }
        .form-label-custom {
            color: #00f0ff;
            text-shadow: 0 0 8px #00f0ff33;
        }
        .alert-custom {
            background-color: #28a745;
            color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px #28a74555;
        }
        @media (max-width: 768px) {
            .img {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="contenido mx-auto" style="max-width: 900px;">
        <h2 class="mb-4 text-center text-info"><i class="fa-solid fa-user"></i> Perfil de <?= $_SESSION["usuario"]->usuario ?></h2>

        <?php if (isset($msg)): ?>
            <div class="alert alert-custom text-center mb-4"><?= $msg ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                <div class="col-md-4 text-center mb-4">
                    <img src="<?= $_SESSION["usuario"]->foto ?>" alt="Foto" class="img mb-3">
                    <div class="mt-2">
                        <label for="foto" class="form-label-custom mb-2 d-block">Cambiar imagen</label>
                        <input type="file" name="foto" class="form-control-custom form-control" accept="image/*">
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label-custom">Nombre completo</label>
                        <input type="text" name="nombre_completo" class="form-control-custom form-control" value="<?= $_SESSION["usuario"]->nombre_completo ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Correo electrónico</label>
                        <input type="email" name="email" class="form-control-custom form-control" value="<?= $_SESSION["usuario"]->email ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Fecha de nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control-custom form-control" value="<?= $_SESSION["usuario"]->fecha_nacimiento ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Género</label>
                        <select name="genero" class="form-select-custom form-select">
                            <option value="">Selecciona...</option>
                            <option value="masculino" <?= $_SESSION["usuario"]->genero == "masculino" ? "selected" : "" ?>>Masculino</option>
                            <option value="femenino" <?= $_SESSION["usuario"]->genero == "femenino" ? "selected" : "" ?>>Femenino</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">País</label>
                        <input type="text" name="pais" class="form-control-custom form-control" value="<?= $_SESSION["usuario"]->pais ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Biografía</label>
                        <textarea name="biografia" class="textarea-custom form-control" rows="3"><?= $_SESSION["usuario"]->biografia ?></textarea>
                    </div>

                    <div class="d-flex justify-content-start gap-3">
                        <button type="submit" class="btn btn-guardar"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button>
                        <a href="../../index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>