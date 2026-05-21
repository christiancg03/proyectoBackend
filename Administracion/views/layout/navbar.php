<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
  <div class="position-sticky pt-3">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link active" href="index.php">
          <i class="fa-solid fa-house"></i> Inicio
        </a>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
          <i class="fa-solid fa-users"></i> Usuarios
        </a>
        <ul class="dropdown-menu dropdown-menu-light">
          <li><a class="dropdown-item" href="/GameNation/Administracion/views/user/create.php">Añadir</a></li>
          <li><a class="dropdown-item" href="/GameNation/Administracion/views/user/list.php">Listar</a></li>
          <li><a class="dropdown-item" href="/GameNation/Administracion/views/user/search.php">Buscar</a></li>
        </ul>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
          <i class="fa-solid fa-gamepad"></i> Juegos
        </a>
        <ul class="dropdown-menu dropdown-menu-light">
          <li><a class="dropdown-item" href="/GameNation/Administracion/views/juegos/create.php">Añadir</a></li>
          <li><a class="dropdown-item" href="/GameNation/Administracion/views/juegos/list.php">Listar</a></li>
          <li><a class="dropdown-item" href="/GameNation/Administracion/views/juegos/search.php">Buscar</a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="/GameNation/user/foro.php">
          <i class="fa-solid fa-comments"></i> Foro
        </a>
      </li>
    </ul>
  </div>
</nav>
