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