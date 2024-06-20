var sesion=localStorage.getItem('usuario') || "null";

if(sesion=="null"){
    window.location.href="index.html"
}


const cargarNombre=async()=>{

    datos=new FormData();
    datos.append("usuario",sesion);
    datos.append("action","select");

    let respuesta=await fetch("php/loginUsuario.php",{method:'POST',body:datos});
    let json=await respuesta.json();

    if(json.success==true){
        document.getElementById("user").innerHTML=json.mensaje;
        document.getElementById("foto_perfil").src="php/"+json.foto;
    }else{
    Swal.fire({title:"ERROR",text:json.mensaje,icon:"error"});
    }
}

document.getElementById("salir").onclick=()=>{
    Swal.fire({
        title:"¿Está seguro de Cerrar Sesión?",
        showDenyButton:true,
        confirmButtonText:"Si",
        denyButtonText:`No`
    }).then((result)=>{
if(result.isConfirmed){
localStorage.clear();
window.location.href="index.html"
}
});
}


const cargarPerfil=async()=>{

    datos=new FormData();
    datos.append("usuario",sesion);
    datos.append("action","perfil");

    let respuesta=await fetch("php/loginUsuario.php",{method:'POST',body:datos});
    let json=await respuesta.json();

    if(json.success==true){

        document.getElementById("email").innerHTML=json.usuario;
        document.getElementById("nombre").value=json.nombre;
        document.getElementById("foto-preview").innerHTML=`<img src="php/${json.foto}" class="foto-perfil">`;
    }else{
    Swal.fire({title:"ERROR",text:json.mensaje,icon:"error"});
    }
}


const guardarPerfil=async()=>{

    let formPerfil=document.getElementById("formPerfil");
    datos=new FormData(formPerfil);
    datos.append("usuario",sesion);
    datos.append("action","saveperfil");

    let respuesta=await fetch("php/loginUsuario.php",{method:'POST',body:datos});
    let json=await respuesta.json();

    if(json.success==true){
        Swal.fire({title:"¡ÉXITO!",text:json.mensaje,icon:"success"});

    }else{
    Swal.fire({title:"ERROR",text:json.mensaje,icon:"error"});
    }
}



function cargarCatalogo() {
    fetch('php/metodosC.php')
        .then(response => response.json())
        .then(data => {
            const catalogo = document.getElementById('catalogo');
            catalogo.innerHTML = '';

            data.forEach(prenda => {
                const prendaHTML = `
                <div class="prenda">
                <img src="${prenda.foto}" alt="${prenda.nombre}" height="60px">
                <h2>${prenda.nombre}</h2>
                <p>${prenda.descripcion}</p>
                <p>Precio: $${prenda.precio}</p>
                <p>Talla: ${prenda.talla}</p>
                
                <div class="input-group mb-3">
                <button class="btn btn-outline-secondary" type="button" onclick="restarCantidad(${prenda.id})">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash" viewBox="0 0 16 16">
                        <path d="M0 8a.5.5 0 0 1 .5-.5h15a.5.5 0 0 1 0 1H.5a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                </button>
                <input type="number" id="cantidad-${prenda.id}" class="form-control text-center" value="1" min="1">
                <button class="btn btn-outline-secondary" type="button" onclick="sumarCantidad(${prenda.id})">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                        <path d="M8 1a.5.5 0 0 1 .5.5v6.5H15a.5.5 0 0 1 0 1H8.5v6.5a.5.5 0 0 1-1 0V8H1a.5.5 0 0 1 0-1h6.5V1.5A.5.5 0 0 1 8 1z"/>
                    </svg>
                </button>
            </div>
        

                <div class="botones">
                    <button class="boton" onclick="agregarCarrito(${prenda.id})"> 
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-circle mx-2" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                        </svg>
                        Agregar al Carrito
                    </button>
                </div>
            </div>
            
                `;
                catalogo.innerHTML += prendaHTML;
            });
        })
        .catch(error => console.error('Error al cargar el catálogo:', error));
}


//CARRITO 

function sumarCantidad(idProducto) {
    const inputCantidad = document.getElementById(`cantidad-${idProducto}`);
    let cantidad = parseInt(inputCantidad.value, 10);
    cantidad++;
    inputCantidad.value = cantidad;
}

// Función para decrementar la cantidad en el spinner
function restarCantidad(idProducto) {
    const inputCantidad = document.getElementById(`cantidad-${idProducto}`);
    let cantidad = parseInt(inputCantidad.value, 10);
    if (cantidad > 1) {
        cantidad--;
        inputCantidad.value = cantidad;
    }
}

// Variable global para almacenar productos en el carrito
let productosEnCarrito = [];

// Función para mostrar los productos del carrito
function mostrarCarrito() {
    const carritoDiv = document.getElementById('carrito');
    carritoDiv.innerHTML = '';

    // Recorrer productosEnCarrito y generar HTML para cada producto
    productosEnCarrito.forEach(producto => {
        const productoHTML = `
            <div class="producto-carrito">
                <img src="${producto.foto}" alt="${producto.nombre}" height="50px">
                <p>${producto.nombre}</p>
                <p>Cantidad: ${producto.cantidad}</p>
                <p>Precio unitario: $${producto.precio}</p>
                <button class="btn btn-danger" onclick="eliminarDelCarrito(${producto.id_carrito})">Eliminar</button>
            </div>
        `;
        carritoDiv.innerHTML += productoHTML;
    });
}

// Función para agregar un producto al carrito
async function agregarCarrito(idProducto) {
    const cantidad = document.getElementById(`cantidad-${idProducto}`).value;
    const usuario = localStorage.getItem('usuario'); // Obtener el usuario desde localStorage

    const formData = new FormData();
    formData.append('action', 'agregarC');
    formData.append('id_p', idProducto);
    formData.append('usuario', usuario);
    formData.append('cantidad', cantidad);

    try {
        const respuesta = await fetch('php/carrito.php', {
            method: 'POST',
            body: formData
        });

        const json = await respuesta.json();

        if (json.success) {
            Swal.fire({
                title: '¡Agregado al carrito!',
                text: json.mensaje,
                icon: 'success'
            }).then(() => {
                // Actualizar la interfaz del carrito mostrando los productos actualizados
                obtenerCarrito();
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: json.mensaje,
                icon: 'error'
            });
        }
    } catch (error) {
        console.error('Error al agregar al carrito:', error);
        Swal.fire({
            title: 'Error',
            text: 'Hubo un problema al intentar agregar al carrito',
            icon: 'error'
        });
    }
}

// Función para eliminar un producto del carrito
async function eliminarDelCarrito(idCarrito) {
    const formData = new FormData();
    formData.append('action', 'eliminarC');
    formData.append('id_carrito', idCarrito);

    try {
        const respuesta = await fetch('php/carrito.php', {
            method: 'POST',
            body: formData
        });

        const json = await respuesta.json();

        if (json.success) {
            Swal.fire({
                title: 'Eliminado del carrito',
                text: json.mensaje,
                icon: 'success'
            }).then(() => {
                // Actualizar la interfaz del carrito mostrando los productos actualizados
                obtenerCarrito();
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: json.mensaje,
                icon: 'error'
            });
        }
    } catch (error) {
        console.error('Error al eliminar del carrito:', error);
        Swal.fire({
            title: 'Error',
            text: 'Hubo un problema al intentar eliminar del carrito',
            icon: 'error'
        });
    }
}

// Función para obtener y mostrar los productos en el carrito
async function obtenerCarrito() {
    const usuario = localStorage.getItem('usuario'); // Obtener el usuario desde localStorage

    const formData = new FormData();
    formData.append('action', 'listarC');
    formData.append('usuario', usuario);

    try {
        const respuesta = await fetch('php/carrito.php', {
            method: 'POST',
            body: formData
        });

        const json = await respuesta.json();

        if (json.success) {
            // Actualizar productosEnCarrito con los datos recibidos del servidor
            productosEnCarrito = json.carrito;
            // Mostrar los productos actualizados en el carrito
            mostrarCarrito();
        } else {
            Swal.fire({
                title: 'Error',
                text: json.mensaje,
                icon: 'error'
            });
        }
    } catch (error) {
        console.error('Error al obtener el carrito:', error);
        Swal.fire({
            title: 'Error',
            text: 'Hubo un problema al intentar obtener el carrito',
            icon: 'error'
        });
    }
}

// Función para confirmar la compra
function confirmarCompra() {
    Swal.fire({
        title: 'Confirmar Compra',
        text: '¿Está seguro de confirmar la compra?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Aquí podrías enviar los datos de la compra al servidor y realizar acciones adicionales si es necesario
            Swal.fire({
                title: 'Compra Confirmada',
                text: '¡Su compra ha sido confirmada con éxito!',
                icon: 'success'
            }).then(() => {
                // Puedes redirigir a otra página o realizar acciones adicionales después de confirmar la compra
                // Por ejemplo, podrías limpiar el carrito y actualizar la interfaz
                limpiarCarrito();
            });
        }
    });
}

// Función para limpiar el carrito después de confirmar la compra
function limpiarCarrito() {
    // Limpiar la variable global y la interfaz del carrito
    productosEnCarrito = [];
    const carritoDiv = document.getElementById('carrito');
    carritoDiv.innerHTML = '';
}
