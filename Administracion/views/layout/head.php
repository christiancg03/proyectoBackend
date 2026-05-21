<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Game Nation - Comunidad de Videojuegos">
  <title>Game Nation</title>
  
<style>
  .navbar .btn-sm {
  padding: 4px 10px;
  font-size: 0.85rem;
}

.navbar img.rounded-circle {
  transition: transform 0.3s ease-in-out;
}

.navbar img.rounded-circle:hover {
  transform: scale(1.1);
}

</style>

  <!-- Bootstrap core CSS -->
  <link href="/GameNation/Administracion/assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css" rel="stylesheet">

  <!-- Custom styles for Game Nation -->
  <link href="/GameNation/Administracion/assets/css/dashboard.css" rel="stylesheet">
  <link href="/GameNation/Administracion/assets/css/404.css" rel="stylesheet">
</head>
<body>
  <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="index.php">🎮 Game Nation</a>
    
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-nav flex-row align-items-center me-3">
  <?php if (!empty($_SESSION["usuario"]->foto)): ?>
    <img src="<?= $_SESSION["usuario"]->foto ?>" alt="Foto perfil" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #00f0ff;">
  <?php else: ?>
    <i class="fa-solid fa-user-circle fa-2x text-white me-2"></i>
  <?php endif; ?>
  
  <span class="text-white me-3 fw-semibold">
    <?= $_SESSION["usuario"]->usuario ?? 'Invitado' ?>
  </span>

  <a href="/GameNation/Administracion/views/user/perfil.php" class="btn btn-sm btn-outline-info me-2">
    <i class="fa-solid fa-user-pen"></i> Editar perfil
  </a>

  <a href="logout.php" class="btn btn-sm btn-outline-danger">
    <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
  </a>
</div>


  </header>
