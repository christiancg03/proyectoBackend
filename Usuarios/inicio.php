<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Game Nation - Bienvenido</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      min-height: 100vh;
      margin: 0;
      background: linear-gradient(135deg, #181c25 0%, #232943 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .contenido-principal {
      background: rgba(20, 23, 34, 0.92);
      padding: 3rem 2.5rem 2.5rem 2.5rem;
      border-radius: 1.2rem;
      box-shadow: 0 8px 32px rgba(0,0,0,0.5);
      max-width: 430px;
      width: 100%;
      text-align: center;
    }
    .titulo-principal {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.7rem;
      margin-bottom: 1rem;
    }
    .titulo-principal i {
      font-size: 2.1rem;
      color: #4be0ff;
      filter: drop-shadow(0 0 3px #222);
    }
    .contenido-principal h1 {
      font-weight: 700;
      font-size: 2.1rem;
      margin: 0;
      color: #fff;
      text-shadow: 1px 1px 8px #0e141e;
      letter-spacing: 1px;
    }
    .contenido-principal p {
      font-size: 1.18rem;
      margin-bottom: 2.2rem;
      color: #c3e6ff;
      text-shadow: 1px 1px 3px #141a2a;
    }
    .btn-acciones {
      display: flex;
      gap: 1.2rem;
      justify-content: center;
      flex-wrap: wrap;
    }
    .btn-accion {
      background: linear-gradient(135deg, #1976d2 60%, #4be0ff 100%);
      color: #fff;
      border: none;
      border-radius: 1rem;
      padding: 1.3rem 1.1rem 1.1rem 1.1rem;
      font-size: 1.12rem;
      font-weight: 600;
      box-shadow: 0 4px 16px rgba(25, 118, 210, 0.14);
      width: 150px;
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: transform 0.18s, box-shadow 0.18s, background 0.18s;
      text-decoration: none;
      margin-bottom: 0.5rem;
      position: relative;
      overflow: hidden;
    }
    .btn-accion.success {
      background: linear-gradient(135deg, #43e97b 60%, #38f9d7 100%);
      color: #18332a;
      box-shadow: 0 4px 16px rgba(67, 233, 123, 0.14);
    }
    .btn-accion i {
      font-size: 2.2rem;
      margin-bottom: 0.4rem;
      filter: drop-shadow(0 0 2px #222);
    }
    .btn-accion:hover, .btn-accion:focus {
      transform: translateY(-4px) scale(1.035);
      box-shadow: 0 8px 32px rgba(25, 118, 210, 0.22);
      background: linear-gradient(135deg, #4be0ff 0%, #1976d2 100%);
      color: #fff;
    }
    .btn-accion.success:hover, .btn-accion.success:focus {
      background: linear-gradient(135deg, #38f9d7 0%, #43e97b 100%);
      color: #18332a;
      box-shadow: 0 8px 32px rgba(67, 233, 123, 0.22);
    }
    @media (max-width: 600px) {
      .contenido-principal {
        padding: 1.5rem 0.5rem;
        max-width: 95vw;
      }
      .btn-acciones {
        flex-direction: column;
        gap: 0.7rem;
      }
      .btn-accion {
        width: 100%;
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <div class="contenido-principal">
    <div class="titulo-principal">
      <h1 class="mb-0">Game Nation</h1>
    </div>
    <p>Tu Portal de Videojuegos Preferido</p>
    <div class="btn-acciones">
      <a href="login.php" class="btn-accion" tabindex="1">
        <i class="fas fa-sign-in-alt"></i>
        <span>Iniciar Sesión</span>
      </a>
      <a href="register.php" class="btn-accion success" tabindex="2">
        <i class="fas fa-user-plus"></i>
        <span>Registrarse</span>
      </a>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>