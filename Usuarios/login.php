<?php
  session_start();
  $user=($_POST["usuario"])??"";
  $password=($_POST["password"])??"";
  $error=false;
// aquí no usamos MVC ya que es muy básica la acción
  if (isset ($_POST["usuario"],$_POST["password"])){
    //podríamos usar el controlador pero como sólo es hacer una consulta
      require_once "models/userModel.php";
      $userModel= new userModel();
      $usuario=$userModel->login($user, $password);
      if ($usuario != null) {
    // Solo permitir acceso si es rol usuario
    if ($usuario->rol === 'usuario') {
        $_SESSION["usuario"] = $usuario;
        header("location:index.php");
        exit();
    } else {
        $msg = "No tienes permisos para acceder desde aquí.";
        $visibilidad = "visible";
        $style = "alert-danger";
    }
}
      //SI NO EXISTE EL USUARIO SE HA PRODUCIDO UN ERROR
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
    <title>Game Nation - Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');
        
        body {
            background: linear-gradient(135deg, #0d0f1c 0%, #23263a 100%);
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .contenedor-principal {
            background: #1c1f2e;
            border-radius: 20px;
            box-shadow: 0 0 40px #00f0ff33;
            width: 800px;
            display: flex;
            overflow: hidden;
        }

        .seccion-titulo {
            background: linear-gradient(45deg, #00f0ff22, #23263a);
            padding: 4rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            flex: 1;
        }

        .icono-login {
            font-size: 5rem;
            color: #00f0ff;
            text-shadow: 0 0 20px #00f0ff88;
            background: #0d0f1c;
            padding: 1.5rem;
            border-radius: 50%;
            border: 4px solid #00f0ff;
            transition: transform 0.3s;
        }

        .icono-login:hover {
            transform: rotate(15deg) scale(1.1);
        }

        .form-section {
            padding: 3rem 4rem;
            flex: 1;
        }

        h1 {
            color: #00f0ff;
            font-family: 'Press Start 2P', cursive;
            font-size: 1.8rem;
            text-align: center;
            margin-bottom: 2.5rem;
            text-shadow: 0 0 10px #00f0ff88;
        }

        .form-floating input {
            background: #2a2d3e;
            border: 2px solid #00f0ff33;
            color: #fff;
            border-radius: 8px;
            margin-bottom: 1.2rem;
        }

        .form-floating input:focus {
            border-color: #00f0ff;
            box-shadow: 0 0 10px #00f0ff55;
        }

        .form-floating label {
            color: #7a7a7a;
            padding-left: 0.5rem;
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background: #00f0ff;
            color: #0d0f1c;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1.1rem;
            transition: all 0.3s;
            margin-top: 1rem;
        }

        .btn-login:hover {
            background: #00c7e6;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px #00f0ff66;
        }

        .form-check-label {
            color: #aaa;
            font-size: 0.9rem;
        }

        .alert {
            border-radius: 8px;
            font-weight: bold;
            margin-bottom: 1.5rem;
        }

        .register-link {
            color: #00f0ff;
            text-align: center;
            display: block;
            margin-top: 1.2rem;
            font-size: 1rem;
        }
        .register-link a {
            color: #00f0ff;
            text-decoration: underline;
            transition: color 0.2s;
        }
        .register-link a:hover {
            color: #fff;
        }
        .recuperar_contra{
            color: #00f0ff;
            text-align: center;
            display: block;
            margin-top: 1.2rem;
            font-size: 1rem;
            display: flex;
            margin-bottom: 5%;
            margin-top: 5%;
        }
        .recuperar_contra a{
            color: #00f0ff;
            text-decoration: underline;
            transition: color 0.2s;
        }
        .recuperar_contra a:hover{
            color: #fff;
        }

        @media (max-width: 768px) {
            .contenedor-principal {
                flex-direction: column;
                width: 95%;
                margin: 1rem;
            }
            
            .seccion-titulo {
                padding: 2rem;
            }
            
            .form-section {
                padding: 2rem;
            }
            
            h1 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="contenedor-principal">
        <div class="seccion-titulo">
            <i class="fas fa-gamepad icono-login"></i>
            <h1>INICIAR SESIÓN<br>EN GAME NATION</h1>
        </div>
        
        <div class="form-section">
            <form action="<?= $_SERVER['PHP_SELF']?>" method="POST">
                <div class="alert <?=$style.' '.$visibilidad?>"><?=$msg?></div>

                <div class="form-floating">
                    <input type="text" class="form-control" id="usuario" name="usuario" value="<?= $user?>" placeholder="Usuario" required>
                    <label for="usuario"><i class="fas fa-user"></i> Usuario</label>
                </div>

                <div class="form-floating">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password"><i class="fas fa-lock"></i> Contraseña</label>
                </div>

                <div class="recuperar_contra">
                    <a href="/GameNation/Usuarios/views/user/recuperar_contra.php">Recuperar Contraseña</a>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault">
                    <label class="form-check-label" for="flexCheckDefault">
                        Recordar mis datos
                    </label>
                </div>

                <button class="btn-login" type="submit">
                    <i class="fas fa-sign-in-alt"></i> ACCEDER
                </button>
            </form>
            <div class="register-link">
                ¿No tienes una cuenta? <a href="register.php">Regístrate</a>
            </div>
        </div>
    </div>
</body>
</html>