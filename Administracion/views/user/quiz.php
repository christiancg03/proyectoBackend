<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}

$conn = db::conexion();

// Obtener 5 preguntas aleatorias
$stmt = $conn->prepare("SELECT * FROM quiz ORDER BY RAND() LIMIT 5");
$stmt->execute();
$preguntas = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quiz - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0d0f1c;
            color: #f1f1f1;
            font-family: 'Press Start 2P', cursive;
            padding: 2rem;
        }

        .contenido-quiz {
            background-color: #1c1f2e;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 0 20px #00f0ff88;
            max-width: 800px;
            margin: auto;
        }

        .titulo-quiz {
            color: #00f0ff;
            text-shadow: 0 0 6px #00f0ff88;
            margin-bottom: 2rem;
            text-align: center;
        }

        .pregunta {
            background-color: #2a2d3e;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            color: #fff;
        }

        .opcion {
            background-color: #333;
            color: #f1f1f1;
            padding: 0.8rem 1rem;
            margin-bottom: 0.8rem;
            border-radius: 8px;
            cursor: pointer;
            border: 1px solid #555;
            transition: all 0.3s;
        }

        .opcion:hover {
            background-color: #00f0ff;
            color: #000;
            border: 1px solid #00f0ff;
        }

        .btn-quiz {
            background-color: #00f0ff;
            color: #000;
            border: none;
            font-weight: bold;
            border-radius: 8px;
            padding: 12px;
            width: 100%;
            margin-top: 1.5rem;
        }

        .btn-quiz:hover {
            background-color: #00c7e6;
        }

        .resultado_final {
            background-color: #28a745;
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1.5rem;
            color: #fff;
            text-align: center;
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
        @import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');
    </style>
</head>
<body>
    <div class="contenido-quiz">
        <h1 class="titulo-quiz">🎮 Game Nation - Quiz</h1>
        
        <form id="quizForm">
            <?php foreach ($preguntas as $index => $pregunta): ?>
                <div class="pregunta">
                    <p><?= ($index + 1) . ". " . htmlspecialchars($pregunta->pregunta) ?></p>
                    <div class="opcion">
                        <input type="radio" name="respuesta_<?= $pregunta->id ?>" value="a">
                        <?= htmlspecialchars($pregunta->opcion_a) ?>
                    </div>
                    <div class="opcion">
                        <input type="radio" name="respuesta_<?= $pregunta->id ?>" value="b">
                        <?= htmlspecialchars($pregunta->opcion_b) ?>
                    </div>
                    <div class="opcion">
                        <input type="radio" name="respuesta_<?= $pregunta->id ?>" value="c">
                        <?= htmlspecialchars($pregunta->opcion_c) ?>
                    </div>
                    <div class="opcion">
                        <input type="radio" name="respuesta_<?= $pregunta->id ?>" value="d">
                        <?= htmlspecialchars($pregunta->opcion_d) ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn-quiz">Enviar Respuestas</button>
        </form>

        <div id="resultado" class="resultado_final" style="display: none;"></div>
    </div>

<a href="/GameNation/Administracion/views/user/minijuegos.php" class="btn btn-volver mt-4">
    <i class="fa-solid fa-arrow-left"></i> Volver a Minijuegos
</a>

    <script>
        const form = document.getElementById('quizForm');
const resultadoDiv = document.getElementById('resultado');

form.addEventListener('submit', (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    let correctas = 0;
    
    // Respuestas correctas del servidor
    const respuestasCorrectas = {
        <?php foreach ($preguntas as $pregunta): ?>
            <?= $pregunta->id ?>: '<?= $pregunta->correcta ?>',
        <?php endforeach; ?>
    };

    formData.forEach((respuesta, preguntaId) => {
        const id = preguntaId.split('_')[1]; // Extrae el ID de la pregunta
        if (respuesta === respuestasCorrectas[id]) {
            correctas++;
        }
    });

    resultadoDiv.innerText = `🎉 Has acertado ${correctas} de <?= count($preguntas) ?> preguntas.`;
    resultadoDiv.style.display = "block";
});

    </script>
</body>
</html>