<style>
/* --- NAVBAR ESTÁTICO --- */
.navbar-custom {
  background-color: #23263a !important;
  border-bottom: 2px solid #00f0ff55;
  box-shadow: 0 2px 10px hsla(184, 100%, 50%, 0.15);
}

.navbar-custom .navbar-brand {
  color: #00f0ff;
  font-weight: 600;
  font-size: 1.4rem;
  text-shadow: 0 0 8px #00f0ff88;
}

.navbar-custom .nav-link {
  color: #00f0ff !important;
  font-weight: 500;
  transition: all 0.3s;
  margin: 0 8px;
  border-radius: 6px;
  padding: 8px 14px;
  display: flex;
  align-items: center;
  gap: 6px;
  border: 1.5px solid transparent;
}

.navbar-custom .nav-link:hover,
.navbar-custom .nav-link.active {
  background-color: #1c1f2e;
  border-color: #00f0ff;
  color: #00f0ff !important;
  text-shadow: 0 0 6px #00f0ff99;
  box-shadow: 0 0 10px #00f0ff33;
}

.navbar-custom .nav-item i {
  font-size: 1rem;
}

.navbar-custom .nav-item .desc {
  display: block;
  font-size: 0.85rem;
  color: #7ae4ff;
}

/* --- Ajustes visuales para la parte del usuario --- */
.user-avatar {
  width: 40px;
  height: 40px;
  object-fit: cover;
  border: 2px solid #00f0ff;
  box-shadow: 0 0 8px #00f0ff44;
  transition: transform 0.3s;
}
.user-avatar:hover {
  transform: scale(1.05);
}

.user-name {
  color: #00f0ff;
  font-weight: 600;
  text-shadow: 0 0 6px #00f0ff88;
}

.salir-btn {
  border-color: #00f0ff55 !important;
  color: #00f0ff !important;
  font-weight: 500;
  transition: all 0.3s ease;
  box-shadow: 0 0 10px transparent;
}
.perfil-btn{
  border-color: #00f0ff55 !important;
  color: #00f0ff !important;
  font-weight: 500;
  transition: all 0.3s ease;
  box-shadow: 0 0 10px transparent;
}
.perfil-btn:hover {
  background-color: #00f0ff22 !important;
  box-shadow: 0 0 12px #00f0ff55;
  color: #b7c40aff !important;
}
.salir-btn:hover {
  background-color: #00f0ff22 !important;
  box-shadow: 0 0 12px #00f0ff55;
  color: #ff0000ff !important;
}


@media (max-width: 992px) {
  .navbar-custom .nav-link {
    margin: 4px 0;
  }
}

/* Fondo general (igual que en el resto del sitio) */
body {
  background: linear-gradient(to right, #0d0f1c, #202437);
  color: #f1f1f1;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
</style>

<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
  <div class="container-fluid">
    <!-- Marca -->
    <a class="navbar-brand" href="index.php">
      <i class="fa-solid fa-gamepad"></i> GameNation
    </a>

    <!-- Menú principal -->
    <div class="collapse navbar-collapse show" id="navbarNav">
      <ul class="navbar-nav me-auto"> <!-- alineamos menú a la izquierda -->
        <!-- <li class="nav-item">
          <a class="nav-link active" href="index.php">
            <i class="fa-solid fa-house"></i> Inicio
          </a>
        </li> -->
        <li class="nav-item">
          <a class="nav-link" href="/GameNation/Usuarios/views/user/amigos.php">
            <i class="fa-solid fa-user-group"></i> Amigos
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/GameNation/Usuarios/views/user/reseñas.php">
            <i class="fa-solid fa-star"></i> Reseñas
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/GameNation/Usuarios/views/user/biblioteca.php">
            <i class="fa-solid fa-gamepad"></i> Biblioteca
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/GameNation/Usuarios/views/user/minijuegos.php">
            <i class="fa-solid fa-dice"></i> Minijuegos
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/GameNation/Usuarios/views/user/noticias.php">
            <i class="fa-solid fa-newspaper"></i> Noticias
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <i class="fa-solid fa-bars-progress"></i> Próximamente
          </a>
        </li>
      </ul>

      <!-- Perfil de usuario (parte derecha) -->
      <div class="navbar-nav flex-row align-items-center ms-auto pe-2">
        <?php if (!empty($_SESSION["usuario"]->foto)): ?>
          <img src="<?= $_SESSION["usuario"]->foto ?>" 
               alt="Foto perfil" 
               class="rounded-circle me-2 user-avatar">
        <?php else: ?>
          <i class="fa-solid fa-user-circle fa-2x text-white me-2"></i>
        <?php endif; ?>

        <span class="text-white me-3 fw-semibold user-name">
          <?= htmlspecialchars($_SESSION["usuario"]->usuario ?? 'Invitado') ?>
        </span>

        <a href="/GameNation/Usuarios/views/user/perfil.php" class="btn btn-outline-info btn-sm me-2 perfil-btn">
          <i class="fa-solid fa-user-pen"></i> Perfil
        </a>

        <a href="logout.php" class="btn btn-outline-danger btn-sm salir-btn">
          <i class="fa-solid fa-right-from-bracket"></i> Salir
        </a>
      </div>
    </div>
  </div>
</nav>


<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>