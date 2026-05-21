<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi Biblioteca - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #0d0f1c, #202437);
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 2rem;
        }
        h2 {
            color: #00f0ff;
            text-shadow: 0 0 6px #00f0ff88;
            margin-bottom: 2rem;
            text-align: center;
        }
        .card {
            background-color: #1c1f2e;
            border-radius: 15px;
            box-shadow: 0 0 20px #00f0ff33;
            padding: 2.5rem 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            max-width: 400px;
            margin: 0 auto;
        }
        .contenedor-principal {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            width: 100%;
        }
        .btn-biblio {
            border-radius: 12px;
            font-weight: 600;
            padding: 14px 15px;
            background-color: #00f0ff;
            color: #000;
            border: none;
            transition: background-color 0.3s, box-shadow 0.3s;
            width: 90%;
            margin: 0 auto;
            text-decoration: none;
            font-size: 1.13rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.7em;
        }
        .btn-biblio i {
            font-size: 1.25em;
        }
        .btn-biblio:hover {
            background-color: #00c7e6;
            box-shadow: 0 0 15px #00c7e688;
            color: #fff;
        }
        .btn-volver {
            border: 1px solid #555;
            background-color: #333;
            color: #f1f1f1;
            padding: 14px 15px;
            transition: all 0.3s;
            border-radius: 12px;
            width: 90%;
            margin: 0 auto;
            font-size: 1.13rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.7em;
            text-decoration: none;
        }
        .btn-volver i {
            font-size: 1.25em;
        }
        .btn-volver:hover {
            background-color: #00f0ff22;
            color: #00f0ff;
            border-color: #00f0ff;
            box-shadow: 0 0 15px #00f0ff88;
        }
        @media (max-width: 600px) {
            .card {
                padding: 1.3rem 0.4rem;
                max-width: 98vw;
            }
            .btn-biblio, .btn-volver {
                width: 100%;
                font-size: 1.05rem;
                padding: 12px 0;
            }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-info"><i class="fa-solid fa-gamepad"></i> Mi Biblioteca</h2>

    <div class="card">
        <div class="contenedor-principal">
            <a href="listar_biblioteca.php" class="btn-biblio">
                <i class="fa-solid fa-list"></i> Listar Videojuegos
            </a>
            <a href="añadir_biblioteca.php" class="btn-biblio">
                <i class="fa-solid fa-circle-plus"></i> Añadir Videojuego
            </a>
            <a href="../../index.php" class="btn-volver">
                <i class="fa-solid fa-arrow-left"></i> Volver al Inicio
            </a>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>