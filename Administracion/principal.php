<?php
require_once __DIR__ . '/config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit();
}

$conn = db::conexion();
$miId = $_SESSION["usuario"]->id;
$miRol = $_SESSION["usuario"]->rol;

// Estadísticas rápidas
$stmt = $conn->prepare("SELECT COUNT(*) FROM contacto WHERE leido=0");
$stmt->execute();
$no_leidos = $stmt->fetchColumn();

$stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE rol LIKE 'usuario'");
$stmt->execute();
$total_usuarios = $stmt->fetchColumn();

$stmt = $conn->prepare("SELECT COUNT(*) FROM resenas");
$stmt->execute();
$total_resenas = $stmt->fetchColumn();

$stmt = $conn->prepare("SELECT COUNT(*) FROM noticias");
$stmt->execute();
$total_noticias = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Administración - Game Nation</title>
    <link href="/GameNation/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/GameNation/assets/css/admin-dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html, body { height: 100%; }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(to right, #0f111a, #1c1f2f);
            color: #f1f1f1;
        }
        .container-fluid { flex: 1 0 auto; }
        .admin-main-content { margin-bottom: 2.5rem; }
        footer {
            background-color: #22304a;
            color: #e3e6ea;
            text-align: center;
            padding: 10px;
            font-size: 0.95rem;
            border-top: 1px solid #e3e6ea;
            width: 100%;
            margin-top: auto;
        }
        /* Sidebar icon color */
        .sidebar .nav-link i {
            color: #00f0ff !important;
            transition: color 0.2s;
        }
        .sidebar .nav-link.active i,
        .sidebar .nav-link:hover i {
            color: #00e0e0 !important;
        }
        /* Bienvenida color */
        .bienvenida-usuario {
            color: #00f0ff;
            text-shadow: 0 0 8px #00f0ff88;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .dashboard-cards .card {
            box-shadow: 0 2px 8px rgba(0,240,255,0.08);
            border-radius: 12px;
            background: #23263a;
            border: 1px solid #00f0ff33;
            color: #f1f1f1;
            min-height: 140px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: box-shadow 0.2s;
        }
        .dashboard-cards .card:hover {
            box-shadow: 0 4px 20px #00f0ff44;
        }
        .dashboard-cards .display-6 {
            font-size: 2.3rem;
            color: #00f0ff;
            font-weight: bold;
            margin-bottom: 0;
        }
        .dashboard-cards .fw-bold {
            font-size: 1.06rem;
            color: #7ae4ff;
            margin-bottom: 0.3rem;
        }
        .dashboard-cards .h3 {
            font-size: 2rem;
            margin-bottom: 0.1rem;
        }
        .dashboard-cards .icon-primary,
        .dashboard-cards .icon-info,
        .dashboard-cards .icon-warning,
        .dashboard-cards .icon-news {
            color: #00f0ff !important;
        }
        @media (max-width: 900px) {
            .dashboard-cards .card {
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 767.98px) {
            .menu-movil-boton {
                display: block;
                position: fixed;
                top: 10px;
                left: 10px;
                z-index: 1050;
                background-color: #00f0ff;
                color: #23263a;
                border: none;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
                box-shadow: 0 0 10px rgba(0, 240, 255, 0.5);
            }
            .menu-movil {
                position: fixed;
                top: 0;
                left: 0;
                width: 250px;
                height: 100%;
                background-color: #23263a;
                z-index: 1040;
                transform: translateX(-100%);
                transition: transform 0.3s ease, visibility 0.3s ease;
                padding: 20px;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
                pointer-events: none;
                visibility: hidden;
            }
            .menu-movil.mostrar {
                transform: translateX(0);
                pointer-events: auto;
                visibility: visible;
            }
            .mobile-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1030;
                visibility: hidden;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            .mobile-overlay.mostrar {
                visibility: visible;
                opacity: 1;
            }
            .sidebar {
                display: none !important;
            }
        }

        @media (min-width: 768px) {
            .menu-movil-boton,
            .menu-movil,
            .mobile-overlay {
                display: none !important;
            }
        }

    </style>
</head>
<body>
<!-- Botón y menú móvil - SOLO visible en móvil -->
<button class="menu-movil-boton" id="mobileMenuToggle">
    <i class="fa-solid fa-bars"></i>
</button>
<div class="menu-movil" id="mobileMenu">
    <div class="nav flex-column">
        <a class="nav-link active" href="index.php"><i class="fa-solid fa-house"></i> Inicio</a>
        <?php if ($miRol === 'admin'): ?>
        <a class="nav-link" href="/GameNation/Administracion/views/user/contacto_admin.php">
            <i class="fa-solid fa-envelope"></i> Contacto
            <?php if ($no_leidos > 0): ?>
                <span class="badge bg-danger ms-2"><?= $no_leidos ?></span>
            <?php endif; ?>
        </a>
        <?php endif; ?>
        <a class="nav-link" href="/GameNation/Administracion/views/user/amigos.php"><i class="fa-solid fa-user-group"></i> Equipo</a>
        <a class="nav-link" href="/GameNation/Administracion/views/user/reseñas.php"><i class="fa-solid fa-star"></i> Reseñas</a>
        <a class="nav-link" href="/GameNation/Administracion/views/user/biblioteca.php"><i class="fa-solid fa-gamepad"></i> Biblioteca</a>
        <a class="nav-link" href="/GameNation/Administracion/views/user/noticias.php"><i class="fa-solid fa-newspaper"></i> Noticias</a>
        <a class="nav-link" href="/GameNation/Administracion/logout.php"><i class="fa-solid fa-sign-out-alt"></i> Cerrar sesión</a>
    </div>
</div>
<div class="mobile-overlay" id="mobileOverlay"></div>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar de navegación - SOLO visible en escritorio -->
        <nav class="col-md-2 d-none d-md-block sidebar py-4">
            <div class="nav flex-column">
                <a class="nav-link active" href="index.php"><i class="fa-solid fa-house"></i> Inicio</a>
                <?php if ($miRol === 'admin'): ?>
                <a class="nav-link" href="/GameNation/Administracion/views/user/contacto_admin.php">
                    <i class="fa-solid fa-envelope"></i> Contacto
                    <?php if ($no_leidos > 0): ?>
                        <span class="badge bg-danger ms-2"><?= $no_leidos ?></span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                <a class="nav-link" href="/GameNation/Administracion/views/user/amigos.php"><i class="fa-solid fa-user-group"></i> Equipo</a>
                <a class="nav-link" href="/GameNation/Administracion/views/user/usuarios.php"><i class="fa-solid fa-users"></i> Ver Usuarios</a>
                <a class="nav-link" href="/GameNation/Administracion/views/user/reseñas.php"><i class="fa-solid fa-star"></i> Reseñas</a>
                <a class="nav-link" href="/GameNation/Administracion/views/user/biblioteca.php"><i class="fa-solid fa-gamepad"></i> Biblioteca</a>
                <!-- <a class="nav-link" href="/GameNation/Administracion/views/user/minijuegos.php"><i class="fa-solid fa-dice"></i> Minijuegos</a> -->
                <a class="nav-link" href="/GameNation/Administracion/views/user/noticias.php"><i class="fa-solid fa-newspaper"></i> Noticias</a>
                <a class="nav-link" href="/GameNation/Administracion/logout.php"><i class="fa-solid fa-sign-out-alt"></i> Cerrar sesión</a>
            </div>
        </nav>
        <!-- Contenido principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 bienvenida-usuario">
                    Bienvenido, <?= htmlspecialchars($_SESSION["usuario"]->usuario) ?>
                </h1>
            </div>
            <div class="dashboard-cards row g-4 mb-4">
                <div class="col-md-3 col-6">
                    <div class="card text-center p-3">
                        <div class="h3 mb-1 icon-primary"><i class="fa-solid fa-users"></i></div>
                        <div class="fw-bold">Usuarios Registrados</div>
                        <div class="display-6"><?= $total_usuarios ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card text-center p-3">
                        <div class="h3 mb-1 icon-info"><i class="fa-solid fa-envelope"></i></div>
                        <div class="fw-bold">Mensajes sin leer</div>
                        <div class="display-6"><?= $no_leidos ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card text-center p-3">
                        <div class="h3 mb-1 icon-warning"><i class="fa-solid fa-star"></i></div>
                        <div class="fw-bold">Reseñas Escritas</div>
                        <div class="display-6"><?= $total_resenas ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card text-center p-3">
                        <div class="h3 mb-1 icon-news"><i class="fa-solid fa-newspaper"></i></div>
                        <div class="fw-bold">Noticias Publicadas</div>
                        <div class="display-6"><?= $total_noticias ?></div>
                    </div>
                </div>
            </div>
            <!-- Aquí puedes añadir más widgets, avisos, gráficos, etc. -->
        </main>
    </div>
</div>
<footer>
    &copy; <?= date('Y') ?> Game Nation - Panel de Administración
</footer>

<!-- Script para el menú móvil -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('mobileMenuToggle');
        const menu = document.getElementById('mobileMenu');
        const overlay = document.getElementById('mobileOverlay');

        toggle.addEventListener('click', function () {
            menu.classList.toggle('mostrar');
            overlay.classList.toggle('mostrar');
        });

        overlay.addEventListener('click', function () {
            menu.classList.remove('mostrar');
            overlay.classList.remove('mostrar');
        });

        menu.querySelectorAll('.nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                menu.classList.remove('mostrar');
                overlay.classList.remove('mostrar');
            });
        });
    });
</script>

</body>
</html>