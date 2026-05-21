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
    <title>Combate - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');
        body {
            background-color: #0d0f1c;
            color: #f1f1f1;
            font-family: 'Press Start 2P', cursive;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
        }
        .arena {
            width: 1000px;
            height: 400px;
            background-image: url('/GameNation/Administracion/assets/img/arena.jpg');
            background-size: cover;
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 0 20px #00f0ff88;
        }
        .luchador {
            width: 120px;
            height: 190px;
            border-radius: 10px;
            position: absolute;
            bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            transition: all 0.2s;
            background: rgba(0,0,0,0.18);
        }
        .luchador.uno {
            left: 50px;
            background-color: #f4e04d22;
        }
        .luchador.dos {
            right: 50px;
            background-color: #ff573322;
        }
        .luchador-img {
            width: 80px;
            height: 80px;
            margin-bottom: 6px;
            object-fit: contain;
            border-radius: 12px;
            background: #181e2c;
            box-shadow: 0 0 10px #00f0ff55;
        }
        .luchador-label {
            font-size: 1.1em;
            font-weight: bold;
            color: #00f0ff;
            text-shadow: 0 0 8px #00f0ff88;
            margin-bottom: 12px;
        }
        .barra-vida {
            width: 400px;
            height: 30px;
            background-color: #333;
            border-radius: 15px;
            margin: 20px;
            box-shadow: 0 0 10px #00f0ff88;
        }
        .vida {
            height: 100%;
            border-radius: 15px;
            background-color: #28a745;
            transition: width 0.5s;
        }
        .controles {
            margin-top: 30px;
            display: flex;
            gap: 20px;
        }
        .controles button {
            padding: 10px 20px;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            background-color: #00f0ff;
            color: #000;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .controles button:hover:not(:disabled) {
            background-color: #00c7e6;
        }
        .controles button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #555;
        }
        .mensaje-box {
            margin-top: 20px;
            min-height: 40px;
            color: #00f0ff;
            font-size: 1.1em;
            text-shadow: 0 0 10px #00f0ff;
            text-align: center;
        }
        .btn-volver, .btn-reiniciar {
            margin-top: 35px;
            padding: 12px 30px;
            border-radius: 12px;
            background-color: #00f0ff22;
            color: #00f0ff;
            border: 2px solid #00f0ff55;
            font-weight: bold;
            text-decoration: none;
            font-size: 1.1rem;
            box-shadow: 0 0 10px #00f0ff22;
            transition: all 0.3s ease;
            display: inline-block;
        }
        .btn-volver:hover, .btn-reiniciar:hover {
            background-color: #00f0ff44;
            color: #fff;
            border-color: #00f0ff;
            box-shadow: 0 0 15px #00f0ff88;
            text-decoration: none;
        }
        .seleccionar-clase-container {
            margin: 30px 0 15px 0;
            background: #1d1f2f;
            padding: 24px 28px 18px 28px;
            border-radius: 15px;
            box-shadow: 0 0 10px #00f0ff22;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .seleccionar-clase-titulo {
            color: #7ae4ff;
            font-size: 1.1em;
            margin-bottom: 18px;
            text-align: center;
            font-weight: bold;
            letter-spacing: 1px;
        }
        #classButtons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 18px;
            margin-bottom: 12px;
        }
        .class-btn {
            padding: 12px 16px 10px 16px;
            margin: 0;
            border-radius: 12px;
            border: 2px solid #00f0ff55;
            background: #222941;
            color: #00f0ff;
            font-weight: bold;
            font-size: 1.05em;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 120px;
            min-height: 110px;
            box-shadow: 0 0 8px #00f0ff22;
        }
        .class-btn.selected, .class-btn:hover {
            background: #00f0ff44;
            color: #fff;
            border-color: #00f0ff;
            box-shadow: 0 0 12px #00f0ff88;
        }
        .class-btn img {
            width: 48px;
            height: 48px;
            margin-bottom: 7px;
            object-fit: contain;
            border-radius: 8px;
            background: #181e2c;
            box-shadow: 0 0 7px #00f0ff44;
        }
        .class-btn span {
            font-size: 1em;
        }
        .class-info {
            font-size: 0.95em;
            color: #ccc;
            margin-top: 10px;
            text-align: center;
        }
        .tabla-vs {
            margin: 20px auto 0 auto;
            border-collapse: collapse;
            font-size: 0.95em;
        }
        .tabla-vs th, .tabla-vs td {
            border: 1px solid #00f0ff44;
            padding: 6px 12px;
            text-align: center;
        }
        .tabla-vs th {
            background: #23263a;
            color: #00f0ff;
        }
        .tabla-vs td {
            background: #1d1f2f;
            color: #fff;
        }
        @media (max-width: 1100px) {
            .arena { width: 95vw; }
            .barra-vida { width: 70vw; min-width: 180px;}
        }
        @media (max-width: 600px) {
            .arena { height: 220px; }
            .luchador { width: 70px; height: 110px;}
            .seleccionar-clase-container { padding: 10px 6px 10px 6px;}
            .class-btn { min-width: 80px; min-height: 80px; font-size: 0.91em;}
            .class-btn img { width: 32px; height: 32px;}
            .tabla-vs {
        width: 98vw;
        min-width: 0;
        margin: 18px auto 0 auto;
        font-size: 0.85em;
        display: block;
        overflow-x: auto;
    }
    .tabla-vs th, .tabla-vs td {
        white-space: nowrap;
        padding: 6px 8px;
    }
        }
    </style>
</head>
<body>
    <h1>⚔️ Pelea Arcade - Game Nation</h1>

    <!-- Selección de clase -->
    <div class="seleccionar-clase-container" id="classSelectContainer">
        <div class="seleccionar-clase-titulo">Selecciona tu clase:</div>
        <div id="classButtons"></div>
        <div class="class-info" id="classInfo"></div>
        <button id="btn-empezar" class="btn-reiniciar" style="margin-top:18px;display:none;">¡Empezar combate!</button>
        <table class="tabla-vs">
            <tr>
                <th>Clase</th>
                <th>Fuerte contra</th>
                <th>Débil contra</th>
            </tr>
            <tr>
                <td>Guerrero</td>
                <td>Arquero</td>
                <td>Mago</td>
            </tr>
            <tr>
                <td>Mago</td>
                <td>Guerrero, Tanque</td>
                <td>Asesino</td>
            </tr>
            <tr>
                <td>Arquero</td>
                <td>Asesino</td>
                <td>Guerrero, Tanque</td>
            </tr>
            <tr>
                <td>Asesino</td>
                <td>Mago</td>
                <td>Arquero, Tanque</td>
            </tr>
            <tr>
                <td>Tanque</td>
                <td>Asesino, Arquero</td>
                <td>Mago</td>
            </tr>
        </table>
    </div>

    <div class="barra-vida" style="display:none;">
        <div id="vida1" class="vida" style="width: 100%;"></div>
    </div>
    <div class="barra-vida" style="display:none;">
        <div id="vida2" class="vida" style="width: 100%;"></div>
    </div>

    <div class="arena" style="display:none;">
        <div id="luchador1" class="luchador uno">
            <img id="imgluchador1" class="luchador-img" src="" alt="" style="display:none;">
            <div class="luchador-label" id="labelluchador1"></div>
        </div>
        <div id="luchador2" class="luchador dos">
            <img id="imgluchador2" class="luchador-img" src="" alt="" style="display:none;">
            <div class="luchador-label" id="labelluchador2"></div>
        </div>
    </div>

    <div class="controles" style="display:none;">
        <button id="btn-atacar" onclick="ataqueJugador()">👊 Atacar</button>
        <button id="btn-defender" onclick="defensaJugador()">🛡️ Bloquear</button>
        <button id="btn-habilidad" class="btn btn-warning" disabled>
            <i class="fa-solid fa-bolt"></i> Usar Habilidad
        </button>
    </div>

    <div class="mensaje-box" id="mensajeBox"></div>

    <a href="/GameNation/Administracion/views/user/minijuegos.php" class="btn-volver">
        <i class="fa-solid fa-arrow-left"></i> Volver a Minijuegos
    </a>
    <button id="btn-reiniciar" class="btn-reiniciar" style="display:none;"><i class="fa-solid fa-rotate-right"></i> Nueva partida</button>
    <script>
        // Configuración de clases
        const CLASES = [
            {
                nombre: "Guerrero",
                fuerte: ["Arquero"],
                debil: ["Mago"],
                color: "#e1a100",
                img: "/GameNation/Administracion/uploads/guerrero.png",
                icon: '<i class="fa-solid fa-shield-halved"></i>',
                habilidad: "Golpe devastador"
            },
            {
                nombre: "Mago",
                fuerte: ["Guerrero", "Tanque"],
                debil: ["Asesino"],
                color: "#7a6cff",
                img: "/GameNation/Administracion/uploads/mago.png",
                icon: '<i class="fa-solid fa-hat-wizard"></i>',
                habilidad: "Explosión arcana"
            },
            {
                nombre: "Arquero",
                fuerte: ["Asesino"],
                debil: ["Guerrero", "Tanque"],
                color: "#2be6b6",
                img: "/GameNation/Administracion/uploads/arquero.png",
                icon: '<i class="fa-solid fa-bow-arrow"></i>',
                habilidad: "Lluvia de flechas"
            },
            {
                nombre: "Asesino",
                fuerte: ["Mago"],
                debil: ["Arquero", "Tanque"],
                color: "#ff3c8b",
                img: "/GameNation/Administracion/uploads/asesino.png",
                icon: '<i class="fa-solid fa-user-ninja"></i>',
                habilidad: "Golpe sombrío"
            },
            {
                nombre: "Tanque",
                fuerte: ["Asesino", "Arquero"],
                debil: ["Mago"],
                color: "#7aab7a",
                img: "/GameNation/Administracion/uploads/tanque.png",
                icon: '<i class="fa-solid fa-dungeon"></i>',
                habilidad: "Carga imparable"
            }
        ];

        // Variables de combate
        let vida1, vida2, blocking1, blocking2, currentTurn, gameActive;
        let jugadorClass = null, aiClass = null;
        let aciertosJugador = 0, aciertosIA = 0;

        // --- SELECCIÓN DE CLASES ---
        function clasesBotones() {
            const btns = CLASES.map((clase, i) =>
                `<button class="class-btn" onclick="selectClass(${i})" id="class-btn-${i}">
                    <img src="${clase.img}" alt="${clase.nombre}" onerror="this.style.display='none';">
                    <span>${clase.nombre}</span>
                </button>`
            ).join('');
            document.getElementById('classButtons').innerHTML = btns;
        }

        function selectClass(idx) {
            jugadorClass = CLASES[idx];
            // Marcar seleccionado
            CLASES.forEach((_, i) => {
                document.getElementById('class-btn-' + i).classList.toggle('selected', i === idx);
            });
            document.getElementById('classInfo').innerHTML =
                `<b>${jugadorClass.nombre}</b><br>
                <span style="color:#7ae4ff;">Fuerte contra:</span> ${jugadorClass.fuerte.join(', ')}<br>
                <span style="color:#ff7878;">Débil contra:</span> ${jugadorClass.debil.join(', ')}`;
            document.getElementById('btn-empezar').style.display = "";
        }

        document.getElementById('btn-empezar').onclick = function() {
            // Elegir clase IA aleatoria
            let idx;
            do {
                idx = Math.floor(Math.random() * CLASES.length);
            } while (CLASES[idx].nombre === jugadorClass.nombre && CLASES.length > 1);
            aiClass = CLASES[idx];

            // Mostrar mensaje de clases elegidas
            showmensaje(`Has elegido <b style="color:${jugadorClass.color}">${jugadorClass.nombre}</b>. 
            La IA es <b style="color:${aiClass.color}">${aiClass.nombre}</b>.`);
            setTimeout(() => {
                // Ocultar selección y mostrar combate
                document.getElementById('classSelectContainer').style.display = "none";
                document.querySelectorAll('.barra-vida, .arena, .controles').forEach(e=>e.style.display='');
                iniciarPartida();
            }, 1800);
        };

        // --- COMBATE ---
        function iniciarPartida() {
            vida1 = 100;
            vida2 = 100;
            blocking1 = false;
            blocking2 = false;
            currentTurn = null;
            gameActive = true;

            // Resetear barras de vida
            document.getElementById('vida1').style.width = "100%";
            document.getElementById('vida2').style.width = "100%";
            // Resetear colores de luchadores según clase
            document.getElementById('luchador1').style.backgroundColor = jugadorClass.color + "22";
            document.getElementById('luchador2').style.backgroundColor = aiClass.color + "22";
            // Mostrar imágenes de luchadores
            let img1 = document.getElementById('imgluchador1');
            let img2 = document.getElementById('imgluchador2');
            img1.src = jugadorClass.img;
            img1.alt = jugadorClass.nombre;
            img1.style.display = "block";
            img2.src = aiClass.img;
            img2.alt = aiClass.nombre;
            img2.style.display = "block";
            // Etiquetas
            document.getElementById('labelluchador1').innerHTML = jugadorClass.nombre;
            document.getElementById('labelluchador2').innerHTML = aiClass.nombre;
            // Ocultar botón de reinicio
            document.getElementById('btn-reiniciar').style.display = "none";
            permitirControles(false);
            determinarPrimerTurno();
            document.getElementById('btn-habilidad').onclick = usarHabilidadJugador;
        }

        // Lanzar dado (1-6)
        function lanzarDados() {
            return Math.floor(Math.random() * 6) + 1;
        }

        function determinarPrimerTurno() {
            showmensaje("Lanzando dados para decidir quién empieza...");
            setTimeout(() => {
                const jugadorRoll = lanzarDados();
                const aiRoll = lanzarDados();
                showmensaje(`Tú sacas un ${jugadorRoll}. La IA saca un ${aiRoll}.`);
                setTimeout(() => {
                    if (jugadorRoll > aiRoll) {
                        currentTurn = 'jugador';
                        showmensaje("¡Empiezas tú! Es tu turno.");
                        permitirControles(true);
                    } else if (aiRoll > jugadorRoll) {
                        currentTurn = 'ai';
                        showmensaje("La IA empieza primero.");
                        permitirControles(false);
                        setTimeout(accionIa, 1200);
                    } else {
                        showmensaje("Empate, repitiendo tirada...");
                        setTimeout(determinarPrimerTurno, 1200);
                    }
                }, 1500);
            }, 1200);
        }

        function permitirControles(enable) {
            document.getElementById('btn-atacar').disabled = !enable;
            document.getElementById('btn-defender').disabled = !enable;
            verificarBotonHabilidad();
        }

        function ataqueAnimado(attackerId) {
            const attacker = document.getElementById(attackerId);
            const move = attackerId === 'luchador1' ? 150 : -150;
            attacker.style.transform = `translateX(${move}px)`;
            setTimeout(() => {
                attacker.style.transform = "translateX(0)";
            }, 200);
        }

        // --- VENTAJAS DE CLASE ---
        function calcularVentaja(atacante, defensor) {
            // Devuelve: 1 si ventaja, -1 si desventaja, 0 si neutral
            if (atacante.fuerte.includes(defensor.nombre)) return 1;
            if (atacante.debil.includes(defensor.nombre)) return -1;
            return 0;
        }

        // --- PROBABILIDAD DE FALLO ---
        function calcularFallo(ventaja) {
            // Base: 15% de fallo
            // Ventaja: solo 8% de fallo
            // Desventaja: 25% de fallo
            if (ventaja === 1) return Math.random() < 0.08;
            if (ventaja === -1) return Math.random() < 0.25;
            return Math.random() < 0.15;
        }

        // --- HABILIDADES ESPECIALES ---
        function calcularDañoEspecial(claseAtacante, claseDefensor) {
            let base = 30;
            let ventaja = calcularVentaja(claseAtacante, claseDefensor);
            if (ventaja === 1) return base + 10;
            if (ventaja === -1) return base - 10;
            return base;
        }

        function ataqueJugador() {
            if (!gameActive || currentTurn !== 'jugador') return;
            let base = 20;
            let ventaja = calcularVentaja(jugadorClass, aiClass);
            let damage = base;
            let msg = "";

            // ¿Falla el ataque?
            if (calcularFallo(ventaja)) {
                msg = "<b>¡Fallaste el ataque!</b>";
                showmensaje(msg);
                finTurno();
                return;
            }

            aciertosJugador++;

            if (ventaja === 1) { damage += 10; msg += "¡Ventaja de clase! "; }
            if (ventaja === -1) { damage -= 7; msg += "¡Desventaja de clase! "; }
            if (blocking2) { damage = Math.max(5, Math.floor(damage/4)); msg += "La IA bloquea. "; }
            if (Math.random() < 0.2) { damage *= 2; msg += "¡Golpe crítico! "; }

            msg += `Has hecho <b>${damage}</b> de daño.`;
            vida2 = Math.max(0, vida2 - damage);
            document.getElementById('vida2').style.width = vida2 + "%";
            ataqueAnimado('luchador1');
            blocking2 = false;

            showmensaje(msg);

            if (finPartida()) return;
            finTurno();
        }

        function defensaJugador() {
            if (!gameActive || currentTurn !== 'jugador') return;
            blocking1 = true;
            showmensaje("¡Te estás defendiendo! Daño recibido reducido en el próximo ataque.");
            finTurno();
        }

        function usarHabilidadJugador() {
            if (!gameActive || currentTurn !== 'jugador' || aciertosJugador < 3) return;
            let damage = calcularDañoEspecial(jugadorClass, aiClass);
            let msg = `<b>¡${jugadorClass.habilidad}!</b> Haces ${damage} de daño.`;
            vida2 = Math.max(0, vida2 - damage);
            document.getElementById('vida2').style.width = vida2 + "%";
            ataqueAnimado('luchador1');
            aciertosJugador = 0;
            showmensaje(msg);
            if (finPartida()) return;
            finTurno();
        }

        function ataqueIa() {
            let base = 15;
            let ventaja = calcularVentaja(aiClass, jugadorClass);
            let damage = base;
            let msg = "";

            // ¿Falla el ataque IA?
            if (calcularFallo(ventaja)) {
                msg = "<b>¡La IA falló el ataque!</b>";
                showmensaje(msg);
                setTimeout(finTurno, 1200);
                return;
            }

            if (ventaja === 1) { damage += 8; msg += "¡Ventaja de clase IA! "; }
            if (ventaja === -1) { damage -= 5; msg += "¡Desventaja de clase IA! "; }
            if (blocking1) { damage = Math.max(5, Math.floor(damage/4)); msg += "¡Bloqueas! "; }
            if (Math.random() < 0.2) { damage *= 2; msg += "¡Crítico IA! "; }

            msg += `La IA te hace <b>${damage}</b> de daño.`;
            vida1 = Math.max(0, vida1 - damage);
            document.getElementById('vida1').style.width = vida1 + "%";
            ataqueAnimado('luchador2');
            blocking1 = false;

            showmensaje(msg);

            if (finPartida()) return;
            setTimeout(finTurno, 1200);
        }

        function defensaIa() {
            blocking2 = true;
            showmensaje("La IA se defiende. ¡Su daño recibido se reducirá!");
            setTimeout(finTurno, 1200);
        }

        function accionIa() {
            if (!gameActive || currentTurn !== 'ai') return;
            // Simple IA: 65% atacar, 35% defender
            if (Math.random() < 0.65) ataqueIa();
            else defensaIa();
        }

        function usarHabilidadIA() {
            if (aciertosIA < 3) return false;
            let damage = calcularDañoEspecial(aiClass, jugadorClass);
            vida1 = Math.max(0, vida1 - damage);
            document.getElementById('vida1').style.width = vida1 + "%";
            ataqueAnimado('luchador2');
            showmensaje(`<b>¡${aiClass.habilidad} de la IA!</b> Te hace ${damage} de daño.`);
            aciertosIA = 0;
            return true;
        }

        function finTurno() {
            if (!gameActive) return;
            if (currentTurn === 'jugador') {
                currentTurn = 'ai';
                permitirControles(false);
                setTimeout(accionIa, 1200);
            } else {
                currentTurn = 'jugador';
                permitirControles(true);
                showmensaje("¡Es tu turno!");
            }
        }

        function finPartida() {
            if (vida1 <= 0 || vida2 <= 0) {
                gameActive = false;
                permitirControles(false);
                let msg = "";
                if (vida1 <= 0 && vida2 <= 0) msg = "¡Empate!";
                else if (vida1 <= 0) msg = "¡Has perdido!";
                else msg = "¡Has ganado!";
                showmensaje(`<b>${msg}</b>`);
                document.getElementById('btn-reiniciar').style.display = "";
                return true;
            }
            return false;
        }

        function showmensaje(msg) {
            document.getElementById('mensajeBox').innerHTML = msg;
        }

        // Mostrar botón si se puede usar habilidad
        function verificarBotonHabilidad() {
            const btn = document.getElementById('btn-habilidad');
            if (currentTurn === 'jugador' && aciertosJugador >= 3) {
                btn.disabled = false;
            } else {
                btn.disabled = true;
            }
        }

        document.getElementById('btn-reiniciar').onclick = function() {
            document.getElementById('classSelectContainer').style.display = "";
            document.querySelectorAll('.barra-vida, .arena, .controles').forEach(e=>e.style.display='none');
            document.getElementById('btn-reiniciar').style.display = "none";
            document.getElementById('mensajeBox').innerHTML = "";
            jugadorClass = null;
            aiClass = null;
            clasesBotones();
            document.getElementById('classInfo').innerHTML = "";
            document.getElementById('btn-empezar').style.display = "none";
        };

        // Inicialización
        clasesBotones();
    </script>
</body>
</html>
