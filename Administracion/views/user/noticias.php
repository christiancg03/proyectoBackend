<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Noticias - Game Nation</title>
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
            padding: 2.5rem;
            margin-bottom: 2rem;
            text-align: center;
            max-width: 400px;
            margin: 0 auto;
        }

        .btn-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            width: 100%;
        }

        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 12px 15px;
            background-color: #00f0ff;
            color: #000;
            border: none;
            transition: background-color 0.3s, box-shadow 0.3s;
            width: 90%;
            margin: 0 auto;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #00c7e6;
            box-shadow: 0 0 15px #00c7e688;
        }

        .btn-acciones {
            border: 1px solid #555;
            background-color: #333;
            color: #f1f1f1;
            padding: 12px 15px;
            transition: all 0.3s;
            border-radius: 12px;
            width: 90%;
            margin: 0 auto;
        }

        .btn-acciones:hover {
            background-color: #00f0ff22;
            color: #00f0ff;
            border-color: #00f0ff;
            box-shadow: 0 0 15px #00f0ff88;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-info"><i class="fa-solid fa-newspaper"></i> Noticias</h2>

    <div class="card">
        <div class="btn-container">
            <a href="añadir_noticias.php" class="btn"><i class="fa-solid fa-plus"></i> Crear Noticias</a>
            <a href="listar_noticias.php" class="btn btn-acciones"><i class="fa-solid fa-list"></i> Ver Noticias</a>
            <a href="../../index.php" class="btn btn-acciones"><i class="fa-solid fa-arrow-left"></i> Volver al Inicio</a>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
