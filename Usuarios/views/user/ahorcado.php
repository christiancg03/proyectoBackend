<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Palabra Gamer - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #0d0f1c, #202437);
            color: #fff;
            font-family: 'Press Start 2P', cursive;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .titulo-juego {
            color: #00f0ff;
            text-shadow: 0 0 10px #00f0ff;
            margin: 2rem 0;
            font-size: 2.5rem;
        }
        .game-container {
            background: #1c1f2e;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0 30px #00f0ff55;
            text-align: center;
            margin: 1rem;
        }
        .imagen-verdugo {
            max-width: 160px;
            margin: 1rem auto;
            display: block;
            filter: drop-shadow(0 0 5px #00f0ff);
        }
        .ver-palabra {
            font-size: 2rem;
            letter-spacing: 1rem;
            margin: 2rem 0;
            color: #7ae4ff;
        }
        .teclado {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 0.5rem;
            max-width: 600px;
            margin: 1rem auto;
        }
        .key {
            background: #2a2d3e;
            border: 2px solid #00f0ff;
            color: #00f0ff;
            padding: 1rem;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.2s;
            font-family: inherit;
            font-size: 1rem;
        }
        .key:hover:not(.disabled) {
            background: #00f0ff33;
            transform: scale(1.1);
        }
        .disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .intentos {
            color: #ff6b6b;
            margin: 1rem 0;
            font-size: 1.2rem;
        }
        .game-over {
            color: #ff6b6b;
            text-shadow: 0 0 10px #ff6b6b;
            font-size: 2.2rem;
            margin-top: 1.2rem;
        }
        .partida-ganada {
            color: #00ffae;
            text-shadow: 0 0 10px #00ffae;
            font-size: 2.2rem;
            margin-top: 1.2rem;
        }
        .reiniciar-partida {
            background: #00f0ff;
            color: #000;
            border: none;
            padding: 1rem 2rem;
            font-family: inherit;
            border-radius: 8px;
            margin: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.1rem;
        }
        .reiniciar-partida:hover {
            transform: scale(1.1);
            box-shadow: 0 0 20px #00f0ff;
        }
        .pista {
            background: #222d3d;
            color: #00f0ff;
            border-radius: 8px;
            font-size: 1.1rem;
            font-family: inherit;
            padding: 0.6rem 1.2rem;
            margin-bottom: 1.3rem;
            margin-top: 0.4rem;
            display: inline-block;
            letter-spacing: 0.5px;
            box-shadow: 0 0 10px #00f0ff22;
        }
        .boton-volver {
            display: block;
            margin: 2.5rem auto 1rem auto;
            font-size: 1.1rem;
            background-color: #00f0ff;
            color: #222d3d;
            border-radius: 8px;
            border: none;
            padding: 12px 32px;
            cursor: pointer;
            font-family: inherit;
            font-weight: bold;
            text-shadow: 0 0 10px #00f0ff55;
            transition: background-color 0.3s, color 0.3s, box-shadow 0.3s;
        }
        .boton-volver:hover {
            background-color: #00c7e6;
            color: #fff;
            box-shadow: 0 0 20px #00f0ff;
        }
        @media (max-width: 768px) {
            .teclado {
                grid-template-columns: repeat(5, 1fr);
            }
            .game-container {
                padding: 1rem;
            }
        }
        @import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');
    </style>
</head>
<body>
    <h1 class="titulo-juego">🎮 PALABRA GAMER</h1>
    <div class="game-container">
        <div class="pista" id="pista"></div>
        <img src="https://cdn.pixabay.com/photo/2014/04/03/10/32/hangman-312496_1280.png" class="imagen-verdugo" id="imagenVerdugo" alt="Ahorcado">
        <div class="ver-palabra" id="verPalabra"></div>
        <div class="intentos" id="intentos">Intentos restantes: <span id="conteointentos">6</span></div>
        <div class="teclado" id="teclado"></div>
        <div id="mensajeJuego"></div>
    </div>
    <button class="boton-volver" onclick="location.href='minijuegos.php'">
        ⬅️ Volver a Minijuegos
    </button>
    <script>
        // Dos categorías de palabras
        const palabras_personajes = [
            'MARIO', 'ZELDA', 'SONIC', 'KIRBY', 'ASTARION', 'KRATOS', 'SAMUS', 'RAYMAN', 'PACMAN', 'DONKEYKONG', 'CLOUD', 'SEPHIROTH', 'LUIGI', 'PEACH', 'BOWSER', 'DOOMSLAYER', 'MEGAMAN', 'GHOST', 'DANTE', 'TREVOR', 'YOSHI', 'GERALT', 'ARTHURMORGAN', 'SNAKE', 'KEN', 'RYU', 'PIKACHU'
        ];
        const palabras_videojuegos = [
            'TETRIS', 'HALO', 'MINECRAFT', 'POKEMON', 'METROID', 'FORTNITE', 'ZELDA', 'DOOM', 'PORTAL', 'OVERWATCH', 'FIFA', 'AMONGUS', 'VALORANT', 'GTA', 'SKYRIM', 'BALDURSGATE', 'METALSLUG', 'DARKSOULS', 'BATTLEFIELD', 'COD', 'DYINGLIGHT', 'THEWITCHER', 'BULLY', 'REPO', 'SEKIRO', 'BLOODBORNE', 'SIMS', 'METALGEAR'
        ];

        let palabraSecreta;
        let letrasAdivinadas;
        let intentosRestantes;
        let juegoTerminado = false;
        let categoria = '';

        function iniciarPartida() {
            // Elegir categoría al azar
            if (Math.random() < 0.5) {
                categoria = 'personaje';
                palabraSecreta = palabras_personajes[Math.floor(Math.random() * palabras_personajes.length)];
                document.getElementById('pista').textContent = 'Pista: Es un Personaje de Videojuegos';
            } else {
                categoria = 'videojuego';
                palabraSecreta = palabras_videojuegos[Math.floor(Math.random() * palabras_videojuegos.length)];
                document.getElementById('pista').textContent = 'Pista: Es un Videojuego';
            }
            letrasAdivinadas = Array(palabraSecreta.length).fill('_');
            intentosRestantes = 6;
            juegoTerminado = false;
            document.getElementById('verPalabra').textContent = letrasAdivinadas.join(' ');
            document.getElementById('conteointentos').textContent = intentosRestantes;
            document.getElementById('mensajeJuego').textContent = '';
            document.getElementById('imagenVerdugo').src = "https://cdn.pixabay.com/photo/2014/04/03/10/32/hangman-312496_1280.png";
            // Generar teclado
            const teclado = document.getElementById('teclado');
            teclado.innerHTML = '';
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('').forEach(letra => {
                const button = document.createElement('button');
                button.className = 'key';
                button.textContent = letra;
                button.addEventListener('click', () => adivinar(letra, button));
                teclado.appendChild(button);
            });
        }

        function adivinar(letra, button) {
            if (juegoTerminado || button.classList.contains('disabled')) return;
            button.classList.add('disabled');
            let acierto = false;
            for (let i = 0; i < palabraSecreta.length; i++) {
                if (palabraSecreta[i] === letra) {
                    letrasAdivinadas[i] = letra;
                    acierto = true;
                }
            }
            document.getElementById('verPalabra').textContent = letrasAdivinadas.join(' ');
            if (!acierto) {
                intentosRestantes--;
                document.getElementById('conteointentos').textContent = intentosRestantes;
            }
            resultadoPartida();
        }

        function resultadoPartida() {
            if (letrasAdivinadas.join('') === palabraSecreta) {
                juegoTerminado = true;
                document.getElementById('mensajeJuego').innerHTML =
                    '<div class="partida-ganada">¡Enhorabuena, Has Ganado! 🎉</div>' +
                    '<button class="reiniciar-partida" onclick="iniciarPartida()">Jugar de nuevo</button>';
            } else if (intentosRestantes === 0) {
                juegoTerminado = true;
                document.getElementById('mensajeJuego').innerHTML =
                    `<div class="game-over">¡HAS PERDIDO! 😵<br>La palabra era: <span style="color:#00f0ff">${palabraSecreta}</span></div>` +
                    '<button class="reiniciar-partida" onclick="iniciarPartida()">Jugar de nuevo</button>';
            }
        }

        // Iniciar el juego al cargar
        iniciarPartida();
    </script>
</body>
</html>