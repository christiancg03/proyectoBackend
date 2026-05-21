<?php
require_once __DIR__ . '/../models/userModel.php'; // Ajusta la ruta según la estructura de tu proyecto

class UsersController { 
    private $model;

    public function __construct(){
        $this->model = new UserModel();
    }

    public function crear (array $arrayUser):void {
        $id=$this->model->insert ($arrayUser);
        ($id==null)?header("location:index.php?tabla=user&accion=crear&error=true&id={$id}"): header("location:index.php?tabla=user&accion=ver&id=".$id);
        exit ();
    }

    public function ver(int $id): ?stdClass
    {
        return $this->model->read($id);
    }

    public function listar (){
        return $this->model->readAll ();
   }

   public function borrar(int $id ): void
   {   
       $usuario= $this->ver($id);
       $borrado = $this->model->delete($id);
       $mensaje = $borrado ? 'Usuario eliminado exitosamente.' : 'Error al eliminar el usuario.';
       $redireccion = "location:/Digimon/Administracion/views/user/list.php?mensaje=" . urlencode($mensaje);
       header($redireccion);
       exit();
   }
}