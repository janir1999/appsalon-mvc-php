<h1 class="nombre-pagina">Panel de Administración</h1>

<?php
    include_once __DIR__ . '/../templates/barra.php';
?>

<h2 class="h2admin">Buscar Citas</h2>
<div class="busqueda">
    <form class="formulario">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input 
                type="date"
                id="fecha"
                name="fecha"
                value="<?php echo $fecha; ?>"
            />
        </div>
    </form>
</div>

<?php
   if(count($citas) === 0){
        echo "<h2> No Hay Citas en esta fecha</h2>";
   }
?>

<div id="citas-admin">
    <ul class="citas">
        <?php
            $idCita = 0;
            // iteramos el arreglo de citas traido desde adminController
            foreach( $citas as $key => $cita){
                // si el id de la cita se repite, la primera ves va ha ser diferente a 0 y despues 
                // como ya son iguales no se muestran hasta que haya un id de cita diferente
                // $key muestra el indice de cada arreglo
                if($idCita !== $cita->id){
                    $total = 0; // cuando se cumpla la condición se receta el total, de lo contrario se suma el precio al total en la variable abajo
        ?>
            <li class="li1">
                <p>ID: <span><?php echo $cita->id; ?></span></p>
                <p>Hora: <span><?php echo $cita->hora; ?></span></p>
                <p>Cliente: <span><?php echo $cita->cliente; ?></span></p>
                <p>Email: <span><?php echo $cita->email; ?></span></p>
                <p>Telefono: <span><?php echo $cita->telefono; ?></span></p>
                <h3>Servicios</h3>
            
                <?php
                   $idCita = $cita->id;
                } 
                //<!-- Fin if -->
                    // suma todos los precios y se imprimen abajo en el if
                    $total += $cita->precio;
                ?>  
            </li>
            <li class="li2">
            
                <!-- Si se colocamos  los servicios arriba en el if solo se muestra un servicio por que estamos evitando que se repita el id -->
                <p class="servicio"><?php echo $cita->servicio . " " . $cita->precio; ?></p>

            </li>
        <?php
            $actual = $cita->id;//id de la cita, cada servicio es un arreglo que  tiene el mismo el id de la cita actual. esto lo trae la consulta
            // a la base de datos: mirar adminControler y debuguear la consulta,  pero se simplifico el arreglo al
            // solo mostrar 1 vez los datos del la cita ej hora, cliente esto se hizo arriba. por lo cual es el mismo hasta que
            // cambie el id de cita 

            $proximo = $citas[$key + 1]->id ?? 0; //segun el arreglo actual se suma +1 y entra al siguiente indice, este valor
            // cambia cuando se recorre otra cita. recorre cuyo arreglo y identificamos el id en caso que no haya el valor es 0

            //comparamos el id de la cita $actual con el id del arreglo $proximo, 
            if(esUltimo($actual, $proximo)){ ?> 
                <p class="total">Total: <span>$ <?php echo $total; ?></span></p>

                <form class="total" action="/api/eliminar" method="POST">
                    <input type="hidden" name="id" value="<?php echo $cita->id; ?>">
                    <input type="submit" class="boton-eliminar" value="Eliminar">
            
                </form>
            <?php }
            } ?>
        <!-- Fin de Foreach -->
        
    </ul>
</div>

<?php
    $script = "<script src='build/js/buscador.js'></script>"
?>