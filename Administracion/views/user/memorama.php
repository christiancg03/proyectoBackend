<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Memorama - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <style>
        body {
            background: linear-gradient(to right, #0d0f1c, #202437);
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-align: center;
            padding-top: 50px;
        }

        h1 {
            color: #00f0ff;
            margin-bottom: 2rem;
            text-shadow: 0 0 8px #00f0ff88;
        }

        .contenido {
            display: grid;
            grid-template-columns: repeat(4, 100px);
            grid-gap: 15px;
            justify-content: center;
        }

        .card {
            width: 100px;
            height: 100px;
            background-color: #1c1f2e;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            cursor: pointer;
            box-shadow: 0 0 10px #00f0ff22;
            transition: background-color 0.3s;
        }

        .card.revelada {
            background-color: #00f0ff;
            color: #000;
        }

        .card.agrupar {
            background-color: #28a745;
            color: #fff;
            pointer-events: none;
        }

        .btn-volver {
            border: 1px solid #555;
            background-color: #333;
            color: #f1f1f1;
            padding: 10px 20px;
            transition: all 0.3s ease;
            border-radius: 12px;
            margin-top: 2rem;
            text-decoration: none;
            display: inline-block;
        }

        .btn-volver:hover {
            background-color: #00f0ff22;
            color: #00f0ff;
            border-color: #00f0ff;
            box-shadow: 0 0 15px #00f0ff88;
        }
        @media (max-width: 600px) {
    .contenido {
        grid-template-columns: repeat(4, 60px);
        grid-gap: 10px;
    }
    .card {
        width: 60px;
        height: 60px;
        font-size: 1.3rem;
    }
}
    </style>
</head>
<body>

<h1><i class="fa-solid fa-brain"></i> Juego Memorama</h1>

<div class="contenido" id="contenido"></div>

<a href="/GameNation/Administracion/views/user/minijuegos.php" class="btn btn-volver mt-4">
    <i class="fa-solid fa-arrow-left"></i> Volver a Minijuegos
</a>

<script>
    const emojis = ['🎮', '🕹️', '👾', '🧩', '🎲', '💣', '🧠', '⚔️'];
    const contenido = document.getElementById('contenido');
    let cards = [];
    let seleccion = [];

    function crearCartas() {
        const mix = [...emojis, ...emojis].sort(() => 0.5 - Math.random());

        mix.forEach((icon, index) => {
            const card = document.createElement('div');
            card.classList.add('card');
            card.dataset.icon = icon;
            card.dataset.index = index;
            card.addEventListener('click', () => vueltaCarta(card));
            contenido.appendChild(card);
            cards.push(card);
        });
    }

    function vueltaCarta(card) {
        if (card.classList.contains('revelada') || card.classList.contains('agrupar') || seleccion.length === 2) return;

        card.classList.add('revelada');
        card.textContent = card.dataset.icon;
        seleccion.push(card);

        if (seleccion.length === 2) {
            setTimeout(checkMatch, 700);
        }
    }

    function checkMatch() {
        const [first, second] = seleccion;

        if (first.dataset.icon === second.dataset.icon) {
            first.classList.add('agrupar');
            second.classList.add('agrupar');
        } else {
            first.classList.remove('revelada');
            second.classList.remove('revelada');
            first.textContent = '';
            second.textContent = '';
        }

        seleccion = [];

        if (document.querySelectorAll('.agrupar').length === cards.length) {
            setTimeout(() => {
                alert("🎉 ¡Has completado el memorama!");
            }, 500);
        }
    }

    crearCartas();
</script>
</body>
</html>