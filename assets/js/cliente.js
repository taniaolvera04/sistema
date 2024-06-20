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

                        <div class="botones">
                            <button class="boton" onclick="agregarCarrito()"> 
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-circle mx-2" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                          </svg>
                          Agregar a Carrito
                          </button>
                        </div>
                    </div>
                `;
                catalogo.innerHTML += prendaHTML;
            });
        })
        .catch(error => console.error('Error al cargar el catálogo:', error));
}


const agregarCarrito=async()=>{

}