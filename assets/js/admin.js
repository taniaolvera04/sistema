var action=document.getElementById("action");

var btnCa=document.getElementById("btnCa");
var btnPre=document.getElementById("btnPre");
var btnUsu=document.getElementById("btnUsu");
var btnPro=document.getElementById("btnPro");
var btnDa=document.getElementById("btnDa");



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



//REGISTRAR PRENDAS Y UNA IMAGEN EN BASE DE DATOS

const guardarPrendas = async () => {
    let nombrep = document.getElementById('nombrep').value;
    let descripcion = document.getElementById('descripcion').value;
    let precio = document.getElementById('precio').value;
    let talla = document.getElementById('talla').value;
    let cantidadp = document.getElementById('cantidadp').value;
    let fotop = document.getElementById('fotop').files[0]; 

    if (nombrep.trim() == "" || descripcion.trim() == "" || precio.trim() == "" || cantidadp.trim() == "" || talla.trim() == "" || !fotop) {
        Swal.fire({
            title: "ERROR",
            text: "Falta completar campos o seleccionar una imagen",
            icon: "error"
        });
        return;
    }

    let datos = new FormData();
    datos.append("nombrep", nombrep);
    datos.append("descripcion", descripcion);
    datos.append("precio", precio);
    datos.append("cantidadp", cantidadp);
    datos.append("talla", talla);
    datos.append("fotop", fotop); 
    datos.append('action', 'guardar');

    try {
        let respuesta = await fetch("php/metodosA.php", { method: 'POST', body: datos });
        let json = await respuesta.json();

        if (json.success == true) {
            Swal.fire({
                title: "¡REGISTRO ÉXITOSO!",
                text: json.mensaje,
                icon: "success"
            });
            cargarPrendas();
        } else {
            Swal.fire({
                title: "ERROR",
                text: json.mensaje,
                icon: "error"
            });
        }
    } catch (error) {
        console.error('Error al guardar la prenda:', error);
        Swal.fire({
            title: "ERROR",
            text: "Hubo un problema al procesar la solicitud",
            icon: "error"
        });
    }
}


//TABLA PARA CARGAR PRENDAS Y BOTÓN QUE ABRE MODAL

// Función para cargar prendas usando DataTables
const cargarPrendas = async () => {
    const datos = new FormData();
    datos.append("action", "selectAll");
    let respuesta = await fetch("php/metodosA.php", { method: 'POST', body: datos });
    let json = await respuesta.json();

    let tablaHTML = `
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addPrenda">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-circle-fill mx-2" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
            </svg>
            Agregar Prenda
        </button><br>

        <table id="tablaPrendas" class="table table-striped w-100 text-center">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE</th>
                    <th>DESCRIPCIÓN</th>
                    <th>PRECIO</th>
                    <th>TALLA</th>
                    <th>CANTIDAD</th>
                    <th>IMAGEN</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
    `;

    json.data.forEach(item => {
        tablaHTML += `
            <tr>
                <td>${item[0]}</td>
                <td>${item[1]}</td>
                <td>${item[2]}</td>
                <td>${item[3]}</td>
                <td>${item[4]}</td>
                <td>${item[5]}</td>
                <td><img src="img_prendas/${item[6]}" height="90px"></td>
                <td>
                    <button class="btn btn-danger" onclick="eliminarPrenda(${item[0]})">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                        </svg>
                    </button>
                    <button class="btn btn-info" onclick="mostrarPrenda(${item[0]})" data-bs-toggle="modal" data-bs-target="#editPrenda">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                        </svg>
                    </button>
                </td>
            </tr>
        `;
    });

    tablaHTML += `</tbody></table>`;

    document.getElementById("action").innerHTML = tablaHTML;

    if ($.fn.DataTable.isDataTable("#tablaPrendas")) {
        $("#tablaPrendas").DataTable().destroy();
    }

    $("#tablaPrendas").DataTable({
        lengthMenu: [5, 10, 25, 50, 100],
        language: {
            lengthMenu: "Mostrar _MENU_ registros por página",
            zeroRecords: "No se encontraron resultados",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "No hay registros disponibles",
            infoFiltered: "(filtrados de _MAX_ registros totales)",
            search: "Buscar:",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            }
        }
    });
};

btnPre.onclick = cargarPrendas;



//ELIMINAR PRENDAS

const eliminarPrenda = async (idp) => {
    Swal.fire({
        title: "¿Estás seguro de eliminar esta prenda?",
        icon:"question",
        showDenyButton: true,
        confirmButtonText: "Si, estoy seguro",
        denyButtonText: "No estoy seguro"

    }).then(async (result) => {
        if (result.isConfirmed) {
            let idp = new FormData();
            idp.append('idp', idp);
            idp.append('action','delete');

            let respuesta = await fetch("php/metodosA.php", {
                method: 'POST',
                body: idp
            });
            let json = await respuesta.json();

            if (json.success == true) {
                Swal.fire({
                    title: "¡Se eliminó con éxito!", text: json.mensaje, icon: "success"});
            } else {
                Swal.fire({
                    title: "ERROR", text: json.mensaje, icon: "error"});
            }
            cargarProductos();
            Swal.fire("Prenda eliminada", "", "success");
        }
    });
}


//MOSTRAR INFORMACIÓN DE PRENDA EN MODAL

const mostrarPrenda=async(idp)=>{
    let datos=new FormData();
    datos.append("idp",idp);
    datos.append('action','select');
    
    let respuesta=await fetch("php/metodosA.php",{method:'POST',body:datos});
    let json=await respuesta.json();

    document.querySelector("#idp").value=json.idp;
    document.querySelector("#enombrep").value=json.nombrep;
    document.querySelector("#edescripcion").value=json.descripcion;
    document.querySelector("#eprecio").value=json.precio;
    document.querySelector("#etalla").value=json.talla;
    document.querySelector("#ecantidadp").value=json.cantidadp;
   
}


//MOSTRAR FOTO-PREVIEW EN MODAL EDITAR

function cargarImagenActual(urlImagen) {
    var imgPreview = document.getElementById('eprenda-preview');
    imgPreview.src = urlImagen;
}


function previewEditedImage() {
    var fileInput = document.getElementById('efotop');
    var imgPreview = document.getElementById('eprenda-preview');

    // Verificar si se seleccionó un archivo
    if (fileInput.files && fileInput.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            imgPreview.src = e.target.result;
        }

        reader.readAsDataURL(fileInput.files[0]);
    }
}


// MOSTRAR FOTO-PREVIEW EN MODAL

function previewImage() {
    const fotoInput = document.getElementById('fotop');
    const preview = document.getElementById('prenda-preview');
    
    if (fotoInput.files && fotoInput.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block'; 
        }

        reader.readAsDataURL(fotoInput.files[0]); // Leer el archivo como URL
    }
}

