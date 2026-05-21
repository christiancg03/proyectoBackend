<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}

$conn = db::conexion();
$miId = $_SESSION["usuario"]->id;
$usuarioActual = $_SESSION["usuario"];

$amigoId = $_GET["amigo_id"] ?? null;
if (!$amigoId) {
    echo "Amigo no especificado.";
    exit();
}

// Verifica si son amigos
$stmt = $conn->prepare("SELECT * FROM amigos WHERE 
    (usuario_id = :yo AND amigo_id = :el OR usuario_id = :el AND amigo_id = :yo)
    AND aceptado = 1");
$stmt->execute([":yo" => $miId, ":el" => $amigoId]);
if ($stmt->rowCount() === 0) {
    echo "No tienes acceso al chat con este usuario.";
    exit();
}

// Obtener nombre y foto del amigo
$stmt = $conn->prepare("SELECT usuario, foto FROM usuarios WHERE id = :id");
$stmt->execute([":id" => $amigoId]);
$amigo = $stmt->fetch(PDO::FETCH_OBJ);

// Definir las fotos con comprobación si es externa o interna
$miFoto = (!empty($usuarioActual->foto) && !str_starts_with($usuarioActual->foto, "http"))
    ? $usuarioActual->foto
    : ($usuarioActual->foto ?: "/GameNation/assets/img/default-avatar.png");

$amigoFoto = (!empty($amigo->foto) && !str_starts_with($amigo->foto, "http"))
    ? $amigo->foto
    : ($amigo->foto ?: "/GameNation/assets/img/default-avatar.png");

// Enviar mensaje
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["mensaje"])) {
    $mensaje = trim($_POST["mensaje"]);
    if ($mensaje !== "") {
        $stmt = $conn->prepare("INSERT INTO mensajes (emisor_id, receptor_id, contenido) 
                                VALUES (:yo, :el, :contenido)");
        $stmt->execute([
            ":yo" => $miId,
            ":el" => $amigoId,
            ":contenido" => $mensaje
        ]);
    }
    header("Location: chat.php?amigo_id=" . $amigoId);
    exit();
}

// Obtener historial
$stmt = $conn->prepare("SELECT * FROM mensajes 
                        WHERE (emisor_id = :yo AND receptor_id = :el)
                           OR (emisor_id = :el AND receptor_id = :yo)
                        ORDER BY enviado_en ASC");
$stmt->execute([":yo" => $miId, ":el" => $amigoId]);
$mensajes = $stmt->fetchAll(PDO::FETCH_OBJ);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chat con <?= htmlspecialchars($amigo->usuario) ?></title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/GameNation/assets/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
      body {
        background: linear-gradient(to right, #0d0f1c, #202437);
        color: #f1f1f1;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      }
      h2 {
        color: #00f0ff;
        text-shadow: 0 0 6px #00f0ff88;
      }
      .chat-contenedor {
        max-width: 600px;
        margin: 3rem auto 2rem auto;
        background: #181b29;
        border-radius: 18px;
        box-shadow: 0 0 18px #00f0ff22;
        padding: 2.2rem 1.3rem 1.5rem 1.3rem;
      }
      .chat-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
      }
      .chat-header img {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #00f0ff66;
      }
      .chat-mensajes {
        max-height: 400px;
        overflow-y: auto;
        padding: 1rem 0.5rem;
        background: #1c1f2e;
        border-radius: 12px;
        box-shadow: 0 0 10px #00f0ff33;
        margin-bottom: 1.4rem;
        scrollbar-width: thin;
        scrollbar-color: #00f0ff44 #202437;
      }
      .chat-mensajes::-webkit-scrollbar {
        width: 6px;
      }
      .chat-mensajes::-webkit-scrollbar-thumb {
        background-color: #00f0ff88;
        border-radius: 4px;
      }
      .chat-fila {
        display: flex;
        align-items: flex-end;
        margin-bottom: 1.2rem;
      }
      .chat-fila.yo {
        justify-content: flex-end;
      }
      .chat-fila.amigo {
        justify-content: flex-start;
      }
      .chat-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #00f0ff33;
        margin: 0 0.7rem;
      }
      .chat-usuario {
        max-width: 70%;
        padding: 0.9rem 1.2rem;
        border-radius: 18px;
        font-size: 1.05rem;
        line-height: 1.5;
        box-shadow: 0 2px 10px #00f0ff22;
        word-break: break-word;
        position: relative;
        display: flex;
        flex-direction: column;
      }
      .chat-usuario.yo {
        background: #183c4a; /* Fondo azul oscuro, sin degradado */
        color: #e0faff;      /* Texto claro para contraste */
        border-bottom-right-radius: 4px;
        align-items: flex-end;
        border: 1.5px solid #00f0ff44;
      }
      .chat-usuario.amigo {
        background: #23263a;
        color: #f1f1f1;
        border-bottom-left-radius: 4px;
        align-items: flex-start;
      }
      .chat-usuario .hora {
        font-size: 0.8rem;
        color: #7ae4ff;
        margin-top: 0.2rem;
        opacity: 0.7;
      }
      .chat-usuario .fecha {
        font-size: 0.7rem;
        color: #7ae4ffaa;
        margin-top: 0.1rem;
        opacity: 0.7;
      }
      .chat-form-row {
        display: flex;
        align-items: flex-end;
        gap: 0.7rem;
        margin-top: 1.5rem;
      }
      .chat-form-row textarea {
        flex: 1;
        min-height: 45px;
        max-height: 120px;
        border-radius: 14px;
        background: #23263a;
        color: #f1f1f1;
        border: 1px solid #00f0ff44;
        padding: 0.8rem 1.1rem;
        font-size: 1.07rem;
        resize: vertical;
        box-shadow: 0 2px 8px #00f0ff11;
        transition: border 0.2s;
      }
      .chat-form-row textarea:focus {
        border: 1.5px solid #00f0ff;
        outline: none;
      }
      .chat-form-row button {
        background: linear-gradient(90deg, #00f0ff 60%, #1c1f2f 100%);
        color: #111;
        font-weight: 700;
        border: none;
        border-radius: 14px;
        padding: 0.7rem 1.7rem;
        font-size: 1.06rem;
        box-shadow: 0 0 10px #00f0ff44;
        transition: background 0.3s, color 0.3s, box-shadow 0.3s, transform 0.2s;
        margin-left: 0.3rem;
        margin-bottom: 0.05rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
      }
      .chat-form-row button:hover {
        background: linear-gradient(90deg, #1c1f2f 0%, #00f0ff 100%);
        color: #00f0ff;
        box-shadow: 0 0 18px #00f0ff88;
        transform: scale(1.05);
      }
      .volver-btn {
        border: 2px solid #00f0ff;
        color: #00f0ff;
        background: transparent;
        margin-top: 2rem;
        font-size: 1.15rem;
        font-weight: bold;
        padding: 12px 35px !important;
        border-radius: 12px;
        box-shadow: 0 0 10px #00f0ff33;
        transition: all 0.25s;
        text-decoration: none;
        display: inline-block;
      }
      .volver-btn:hover {
        background: linear-gradient(90deg, #1c1f2f 0%, #00f0ff 100%);
        color: #00f0ff;
        box-shadow: 0 0 18px #00f0ff88;
      }
      @media (max-width: 700px) {
        .chat-contenedor {
          max-width: 99vw;
          padding: 1.1rem 0.2rem 1.1rem 0.2rem;
        }
        .chat-mensajes {
          padding: 0.6rem 0.2rem;
        }
        .chat-usuario {
          max-width: 90vw;
          font-size: 0.97rem;
        }
        .chat-header img {
          width: 38px;
          height: 38px;
        }
      }
    </style>
</head>
<body>
<div class="chat-contenedor">

    <div class="chat-header">
        <img src="<?= htmlspecialchars($amigoFoto) ?>" alt="Foto de perfil de <?= htmlspecialchars($amigo->usuario) ?>">
        <h2 class="mb-0"><i class="fa-solid fa-comments"></i> <?= htmlspecialchars($amigo->usuario) ?></h2>
    </div>

    <div class="chat-mensajes" id="chat-mensajes">
        <?php if (empty($mensajes)): ?>
            <p class="text-muted">Aún no hay mensajes.</p>
        <?php else: ?>
            <?php foreach ($mensajes as $msg): ?>
    <?php
      $esMio = $msg->emisor_id == $miId;
      $foto = $esMio ? $miFoto : $amigoFoto;
      $claseFila = $esMio ? 'yo' : 'amigo';
      $claseBurbuja = $esMio ? 'yo' : 'amigo';
    ?>
    <div class="chat-fila <?= $claseFila ?>">
        <?php if (!$esMio): ?>
            <img src="<?= htmlspecialchars($foto) ?>" class="chat-avatar" alt="Foto amigo">
        <?php endif; ?>
        <div class="chat-usuario <?= $claseBurbuja ?>">
            <?= htmlspecialchars($msg->contenido) ?>
            <span class="hora"><?= date('H:i', strtotime($msg->enviado_en)) ?></span>
            <small class="fecha"><?= date('Y-m-d', strtotime($msg->enviado_en)) ?></small>
        </div>
        <?php if ($esMio): ?>
            <img src="<?= htmlspecialchars($foto) ?>" class="chat-avatar" alt="Tu foto">
        <?php endif; ?>
    </div>
<?php endforeach; ?>
        <?php endif; ?>
    </div>

    <form method="POST" class="chat-form-row" autocomplete="off">
        <textarea name="mensaje" class="form-control" placeholder="Escribe tu mensaje..." required></textarea>
        <button type="submit">
            <i class="fa-solid fa-paper-plane"></i> Enviar
        </button>
    </form>

    <div class="text-center">
        <a href="amigos.php" class="volver-btn">
            <i class="fa-solid fa-arrow-left"></i> Volver a Amigos
        </a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script>
    // Scroll automático al final del chat
    window.onload = function() {
        var chat = document.getElementById('chat-mensajes');
        chat.scrollTop = chat.scrollHeight;
    };
</script>
</body>
</html>