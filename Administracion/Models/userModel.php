<?php
require_once __DIR__ . '/../config/db.php'; // Ajusta la ruta según la estructura de tu proyecto

class UserModel
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = db::conexion();
    }

    public function insert(array $user): ?int {
        try {
            $sql = "INSERT INTO usuarios (usuario, password) VALUES (:usuario, :password);";
            $sentencia = $this->conexion->prepare($sql);
            $arrayDatos = [
                ":usuario" => $user["usuario"],
                ":password" => password_hash($user["password"], PASSWORD_DEFAULT)
            ];            
            $resultado = $sentencia->execute($arrayDatos);
            return ($resultado == true) ? $this->conexion->lastInsertId() : null;
        } catch (Exception $e) {
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
            return null;
        }
    }

    public function read(int $id): ?stdClass {
        $sentencia = $this->conexion->prepare("SELECT * FROM usuarios WHERE id=:id");
        $arrayDatos = [":id" => $id];
        $resultado = $sentencia->execute($arrayDatos);
        if (!$resultado) return null;
        $user = $sentencia->fetch(PDO::FETCH_OBJ);
        return ($user == false) ? null : $user;
    }

    public function readAll(): array {
        $sentencia = $this->conexion->query("SELECT * FROM usuarios;");
        $usuarios = $sentencia->fetchAll(PDO::FETCH_ASSOC);
        return $usuarios;
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        try {
            $sentencia = $this->conexion->prepare($sql);
            $resultado = $sentencia->execute([":id" => $id]);
            return ($sentencia->rowCount() <= 0) ? false : true;
        } catch (Exception $e) {
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
            return false;
        }
    }

    public function edit(int $idAntiguo, array $arrayUsuario): bool {
        try {
            $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, usuario = :usuario, password = :password WHERE id = :id;";
            $arrayDatos = [
                ":id" => $idAntiguo,
                ":usuario" => $arrayUsuario["usuario"],
                ":password" => password_hash($arrayUsuario["password"], PASSWORD_DEFAULT),
                ":nombre" => $arrayUsuario["nombre"],
                ":email" => $arrayUsuario["email"]
            ];
            $sentencia = $this->conexion->prepare($sql);
            return $sentencia->execute($arrayDatos);
        } catch (Exception $e) {
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
            return false;
        }
    }

    public function search(string $usuario, string $campo, string $orden): array {
        $sql = "SELECT * FROM usuarios WHERE usuario LIKE :usuario ORDER BY $campo";
        switch ($orden) {
            case 'empieza':
                $arrayDatos = [":usuario" => "$usuario%"];
                break;
            case 'termina':
                $arrayDatos = [":usuario" => "%$usuario"];
                break;
            case 'contiene':
                $arrayDatos = [":usuario" => "%$usuario%"];
                break;
            case 'igual':
                $arrayDatos = [":usuario" => $usuario];
                break;
            default:
                $arrayDatos = [":usuario" => "%$usuario%"];
                break;
        }
        $sentencia = $this->conexion->prepare($sql);
        $resultado = $sentencia->execute($arrayDatos);
        if (!$resultado) return [];
        $users = $sentencia->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }

    public function login(string $usuario, string $password): ?stdClass {
        $sentencia = $this->conexion->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
        $arrayDatos = [":usuario" => $usuario];
        $resultado = $sentencia->execute($arrayDatos);
        if (!$resultado) return null;
    
        $user = $sentencia->fetch(PDO::FETCH_OBJ);
    
        return ($user == false || !password_verify($password, $user->password)) ? null : $user;
    }

    public function exists(string $campo, string $valor): bool {
        $sentencia = $this->conexion->prepare("SELECT * FROM usuarios WHERE $campo = :valor");
        $arrayDatos = [":valor" => $valor];
        $resultado = $sentencia->execute($arrayDatos);
        return (!$resultado || $sentencia->rowCount() <= 0) ? false : true;
    }
}