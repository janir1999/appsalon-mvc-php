let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

// informacion que se va enviar cuando selecciones un servicio
const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: [] // vamos a la funcion mostrarServicio para asocciar un evento
}

document.addEventListener('DOMContentLoaded', function () {
    iniciarApp();
});

function iniciarApp() {
    mostrarSeccion();
    tabs(); //Cambia la sección cuando se presionen los tabs
    botonesPaginador(); // Agrega o quita los botones del paginador
    paginaSiguiente();
    paginaAnterior();
    consultarAPI(); // Consultar la Api en el backend de php
    idCliente();
    nombreCliente() // añade el nombre de cliente al objeto de cita
    seleccionarFecha(); // añade la fecha del cliente al arreglo de cita
    seleccionarHora(); // Añade la hora de la cita en el objeto
    mostrarResumen(); // mostrar el resumen de la cita
}

// muestra la sección 1 que tiene la clase mostrar por default, cambia si se llama la funcion de tabs
function mostrarSeccion() {
    //Ocultar la sección que tenga la clase de mostrar
    const seccionAnterior = document.querySelector('.mostrar');
    // si exite la clase de mostrar en las seciones la oculta
    if (seccionAnterior) {
        seccionAnterior.classList.remove('mostrar');
    }

    // Seleccionar la sección con el paso..
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');


    //Quita la clase de actual al tab anterior
    const tabAnterior = document.querySelector('.actual');
    if (tabAnterior) {
        tabAnterior.classList.remove('actual');
    }

    //Resalta el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');

}


function tabs() {
    // seleccionas los botones que esten dentro de esa clase
    const botones = document.querySelectorAll('.tabs button');

    // itera sobre ellos
    // sin arroy function = botones.forEach( function(boton) {
    botones.forEach(boton => {
        boton.addEventListener('click', function (e) {
            // e=evento.targe=para seleccionar.dataset.paso = selecciona el datapaso y su valor que viene del html
            // cambia el valor del paso
            paso = parseInt(e.target.dataset.paso);
            // llamamo la sección
            mostrarSeccion();
            // y llamamos la funcion que muestra los botones dependiendo de valor del paso
            botonesPaginador();


        });
    });

}
function botonesPaginador() {
    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    //paso 1 = servicios, paso 2 = infocita y paso 3 = resumen.
    if (paso === 1) {
        //oculta el boton anterior
        paginaAnterior.classList.add('ocultar');
        // si volvemos al paso 1 y el boton siguiente tiene la clase de ocultar agregada por que visitamos otro paso se la removemos
        paginaSiguiente.classList.remove('ocultar');
    } else if (paso === 3) {
        // por si se oculto el boton antes lo volvemos a mostrar en este paso
        paginaAnterior.classList.remove('ocultar');
        // ocultar el boton de siguiente
        paginaSiguiente.classList.add('ocultar')
        
        mostrarResumen();
    } else {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }
    // al ser llamada function botonesPaginador por las funciones
    // pagina anterior o siguiente: evalua y le aplica o remueve las clases
    // despues de esto se manda a llamar la sección donde se aplican estas condiciones y se muestran
    mostrarSeccion();
}

function paginaAnterior() {
    const paginaAnterior = document.querySelector('#anterior')
    paginaAnterior.addEventListener('click', function () {
        //si paso es menor o igual a 1 se retorna true de lo contrario se le resta 1 paso--
        if (paso <= pasoInicial) return;
        paso--;

        // llamamos el paginador que valida los botones y manda a llamar las secciones
        // segun el valor del paso

        botonesPaginador();
    });
}
function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente')
    paginaSiguiente.addEventListener('click', function () {
        //si paso es mayor o igual a 3 se retorna true de lo contrario se le suma 1 paso--
        if (paso >= pasoFinal) return;
        paso++;

        // llamamos el paginador que valida los botones y manda a llamar las secciones
        // segun el valor del paso

        botonesPaginador();
    });
}

//////////////////////API/////////////////////////////////////////

async function consultarAPI() {
    try {
        //const url = 'http://localhost:3000/api/servicios'; //donde esta nuestra api
        //const url = `${location.origin}/api/servicios`; otro dominio
        const url = '/api/servicios'; // si queda todo en un mismo dominio
        const resultado = await fetch(url); //la funcion que nos va permitir consumir el servicio: /servicios
        const servicios = await resultado.json();// leer el json
        mostrarServicios(servicios);
    } catch (error) {
        console.log(error);
    }
}
function mostrarServicios(servicios) {
    servicios.forEach(servicio => {
        const { id, nombre, precio } = servicio;

        // Crear parrafos con su devido valor
        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;

        // Crear un div
        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        // lo que crea la linea de abajo: data-id-servicio ="1"
        servicioDiv.dataset.idServicio = id;
        // cuando demos click en el servicioDiv se ejecuta la funcion onclick y se ejecuta una función
        // al dar click se selecciona el servicio segun el valor el dataset
        servicioDiv.onclick = function () // function() es un colback
        // no sirve si manda a llamar la funcion directamente: servicioDiv.onclick = seleccionarServicio(servicio);
        // por que no se ejecutaria la funcion que esta mas abajo de: function seleccionarServicio(servicio){}
        {
            seleccionarServicio(servicio);
        }

        // le agregamos al div los parrafos con los valores
        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        // lo agregamos al index de citas en el div con el id de servicios
        document.querySelector('#servicios').appendChild(servicioDiv);

    });

}
function seleccionarServicio(servicio) {
    const { id } = servicio;
    // extraer el arreglo de servicios que esta arriba dentro de la constante de cita
    const { servicios } = cita;

    

    // seleccionar el data set segun el id del arreglo de servicios
   
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

    //some va a iterar sobre un arreglo y retorna true o false aun array method
    //Agregado.id es el id del arrego de { servicios } de cita que se sobreescribio abajo [...servicios, servicio];
    // id = servicio.id pero con el destruction que da solo el id, es el id al dar click  y el arreglo antes de rescribirlo en en ..servicios
    // compara ambos id
    if (servicios.some(agregado => agregado.id === id)) {
        // eliminarlo: 
        // 1. servicios.filter recorre todos los registros del arreglo servicios 
        // 2. ( agregado => agregado.id !== id); cada uno de los servicios(agregado.id) se compra con el id de servicio  
        // que que se paso en la comparacion anterior
        // 2.2 dicha condicion solo muestra o imprime los arreglos que tienen  el id(agregado.id) difenrente al id de servicio (servicio.id) = id
        // 2.3 al ser igual el id seleccionado al de servicio no se muestra y se excluye ese arreglo
        // 3. Almacena en un nuevo arreglo cita.servicio esto quiere decir el arreglo de servicios[] que esta dentro de cita arriba  
        cita.servicios = servicios.filter( agregado => agregado.id !== id); 
        //se quita la clase de seleccionado segun el id
        divServicio.classList.remove('seleccionado');
        
    } else {
        // toma una copia de lo que hay en memoria ...servicios y se la asigna los valores que tiene servico
        // si no tienen los ... pasa el primer arreglo vacio y los demas no 
        // cita.servicios se reescribe segun = el valor de [...servicios, servicio];
        cita.servicios = [...servicios, servicio];
        divServicio.classList.add('seleccionado');
    }

    console.log(cita);
}

// agregar los datos de const cita con sus servicios
function idCliente(){
    cita.id = document.querySelector('#id').value;
}

function nombreCliente(){
    cita.nombre = document.querySelector('#nombre').value;
}

function seleccionarFecha(){
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e){
        const dia = new Date(e.target.value).getUTCDay();

        // si el dia es sabado = 6 o domingo = 0
        if( [6, 0].includes(dia)){
            // si se cumple esta condición no cambia el valor del campo del form
            e.target.value = '';
            mostrarAlerta('Fines de semana no permitidos', 'error', '.formulario');
        } else {
            // de lo contrario si ser agrega la fecha en el form y en la const cita
            cita.fecha = e.target.value;
        }
    });
}
function seleccionarHora(){
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e){

        const horaCita = e.target.value;
        const hora = horaCita.split(":")[0];
        console.log(hora);
        //si la hora es menor a las 10 o si es mayor que las 18
        if(hora < 10 || hora > 18){
            e.target.value = '';
            mostrarAlerta('Hora No Válida', 'error', '.formulario');
        }else{
            cita.hora = e.target.value;
            console.log(cita);
        }
    });
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true){
    //  previene que se generen más de 1 alerta
    const alertaPrevia = document.querySelector('.alerta');
    if(alertaPrevia){
        alertaPrevia.remove();
    }

    // scripting para crear la alerta
    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    if(desaparece){
        // Eliminar alerta 
        setTimeout(() =>{
            alerta.remove();
        }, 3000);
    }
   
}

function mostrarResumen(){
    const resumen = document.querySelector('.contenido-resumen');

    // Limpiar el Contenidode resumen
    while(resumen.firstChild){ // trad. primer hijo
        resumen.removeChild(resumen.firstChild);
    }

    // iteramos sobre los valores de cita y si incluye un valor vacio hacen falta datos
    // o si no hay ningun servicio seleccionado length=longitud
    if(Object.values(cita).includes('') || cita.servicios.length === 0 ){
        mostrarAlerta('Faltan datos de Servicios, Fecha u Hora', 'error', '.contenido-resumen',
        false);

        return;
    }
    
    // Formatear el div de resumen
    const { nombre, fecha, hora, servicios} = cita;

    // Heading para Servicios en Resumen
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headingServicios);

    // Iterando y mostrando los servicios
    servicios.forEach(servicio =>{
        const {id, precio, nombre} = servicio;
        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');
        
        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);

    });

    // Heading para Citas en Resumen
    const headingCita = document.createElement('H3');
    headingCita.textContent = 'Resumen de Cita';
    resumen.appendChild(headingCita);

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    // formatear la hora en español
    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth(); // traer el mes
    const dia = fechaObj.getDate() + 2; // al instanciarce 2 veces el Date se restantan 2 dias por eso se suman 2 
    const year = fechaObj.getFullYear(); // traer el mes

    const fechaUTC = new Date(Date.UTC(year, mes , dia));

    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'} // si quieres que el dato se muestre de forma numerica o como string=long
    const fechaFormateada = fechaUTC.toLocaleDateString('es-CO', opciones); // formateamos la fecha 

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`;

    //Boton para Crear una Cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita; // funcion reservar cita al dar click

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);
    resumen.appendChild(botonReservar);

}

async function reservarCita(){

    const { nombre, fecha, hora, servicios, id} = cita
    // itera sobre todos los servicios con map, le pasa todas la coincidencias uo todos los id a la variable idServicio
    const idServicios = servicios.map( servicio => servicio.id)

    const datos = new FormData();
    //  obtenerlo en post  'fecha' y se le pasa la variable con el valor
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('usuarioId', id);
    datos.append('servicios',idServicios);

    // toma una copia de datos y lo formatea en ese arreglo [...datos] y validar lo que se esta enviando en datos.append
    //console.log([...datos])
    try {
        // Petición hacia la Api
        const url = '/api/citas'

        // trae la respuesta de esa url con los datos 
        const respuesta = await fetch(url, {
            method: 'POST', // manda los datos por medio del metodo post 
            body: datos //Datos va ha ser el cuerpo que va a contener los datos añadidos arriba ej: datos.append('nombre', nombre);
            //y de esta forma fecth puede reconocer que hay el formData
        
        }); 

        // leer la api pero sin thunder o postman
        // lee la respuesta json 
        const resultado = await respuesta.json();
        // acceder al resultado que retorna de la funcion crear de active record 
        console.log(resultado.resultado);
        // resultado de const resultado y resultado de active record de lo que retorna la funcion crear 'resultado' =>  $resultado de la base de datos
        if(resultado.resultado){ // si el resuldado de que retorna la funcion crear es true se ejecuta la notificación
            Swal.fire({
              icon: "success",
              title: "Cita Creada",
              text: "Cita creada correctamente"
            }).then(() => {
                // recarga la pagina
                setTimeout(() => {
                    window.location.reload();
                }, 3000);     
            })   
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: " Hubo un error al guardar la cita"
          })
    }
    
}