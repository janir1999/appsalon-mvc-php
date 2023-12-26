<?php

namespace Controllers;

use Model\Servicio;
use MVC\Router;

class ServicioController{
    public static function index(Router $router){
        
        if(!isset($_SESSION)) {
            session_start();
        };

        isAdmin();
        
        $servicios = Servicio::all();

        $router->render('servicios/index',[
            'nombre' => $_SESSION['nombre'],
            'servicios' => $servicios
        ]);
    }
    public static function crear(Router $router){
        
        if(!isset($_SESSION)) {
            session_start();
        };

        isAdmin();

        $servicio = new Servicio;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
           
            $servicio->sincronizar($_POST);
        
            $alertas = $servicio->validar();

            if(empty($alertas)){
                $servicio->guardar();
                header('Location: /servicios');
            }
        }
        $router->render('servicios/crear',[
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas

        ]);
    }

    public static function actualizar(Router $router){
        
        if(!isset($_SESSION)) {
            session_start();
        };

        isAdmin();

        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        // en caso que no exista un id o no sea un valor numerico
        //if(!is_numeric($_GET['id'])) return;
        //si el valor de id no es un int redirecciona
        if(!$id){
            header('Location: /servicios');
        } 
        $servicio = Servicio::find($id);
        //debuguear($servicio);
        if($servicio === null){
            header('Location: /servicios');
        }

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $servicio->sincronizar($_POST);

            $alertas = $servicio->validar();

            if(empty($alertas)){
                $servicio->guardar();
                header('Location: /servicios');
            }
        }
        $router->render('servicios/actualizar',[
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas
        ]);
    }

    public static function eliminar( ){

        if(!isset($_SESSION)) {
            session_start();
        };

        isAdmin();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id'];
            $servicio = Servicio::find($id);
            $servicio->eliminar();
            header('Location: /servicios');

        }
        
    }
}