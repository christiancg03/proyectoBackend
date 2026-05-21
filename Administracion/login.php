<?php
  session_start();
  $user=($_POST["usuario"])??"";
  $password=($_POST["password"])??"";
  $error=false;
  if (isset ($_POST["usuario"],$_POST["password"])){
      require_once "models/userModel.php";
      $userModel= new userModel();
      $usuario=$userModel->login($user, $password);
      if ($usuario != null) {
        if ($usuario->rol === 'admin' || $usuario->rol === 'moderador') {
            $_SESSION["usuario"] = $usuario;
            header("location:index.php");
            exit();
        } else {
            $msg = "No tienes permisos para acceder desde aquí.";
            $visibilidad = "visible";
            $style = "alert-danger";
        }
      }
      $error=true;
  }
  $msg="";$visibilidad="";$style="";
  if ($error){
    $msg="Error, Usuario o Password Incorrectos";
    $visibilidad="visible";
    $style="alert-danger";
  }
  if(isset($_GET["session"])&&($_GET["session"]=="logout")){
    $msg="Fin de Sesion";
    $visibilidad="visible";
    $style="alert-success";
  }
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administración - Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html, body {
            height: 100%;
        }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e8f1f7 0%, #b5c6d6 100%);
            color: #22304a;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 6px 36px #22304a22;
            padding: 2.5rem 2.2rem 2rem 2.2rem;
            min-width: 340px;
            max-width: 370px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #e3e6ea;
            margin-bottom: 1rem;
            margin-top: 0.5rem;
            box-shadow: 0 2px 12px #2196f344;
        }
        .login-logo i {
            font-size: 2.5rem;
            color: #2196f3;
        }
        .titulo-login {
            color: #22304a;
            font-weight: 700;
            font-size: 1.55rem;
            margin-bottom: 1.2rem;
            letter-spacing: 0.5px;
            text-align: center;
        }
        .form-floating input {
            background-color: #f6f8fa;
            color: #22304a;
            border-radius: 8px;
            border: 1.5px solid #b5c6d6;
            margin-bottom: 1.1rem;
            font-size: 1.07rem;
            box-shadow: none;
            transition: border-color 0.18s;
        }
        .form-floating input:focus {
            border-color: #2196f3;
            box-shadow: 0 0 8px #2196f355;
            color: #22304a;
        }
        .form-floating label {
            color: #7a8ca3;
        }
        .btn-login {
            background-color: #2196f3;
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 8px;
            width: 100%;
            margin-bottom: 0.7rem;
            font-size: 1.1rem;
            padding: 0.95rem 0;
            transition: background 0.2s;
            box-shadow: 0 2px 12px #2196f322;
        }
        .btn-login:hover {
            background-color: #1769aa;
        }
        .alert {
            margin-bottom: 1.1rem;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1.02rem;
        }
        .alert-danger {
            background-color: #e57373;
            color: #fff;
            border: 1px solid #c62828;
        }
        .alert-success {
            background-color: #81c784;
            color: #fff;
            border: 1px solid #388e3c;
        }
        .form-check-label {
            font-size: 0.93rem;
            color: #7a8ca3;
        }
        .form-check-input:checked {
            background-color: #2196f3;
            border-color: #2196f3;
        }
        @media (max-width: 500px) {
            .login-container {
                padding: 1.3rem 0.5rem;
                min-width: 98vw;
                max-width: 99vw;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <i class="fa-solid fa-user-shield"></i>
        </div>
        <div class="titulo-login">Panel de Administración</div>

        <form action="<?= $_SERVER['PHP_SELF']?>" method="POST" autocomplete="off">
            <div class="alert <?=$style.' '.$visibilidad?>"><?=$msg?></div>

            <div class="form-floating">
                <input type="text" class="form-control" id="usuario" name="usuario" value="<?= htmlspecialchars($user) ?>" placeholder="Usuario" required autofocus>
                <label for="usuario"><i class="fa-solid fa-user"></i> Usuario</label>
            </div>

            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password"><i class="fa-solid fa-lock"></i> Contraseña</label>
            </div>

            <div class="form-check text-start mb-3">
                <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault">
                <label class="form-check-label" for="flexCheckDefault">
                    Recuérdame
                </label>
            </div>

            <button class="btn-login" type="submit">
                <i class="fa-solid fa-arrow-right-to-bracket"></i> Iniciar Sesión
            </button>
        </form>
    </div>
</body>
</html>
