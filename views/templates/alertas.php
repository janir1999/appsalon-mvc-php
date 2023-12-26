<?php
    foreach($alertas as $key => $mensajes):
        //debuguear($key) por si las dudas
        foreach($mensajes as $mensaje):
?>
    <div class="alerta <?php echo $key ?>">
    <?php echo $mensaje ?>
    </div>
<?php
        endforeach;
    endforeach;
?>