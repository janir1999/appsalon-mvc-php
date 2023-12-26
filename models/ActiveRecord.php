<?php

namespace Model;

class ActiveRecord
{

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];

    // Alertas y Mensajes
    protected static $alertas = [];

    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database)
    {
        self::$db = $database;
    }

    public static function setAlerta($tipo, $mensaje)
    {
        static::$alertas[$tipo][] = $mensaje;
    }

    // Validación
    public static function getAlertas()
    {
        return static::$alertas;
    }

    public function validar()
    {
        static::$alertas = [];
        return static::$alertas;
    }

    // Consulta SQL para crear un objeto en Memoria
    public static function consultarSQL($query)
    {
        // Consultar la base de datos
        $resultado = self::$db->query($query);
        // Iterar los resultados
        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            // le pasamos el arreglo accosiativo a ($registro linea de arriba) a la funcion de crear objeto 
            // y cuando convierta el arreglo a un objetos se lo pasamos al array lo retorna
            $array[] = static::crearObjeto($registro);
        }

        // liberar la memoria
        $resultado->free();

        // retornar los resultados
        return $array;

    }

    // Crea el objeto en memoria que es igual al de la BD
    protected static function crearObjeto($registro)
    {
        // crea un nueva instacia con lo que tenemos en el constructor de la clase que usemos
        // pasa un objeto vacio que parece un arreglo vacio pero es un objeto
        //podemos usar el debugear(usuario);
        $objeto = new static;
        foreach ($registro as $key => $value) {
            // valida si nuevo objeto tiene una key 
            if (property_exists($objeto, $key)) {
                //a la key del objeto le pasamos el valor del arreglo associatico de la base de datos
                // y retornamos un objeto pero como ese se pasa a consultarSQL se vuelve un arreglo y dentro 
                // el objeto, si se llama la funcion SQL muestra todos lo resultados de lo
                // contrario si se llamam where solo el primero
                $objeto->$key = $value;
            }
        }
       
        return $objeto;
    }

    // Identificar y unir los atributos de la BD
    public function atributos()
    {
        $atributos = [];
        // itera sobre las columnas que se le hayan pasado en su debida clase
        foreach (static::$columnasDB as $columna) {
            if ($columna === 'id') continue; //ignora el id muestra el arreglo son id
            $atributos[$columna] = $this->$columna; 
        // une el arreglo que trae el post, nota: el constructor de esa clase tendria los valores del arreglo de post
        // al tener el arreglo de post en memoria lo mandamos a llamar con $this se une con la variable de $columna = arreglo de columnasDB iterado
        // se unen los 2 arreglos ej: con las llaves que coincidan. Crea un nuevo arreglo ya con valores, donde el nombre de llave es el mismo  
        // que tienen los campos de la tabla que se seleccione, nota: $columnasDB estos campo se definen en su respectiva classe
        // se crea un nuevo arreglo  $atributos[$columna] y se asigna a la variable de $atributos la cual va a tener este nuevo arreglo
        }
        return $atributos;
    }

    // Sanitizar los datos antes de guardarlos en la BD
    public function sanitizarAtributos()
    {
        // antes de sanitizar se llama la funcion de atributos
        $atributos = $this->atributos(); // trae la el arreglo que se unio
        $sanitizado = [];
        foreach ($atributos as $key => $value) {
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;
    }

    // Sincroniza BD con Objetos en memoria
    public function sincronizar($args = [])
    {   // pasa el post que es un arreglo  y lo convierte en un objeto
        // incluyendo los valores
        foreach ($args as $key => $value) {
            // $this arreglo que puede ver con: 
            //$usuario = new Usuario; debuguear($usuario); en logincontroller
            // $key llave del arreglo al llamar el post o enviar el formulario: debuguear($_POST);
            // valida que los dos arreglos tengan key y exista esta llave
            // y que el  is_null($value) que np sea un valor vcio
            if (property_exists($this, $key) && !is_null($value)) {
                //$this = arreglo que pasan las clases o la clase y se 
                //le pasa -> $key llave del arreglo al llamar el post o enviar el formulario: debuguear($_POST);
                // a dicho arreglo se le pasa el value que trae el arreglo del post
                $this->$key = $value;
                // mescla los el arreglo del post con el objeto vacio de la clase y retorna un objeto con los valores
            }
        }
    }

    // Registros - CRUD
    public function guardar()
    {
        $resultado = '';
        if (!is_null($this->id)) {
            // actualizar
            $resultado = $this->actualizar();
        } else {
            // Creando un nuevo registro
            $resultado = $this->crear();
        }
        return $resultado;
    }

    // Todos los registros
    public static function all()
    {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busca un registro por su id
    public static function find($id)
    {
        $query = "SELECT * FROM " . static::$tabla  . " WHERE id = {$id}";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    // Obtener Registros con cierta cantidad
    public static function get($limite)
    {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT {$limite}";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    // Busca un registro por su token
    public static function where($columna, $valor)
    {
        $query = "SELECT * FROM " . static::$tabla  . " WHERE {$columna} = '{$valor}'";
        $resultado = self::consultarSQL($query); // crea un arreglo y dentro objetos 
        return array_shift($resultado); // array_ saca el primer elemento de un arreglo sin tener que usar ej: resultado[0]->imagen
    }
     // Consulta plana de SQL (utilizar cuando los métodos del modelo no son suficientes) left join
     public static function SQL($columna)
     {
         $query = $columna;
         $resultado = self::consultarSQL($query); // crea un arreglo y dentro objetos 
         return $resultado; // sin usar array_shift
     }

    // crea un nuevo registro
    public function crear()
    {
        //el arreglo que trae post se Sanitizar primero y luego realiza la consulta
        $atributos = $this->sanitizarAtributos();
       

        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES (' ";
        $query .= join("', '", array_values($atributos));
        $query .= " ') ";

        // ver los errores cuando se mando a crear un registro por medio de una api
        // return json_decode(['query' => $query])

        // Resultado de la consulta
        $resultado = self::$db->query($query);
        return [
            'resultado' =>  $resultado,
            'id' => self::$db->insert_id
        ];
    }

    // Actualizar el registro
    public function actualizar()
    {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Iterar para ir agregando cada campo de la BD
        $valores = [];
        foreach ($atributos as $key => $value) {
            $valores[] = "{$key}='{$value}'";
        }

        // Consulta SQL
        $query = "UPDATE " . static::$tabla . " SET ";
        $query .=  join(', ', $valores);
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 ";

        // Actualizar BD
        $resultado = self::$db->query($query);
        return $resultado;
    }

    // Eliminar un Registro por su ID
    public function eliminar()
    {
        $query = "DELETE FROM "  . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado;
    }
}
