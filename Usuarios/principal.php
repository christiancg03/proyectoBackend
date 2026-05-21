<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Principal - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, #12141e, #252a3a);
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        h1, h2 {
            color: #00f0ff;
            text-shadow: 0 0 5px #00f0ff44;
            text-align: center;
        }
        .lead {
            font-size: 1.2rem;
            color: #ccc;
        }
        main {
            width: 100%;
            max-width: 1400px; /* Ensanchado */
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #contenido {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .card {
            background-color: #1d1f2f;
            border-radius: 12px;
            box-shadow: 0 0 15px #00f0ff22;
            padding: 2rem;
            margin-top: 1.5rem;
            max-width: 98%; /* Ensanchado */
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .row-noticias {
            margin-top: 0.3rem;
            justify-content: center;
            width: 100%;
            display: flex;
            flex-wrap: wrap;
        }
        .noticia {
            background-color: #2a2d3f;
            padding: 1.2rem 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 0 10px #00f0ff11;
            text-align: left;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.7s ease forwards;
            width: 100%;
            max-width: 600px; /* Ensanchado */
            margin-left: auto;
            margin-right: auto;
        }
        .noticia h5 { color: #00f0ff; }
        .noticia img {
            display: block;
            margin: 0 auto 1rem auto;
            max-width: 80%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 0 10px #00f0ff22;
        }
        .noticia .autor {
            font-size: 0.9rem;
            color: #aaa;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px);}
            to   { opacity: 1; transform: translateY(0);}
        }
        .noticia[data-index="1"] { animation-delay: 0.1s; }
        .noticia[data-index="2"] { animation-delay: 0.2s; }
        .noticia[data-index="3"] { animation-delay: 0.3s; }
        .noticia[data-index="4"] { animation-delay: 0.4s; }
        .noticia[data-index="5"] { animation-delay: 0.5s; }
        .noticia[data-index="6"] { animation-delay: 0.6s; }
        .comentario {
            display: flex;
            gap: 15px;
            background-color: #1e2235;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 0 10px #00f0ff22;
            align-items: flex-start;
        }
        .comentario .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 0 5px #00f0ff55;
        }
        .comentario .contenido {
            flex-grow: 1;
        }
        .comentario .info {
            margin-bottom: 5px;
        }
        .comentario p {
            margin: 0;
            color: #ccc;
        }
        .comentario-form {
            background-color: #1d1f2f;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 0 10px #00f0ff22;
            margin-top: 2rem;
        }
        .comentario-textarea {
            width: 97%;
            background-color: #2a2d3f;
            color: #f1f1f1;
            border: 1px solid #444;
            border-radius: 8px;
            padding: 10px;
            resize: vertical;
            min-height: 80px;
            font-family: inherit;
            transition: border 0.3s ease;
        }
        .comentario-textarea:focus {
            border-color: #00f0ff;
            outline: none;
            box-shadow: 0 0 8px #00f0ff55;
        }
        .comentario-btn {
            margin-top: 10px;
            background-color: #00f0ff22;
            color: #00f0ff;
            border: 1px solid #00f0ff55;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .comentario-btn:hover {
            background-color: #00f0ff44;
            box-shadow: 0 0 10px #00f0ff88;
            color: #fff;
        }
        .btn-ir-noticia {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 10px 22px;
            border-radius: 10px;
            background-color: #00f0ff22;
            color: #00f0ff;
            border: 1.5px solid #00f0ff55;
            font-weight: bold;
            text-decoration: none;
            font-size: 1rem;
            box-shadow: 0 0 10px #00f0ff22;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .btn-ir-noticia:hover {
            background-color: #00f0ff44;
            color: #fff;
            border-color: #00f0ff;
            box-shadow: 0 0 15px #00f0ff88;
            text-decoration: none;
        }
        @media (max-width: 1600px) {
            main { max-width: 98vw; }
            .card { max-width: 99vw; }
        }
        @media (max-width: 767px) {
            main, .card { max-width: 100vw; padding: 0.5rem; }
            .noticia { padding: 1rem 0.5rem; max-width: 100vw; }
        }
    </style>
</head>
<body>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
  <div class="d-flex flex-column align-items-center pt-3 pb-2 mb-3 border-bottom mt-4">
    <h1 class="h2">Bienvenido a Game Nation</h1>
  </div>
  <div id="contenido">
    <h2 class="mt-4"><i class="fa-solid fa-newspaper"></i> Últimas Noticias del Mundo de los Videojuegos</h2>
    <div class="card">
      <div class="row row-noticias">
        <?php
        require_once "config/db.php";
        $conn = db::conexion();
        $stmt = $conn->prepare("SELECT n.*, u.usuario AS autor 
                                FROM noticias n
                                JOIN usuarios u ON n.autor_id = u.id
                                ORDER BY fecha_publicacion DESC");
        $stmt->execute();
        $noticias = $stmt->fetchAll(PDO::FETCH_OBJ);

        $index = 1;
         foreach ($noticias as $noticia): ?>
          <div class="col-md-6 d-flex align-items-stretch justify-content-center">
            <div class="noticia" data-index="<?= $index ?>">
              <?php if (!empty($noticia->imagen)): ?>
                <img src="<?= htmlspecialchars($noticia->imagen) ?>" alt="Imagen de la noticia">
              <?php endif; ?>
              <h5><?= htmlspecialchars($noticia->titulo) ?></h5>
              <p class="autor">Por <?= htmlspecialchars($noticia->autor) ?> | <?= $noticia->fecha_publicacion ?></p>
              <p><?= nl2br(htmlspecialchars($noticia->contenido)) ?></p>
              <!-- Comentarios -->
              <div class="mt-3">
                <h6>Comentario Más Reciente:</h6>
                <?php
                $cStmt = $conn->prepare("SELECT c.*, u.usuario, u.foto FROM comentarios c 
                                         JOIN usuarios u ON c.usuario_id = u.id 
                                         WHERE c.noticia_id = :noticia_id 
                                         ORDER BY c.fecha DESC LIMIT 1");
                $cStmt->execute([":noticia_id" => $noticia->id]);
                $comentario = $cStmt->fetch(PDO::FETCH_OBJ);
                if ($comentario): ?>
                  <div class="comentario">
                      <img class="avatar"
                          src="<?= !empty($comentario->foto) ? htmlspecialchars($comentario->foto) : '/GameNation/assets/img/default-user.png' ?>"
                          alt="avatar">
                      <div class="contenido">
                          <div class="info">
                              <strong><?= htmlspecialchars($comentario->usuario) ?></strong>
                              <small class="text-muted"> - <?= $comentario->fecha ?></small>
                          </div>
                          <p><?= nl2br(htmlspecialchars($comentario->contenido)) ?></p>
                      </div>
                  </div>
                <?php else: ?>
                  <div class="text-muted" style="font-size:0.98rem;">No hay comentarios aún.</div>
                <?php endif; ?>
              </div>
              <a href="/GameNation/Usuarios/views/user/noticias.php?id=<?= $noticia->id ?>" class="btn-ir-noticia">
                <i class="fa-solid fa-comment-dots"></i> Comentar / Ver Detalle
              </a>
            </div>
          </div>
        <?php $index++; endforeach; ?>
      </div>
    </div>
  </div>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>
