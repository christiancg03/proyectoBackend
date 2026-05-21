<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}

$conn = db::conexion();

// Traer todos los usuarios con rol usuario
$sql = $conn->prepare("SELECT id, usuario, email, rol, foto FROM usuarios WHERE rol = 'usuario'");
$sql->execute();
$usuarios_totales = $sql->fetchAll(PDO::FETCH_ASSOC);

// Eliminar usuario si se pasa por POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["eliminar_id"])) {
    $idEliminar = (int)$_POST["eliminar_id"];
    if ($idEliminar > 0) {
        
        // 1. Limpiar amigos
        $delAmigos = $conn->prepare("DELETE FROM amigos WHERE usuario_id = :id OR amigo_id = :id");
        $delAmigos->execute([":id" => $idEliminar]);
        
        // 2. Limpiar biblioteca
        $delBiblioteca = $conn->prepare("DELETE FROM biblioteca WHERE usuario_id = :id");
        $delBiblioteca->execute([":id" => $idEliminar]);

        // 3. Limpiar mensajes (tanto enviados como recibidos)
        $delMensajes = $conn->prepare("DELETE FROM mensajes WHERE emisor_id = :id OR receptor_id = :id");
        $delMensajes->execute([":id" => $idEliminar]);

        // 4. Limpiar sus comentarios en las noticias
        $delComentarios = $conn->prepare("DELETE FROM comentarios WHERE usuario_id = :id");
        $delComentarios->execute([":id" => $idEliminar]);

        // 5. Limpiar registros de contacto
        $delContacto = $conn->prepare("DELETE FROM contacto WHERE usuario_id = :id");
        $delContacto->execute([":id" => $idEliminar]);

        // 6. Por último, borrar al usuario de forma segura
        $del = $conn->prepare("DELETE FROM usuarios WHERE id = :id");
        $del->execute([":id" => $idEliminar]);
        
        header("Location: usuarios.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lista de Usuarios</title>
  <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #0d0f1c, #202437);
      color: #f1f1f1;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    h2 {
      text-align: center;
      margin: 2rem 0;
      color: #00f0ff;
      text-shadow: 0 0 6px #00f0ff88;
    }
    .user-card {
      background: #1c1f2e;
      border-radius: 15px;
      box-shadow: 0 0 15px #00f0ff33;
      padding: 1.5rem;
      text-align: center;
      transition: transform 0.3s, box-shadow 0.3s;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
      height: 100%;
    }
    .user-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 0 25px #00f0ff55;
    }
    .user-card img {
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #00f0ff66;
      margin-bottom: 1rem;
      width: 90px;
      height: 90px;
    }
    .user-name {
      font-weight: bold;
      color: #fff;
      font-size: 1.2rem;
      margin-bottom: 0.2rem;
    }
    .user-email {
      font-size: 0.9rem;
      color: #aaa;
      margin-bottom: 0.8rem;
      word-break: break-word;
    }
    .delete-btn {
      background-color: #dc3545;
      border: none;
      color: #fff;
      padding: 0.4rem 0.9rem;
      border-radius: 8px;
      font-size: 0.9rem;
      font-weight: 600;
      transition: background-color 0.3s, box-shadow 0.3s;
    }
    .delete-btn:hover {
      background-color: #b02a37;
      box-shadow: 0 0 12px #ff4d4d88;
    }
    .volver-btn {
      background-color: transparent;
      border: 1.5px solid #00f0ff;
      color: #00f0ff;
      border-radius: 8px;
      padding: 0.4rem 1rem;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 1.5rem;
      transition: background-color 0.3s, color 0.3s;
    }
    .volver-btn:hover {
      background-color: #00f0ff;
      color: #1d1f2f;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2><i class="fa-solid fa-users"></i> Lista de Usuarios</h2>
    <a href="../../index.php" class="volver-btn">
        <i class="fa-solid fa-arrow-left"></i> Volver al panel principal
    </a>
    <div class="row g-4">
      <?php foreach ($usuarios_totales as $usuario): ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
          <div class="user-card">
            <img src="<?= htmlspecialchars($usuario['foto']) ?>" 
                 alt="Foto de <?= htmlspecialchars($usuario['usuario']) ?>">
            <div class="user-name"><?= htmlspecialchars($usuario['usuario']) ?></div>
            <div class="user-email"><?= htmlspecialchars($usuario['email']) ?></div>
            <form method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
              <input type="hidden" name="eliminar_id" value="<?= $usuario['id'] ?>">
              <button type="submit" class="delete-btn">
                <i class="fa-solid fa-user-xmark"></i> Eliminar
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>