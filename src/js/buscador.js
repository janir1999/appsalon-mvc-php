document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp(){
    buscarPorFecha();
}

function buscarPorFecha(){
    const fechaInput = document.querySelector('#fecha');
    // pasamos el evento en otra funcion como callback
    fechaInput.addEventListener('input', function(e){
        const fechaSeleccionada = e.target.value;

        // recargamos la pagina y mostramos la fecha en la url junto con la ruta
        window.location = `?fecha=${fechaSeleccionada}`;
    })
}