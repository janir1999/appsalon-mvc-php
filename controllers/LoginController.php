<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController
{
    public static function login(Router $router)
    {

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                // Comprobar que exista el usuario
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario) {
                    // Verificar el password
                    // ($auth->password) que viene del post
                    // validamos el valor que trajo la funcion si true o false
                    if ($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //redireccionamiento
                        if ($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no Encontrado');
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout()
    {
        session_start();

        $_SESSION = [];

        header('Location: /');
    }

    public static function olvide(Router $router)
    {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if (empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario && $usuario->confirmado === "1") {
                    // generar un token 
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // mensaje de exito
                    Usuario::setAlerta('exito', 'Revisa tu email');
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
            }
        }
        $alertas = Usuario::getAlertas();

        // vista o archivo auth/olvide-password  que se muestra al llamar la 
        // url /olvide
        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router)
    {
        $alertas = [];
        $error = false;

        $token = s($_GET['token']);

        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        //debuguear($usuario);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token no Válido');
            $error = true;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            // que el campo no este vacio
            $alertas = $password->validarPassword();

            if (empty($alertas)) {
                // resetear el password del objeto que se llamo al buscar el usuario 
                // por el token
                $usuario->password = null;

                // $usuario->password : password al buscar el usuario por el token
                // $password->password : enviado por medio del post
                $usuario->password = $password->password;
                $usuario->hashPassword();
                // cambia el token del objeto a null
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if ($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crear(Router $router)
    {
        $usuario = new Usuario;
        //debuguear($usuario);
        // Alertas Vacias
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //debuguear($_POST);
            // se llama la funcion de sincronizar y se pasa el post
            // que es un array con los datos y convierte el arreglo en un objeto
            $usuario->sincronizar($_POST);
            // debuguear($usuario);
            $alertas = $usuario->validarNuevaCuenta();

            //Revisar que alerta este vacio
            if (empty($alertas)) {
                // Verificar que el usuario no esta registrado
                $resultado = $usuario->existeUsuario();

                //Si existe el correo manda la alerta
                if ($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // No esta registrado
                    // Hashear el Password
                    $usuario->hashPassword();

                    //Generar un Token único
                    $usuario->crearToken();

                    // enviar el email
                    // pasamos la informacion a la clase de Email
                    $email = new Email(
                        $usuario->nombre,
                        $usuario->email,
                        $usuario->token
                    );

                    $email->enviarConfirmacion();

                    // crear usuario
                    $resultado = $usuario->guardar();

                    if ($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }
        }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }
    public static function mensaje(Router $router)
    {
        $router->render('auth/mensaje');
    }
    public static function confirmar(Router $router)
    {
        $alertas = [];
        // leer el token de url
        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            //Mostrar mensaje de error
            Usuario::setAlerta('error', 'token No Válido');
        } else {
            $usuario->confirmado = "1";
            $usuario->token = "";
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        //Obtener alertas
        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}
