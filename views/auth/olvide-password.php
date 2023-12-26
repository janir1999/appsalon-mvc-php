<h1 class="nombre-pagina">Olvide Password</h1>
<p class="descripcion-pagina">Reestablece tu password escribiendo tu email a continuación</p>

<?php 
    //tambien se podria poner contenido del archivo alertas en este
    include_once __DIR__ . "/../templates/alertas.php";
?>

<form action="/olvide" method="POST" class="formulario">
    <div class="campo">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Tu Email"/>
    </div>
    <input type="submit" value="Enviar Instrucciones" class="boton">
</form>


<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Session</a>
    <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear una</a>
</div>