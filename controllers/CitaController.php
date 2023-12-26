<?php

namespace Controllers;

use MVC\Router;

class CitaController{
    public static function index(Router $router){
        
        if(!isset($_SESSION)) {
            session_start();
        };
        // valida que este autenticado si no redirecciona 
        isAuth();
     
        $router->render('cita/index', [
            'nombre' => $_SESSION['nombre'],
            'id'  => $_SESSION['id']
        ]);
    }
}