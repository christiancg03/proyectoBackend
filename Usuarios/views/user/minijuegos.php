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
    <title>Minijuegos - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/GameNation/assets/css/dashboard.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');
        body {
            background: #000;
            color: #fff;
            font-family: 'Press Start 2P', cursive;
            padding-bottom: 2rem;
            margin: 0;
        }
        #particles-js {
            position: fixed;
            width: 100vw;
            height: 100vh;
            z-index: 0;
            top: 0;
            left: 0;
            background: #0d0f1c;
        }
        .titulo-arcade, .contenido-arcade, .btn-inicio {
            position: relative;
            z-index: 1;
        }
        .titulo-arcade {
            color: #f4e04d;
            font-size: 3rem;
            text-align: center;
            text-shadow: 0 0 8px #f4e04d, 0 0 16px #f4e04d;
            margin-bottom: 2rem;
        }
        .contenido-arcade {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 0 20px;
        }
        .minijuego {
            width: 280px;
            background-color: #333;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 0 20px #f4e04d;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        .minijuego:hover {
            transform: scale(1.05);
            box-shadow: 0 0 30px #f4e04d, 0 0 60px #f4e04d;
        }
        .minijuego img {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .minijuego h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #f4e04d;
        }
        .minijuego p {
            font-size: 0.9rem;
            color: #ccc;
        }
        .btn-inicio {
            display: block;
            margin: 30px auto 0;
            font-size: 1rem;
            background-color: #f4e04d;
            color: #000;
            border-radius: 8px;
            border: none;
            padding: 10px 30px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-inicio:hover {
            background-color: #f2b705;
        }
        img{
            width: 20%;
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>
    <h1 class="titulo-arcade">🎮 Arcade - Game Nation</h1>
    <div class="contenido-arcade">
        <div class="minijuego" onclick="location.href='quiz.php'">
            <img src="/GameNation/Usuarios/assets/img/quiz.png" alt="Quiz">
            <h3>📝 Quiz</h3>
            <p>Desafía tu conocimiento sobre videojuegos y gana puntos.</p>
        </div>
        <div class="minijuego" onclick="location.href='memorama.php'">
            <img src="/GameNation/Usuarios/assets/img/memorama.png" alt="Memorama">
            <h3>🧠 Memorama</h3>
            <p>Pon a prueba tu memoria con cartas temáticas de videojuegos.</p>
        </div>
        <div class="minijuego" onclick="location.href='ahorcado.php'">
            <img src="/GameNation/Usuarios/assets/img/ahorcado.png" alt="Ahorcado">
            <h3>⚡ Ahorcado</h3>
            <p>Un ahorcado de toda la vida pero relacionado con el mundo Gaming.</p>
        </div>
        <div class="minijuego" onclick="location.href='peleas.php'">
            <img src="/GameNation/Usuarios/assets/img/peleas.png" alt="Peleas">
            <h3>⚔️ Peleas</h3>
            <p>Enfréntate a otros jugadores en combates épicos.</p>
        </div>
    </div>
    <button class="btn-inicio" onclick="location.href='../../index.php'">
        ⬅️ Volver al Inicio
    </button>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    
</body>
</html>