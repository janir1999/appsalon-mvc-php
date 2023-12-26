<?php

namespace Model;

class Usuario extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email',
    'password', 'telefono', 'admin','confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;
    
    public function __construct($args = []){ 
        //args es el name
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? '0';
        $this->confirmado = $args['confirmado'] ?? '0';
        $this->token = $args['token'] ?? '';
    }

    //Mensajes de validación para la creación de una cuenta
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'El Nombre del Cliente es Obligatorio';
        }
        if(!$this->apellido){
            self::$alertas['error'][] = 'El Apellido del Cliente es Obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        if(!$this->telefono){
            self::$alertas['error'][] = 'El telefono es Obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El Password es Obligatorio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'El Password debe de Contener al menos 6 Caracteres';
        }
        return self::$alertas;
    }

    public function validarLogin(){
        
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }  
        if(!$this->password){
            self::$alertas['error'][] = 'El Password es Obligatorio';
        }
        
        return self::$alertas;
    }

    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }  
        return self::$alertas;
    }
    public function validarPassword(){

        if(!$this->password){
            self::$alertas['error'][] = 'El Password es Obligatorio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'El Password debe de Contener al menos 6 Caracteres';
        }
        
        return self::$alertas;
    }

    // Revisa si el usuario ya existe
    public function existeUsuario(){
        $query = " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";

        $resultado = self::$db->query($query);
        
        // si al hacer la consulta hay un resultado el usuario ya exite o el email ya existe
        if($resultado->num_rows){
            self::$alertas['error'][] = 'El Usuario ya esta registrado';
        }
     
        return  $resultado;
    }

    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken(){
        // uniqid genera un id unico
        $this->token = uniqid();
    }

    public function comprobarPasswordAndVerificado($password)
    {   // compara el password que le pasamos ($password) en el post,
        // con el password del resultado al buscar el email en el controlador: loginController funcion=index
        // al validar el email queda guardado en memoria el resultado como un objeto y ese password se compara con el del post
        // $usuario = Usuario::where('email', $auth->email);      
        $resultado = password_verify($password, $this->password);
        // si el  $resultado es false o no esta confirmado
        if(!$resultado || !$this->confirmado){
            self::$alertas['error'][] = 'Password incorrecto o tu cuenta no ha sido confirmada';
        }else{
            return true;
        }
    }
}
