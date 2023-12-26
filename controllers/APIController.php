<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController{
    public static function index(){
        $servicios = Servicio::all();
        //conviente el arreglo en un json
        // los arreglos asociativos son lo mismo que un objeto en java scrip por eso se 
        // permite convirtir $servicios a un json
        echo json_encode($servicios);
    }

    public static function guardar(){
        // Almacena la cita y devuelve el ID
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();
        // trae el id que retorna cita al llamar la funcion guardar y luego crear
        $id = $resultado['id'];

        // Almacena los Servicios con el ID de la cita
        // los servicios de la api llegan como un string y con explode los volvemos un arreglo
        $idServicios = explode(",", $_POST['servicios']);

        foreach($idServicios as $idServicio){
            // le pasamos los valores al constructor de la clase CitaServicio
            $args = [
                'citaId' => $id,
                'servicioId' => $idServicio
            ];
            // le pasamos los args a la clase  citaServicio y los guardamos en la base de datos
            $citaServicio = new CitaServicio($args);
            $citaServicio->guardar();

        }
        // respuesta que retorna la funcion crear en activeRecord
        echo json_encode(['resultado' => $resultado]);
    }
    public static function eliminar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id'];
            $cita = Cita::find($id); // llena el constructor de Cita
            $cita->eliminar(); // eliminamos segun el id de la tabla de Citas en DB
            // redirecciona a la misma pagina con la fecha
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }
}

?>