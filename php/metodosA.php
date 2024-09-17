<?php
require_once "config.php";
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Mexico_City'); 


$valido['success']=array('success'=>false,'mensaje'=>"");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch ($action) {

        //METODOS PARA AGREGAR PRENDAS
        case "guardar":
            // Obtener los datos del formulario
            $a = $_POST['nombrea'] ?? '';
            $b = $_POST['descripcion'] ?? '';
            $c = $_POST['precio'] ?? 0;
            $d = $_POST['cantidada'] ?? 0;
            $e = $_POST['idc'] ?? '';
            $usuario = $_POST['usuario'] ?? ''; // Nombre de usuario
        
            // Manejo de la imagen
            $fileName = $_FILES['fotoa']['name'];
            $fileTmpName = $_FILES['fotoa']['tmp_name'];
            $uploadDirectory = '../assets/img_album/';
        
            // Verificar y crear directorio si no existe
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0755, true);
            }
        
            $filePath = $uploadDirectory . basename($fileName);
        
            // Mover la imagen subida al directorio deseado
            if (move_uploaded_file($fileTmpName, $filePath)) {
                // Obtener el ID del usuario
                $sqlUsuario = "SELECT id_u FROM usuarios WHERE usuario = ?";
                $stmtUsuario = $cx->prepare($sqlUsuario);
                $stmtUsuario->bind_param("s", $usuario);
                $stmtUsuario->execute();
                $resultadoUsuario = $stmtUsuario->get_result();
        
                if ($resultadoUsuario->num_rows > 0) {
                    $rowUsuario = $resultadoUsuario->fetch_assoc();
                    $id_u = $rowUsuario['id_u'];
        
                    // Insertar los datos de la prenda en la base de datos
                    $sqlInsertAlbum = "INSERT INTO albumes (nombrea, descripcion, precio, cantidada, fotoa, id_c) 
                                        VALUES ('$a', '$b', $c, $d, '$filePath', $e)";
        
                    // Ejecutar la consulta
                    if ($cx->query($sqlInsertAlbum) === TRUE) {
                        $valido['success'] = true;
                        $valido['mensaje'] = "Registro exitoso";
                    } else {
                        $valido['success'] = false;
                        $valido['mensaje'] = "Error en la consulta SQL: " . $cx->error;
                    }
                } else {
                    $valido['mensaje'] = "No se encontró el usuario '$usuario'";
                }
            } else {
                $valido['mensaje'] = "Error al subir la imagen del álbum";
            }
        
            echo json_encode($valido);
            break;
        


            case "selectAll":

                $sql="SELECT * FROM albumes";
                $registros=array('data'=>array());
                $res=$cx->query($sql);
                if($res->num_rows>0){
                    while($row=$res->fetch_array()){
                        $registros['data'][]=array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5]);
                    }
                }
                
                echo json_encode($registros);
            
            break;

            $ida = $_POST['ida'];
    
            // Respuesta por defecto
            $valido = array(
                'success' => false,
                'mensaje' => 'Error al procesar la solicitud'
            );
        
            // Verificar si el ID de la prenda es numérico
            if (!is_numeric($idp)) {
                $valido['mensaje'] = "ID de album no válido";
            } else {
                // Preparar la consulta SQL para eliminar la prenda con una consulta preparada
                $sql = "DELETE FROM albumes WHERE id_a = ?";
                $stmt = $cx->prepare($sql);
                $stmt->bind_param("i", $ida); // "i" indica que el parámetro es un entero (ID de la prenda)
                
                // Ejecutar la consulta para eliminar la prenda
                if ($stmt->execute()) {
                    $valido['success'] = true;
                    $valido['mensaje'] = "Se eliminó el album correctamente";
                } else {
                    $valido['mensaje'] = "Error al eliminar el album: " . $stmt->error;
                }
            }
        
            // Devolver respuesta en formato JSON
            echo json_encode($valido);
            
            
            
            
            case "select":
            
                $valido['success']=array('success'=>false,
            'mensaje'=>"",
            'ida'=>"",
            'nombrea'=>"",
            'descripcion'=>"",
            'precio'=>"",
            'cantidada'=>"",
            'idc'=>"",);
            
            $ida=$_POST['ida'];
                $sql="SELECT * FROM prendas WHERE id_a=$ida";
            
                $res=$cx->query($sql);
                $row=$res->fetch_array();
                
                $valido['success']==true;
                $valido['mensaje']="SE ENCONTRÓ PRODUCTO";
            
                $valido['ida']=$row[0];
                $valido['nombrea']=$row[1];
                $valido['descripcion']=$row[2];
                $valido['precio']=$row[3];
                $valido['cantidada']=$row[4];
                $valido['fotoa']=$row[5];
                $valido['idc']=$row[6];
            
            echo json_encode($valido);
            
            break;

            
case "update":

    $ida=$_POST['ida'];
    $a=$_POST['nombrea'];
    $b=$_POST['descripcion'];
    $c=$_POST['precio'];
    $d=$_POST['cantidada'];
    $e=$_POST['idc'];

    $fileName = $_FILES['fotoa']['name'];
    $fileTmpName = $_FILES['fotoa']['tmp_name'];
    $uploadDirectory = '../assets/img_album/'; 
    
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0755, true);
    }
    
    $filePath = $uploadDirectory . basename($fileName);
    
    if (move_uploaded_file($fileTmpName, $filePath)) {

    $sql="UPDATE albumes SET nombrea='$a',
    descripcion='$b',
    precio='$c',
    cantidada='$d',
    fotoa='$filePath',
    id_c='$e'
    WHERE id_a=$ida";

    if($cx->query($sql)){
       $valido['success']=true;
       $valido['mensaje']="SE ACTUALIZÓ CORRECTAMENTE EL ÁLBUM";
    }else{
        $valido['success']=false;
       $valido['mensaje']="ERROR AL ACTUALIZAR EN BD"; 
    }

    
} else {
    $valido['mensaje'] = "ERROR AL ACTUALIZAR IMAGEN";
}

    echo json_encode($valido);
   break;


   //METODOS PARA AGREGAR CATEGORIAS

   case "guardarCa":
    $a = $_POST['nombrec'];
        
$sql = "INSERT INTO categorias VALUES (null,'$a')";
        
        if ($cx->query($sql)) {
            $valido['success'] = true;
            $valido['mensaje'] = "CATEGORÍA SE GUARDÓ CORRECTAMENTE";
        } else {
            $valido['mensaje'] = "ERROR AL GUARDAR CATEGORÍA EN BD";
        }
    echo json_encode($valido);
    break;


   case "selectCa":
            
    $valido['success']=array('success'=>false,'mensaje'=>"",'idc'=>"",'nombrec'=>"");

    $idc=$_POST['idc'];
    $sql="SELECT * FROM categorias WHERE id_c=$idc";

    $res=$cx->query($sql);
    $row=$res->fetch_array();
    
    $valido['success']==true;
    $valido['mensaje']="SE ENCONTRÓ CATEGORÍA";

    $valido['idc']=$row[0];
    $valido['nombrec']=$row[1];

echo json_encode($valido);

break;


case "selectAllCa":

    $sql="SELECT * FROM categorias";
    $registros=array('data'=>array());
    $res=$cx->query($sql);
    if($res->num_rows>0){
        while($row=$res->fetch_array()){
            $registros['data'][]=array($row[0],$row[1]);
        }
    }
    
    echo json_encode($registros);

break;

    case "updateCa":

        $idc=$_POST['idc'];
        $a=$_POST['nombrec'];
    
        $sql="UPDATE categorias SET categoria='$a' WHERE id_c=$idc";
    
        if($cx->query($sql)){
           $valido['success']=true;
           $valido['mensaje']="SE ACTUALIZÓ CORRECTAMENTE LA CATEGORIA";
        }else{
            $valido['success']=false;
           $valido['mensaje']="ERROR AL ACTUALIZAR EN BD"; 
        }
    
        echo json_encode($valido);
        break;
        
    
        case "delete":

            $ida=$_POST['ida'];
        
            $sql="DELETE FROM albumes WHERE id_a=$ida";
            if($cx->query($sql)){
               $valido['success']=true;
               $valido['mensaje']="SE ELIMINÓ CORRECTAMENTE";
            }else{
                $valido['success']=false;
               $valido['mensaje']="ERROR AL ELIMINAR EN BD"; 
            }
        
            echo json_encode($valido);
        
        break;
        
        
        case "deleteCa":
            if(isset($_POST['idc'])) {
                $idc = $_POST['idc'];
                
                // Realizar la eliminación en la base de datos
                $sql = "DELETE FROM categorias WHERE id_c = $idc";
                if($cx->query($sql)){
                    $valido['success'] = true;
                    $valido['mensaje'] = "SE ELIMINÓ CORRECTAMENTE";
                } else {
                    $valido['success'] = false;
                    $valido['mensaje'] = "ERROR AL ELIMINAR EN BD"; 
                }
            } else {
                $valido['success'] = false;
                $valido['mensaje'] = "ID de categoría no proporcionado"; 
            }
            echo json_encode($valido);
            break;


            //MOSTRAR TODOS LOS USUARIOS 
            case "selectAllUsu":

                $sql="SELECT * FROM usuarios";
                $registros=array('data'=>array());
                $res=$cx->query($sql);
                if($res->num_rows>0){
                    while($row=$res->fetch_array()){
                        $registros['data'][]=array($row[0],$row[1],$row[2],$row[3],$row[5]);
                    }
                }
                
                echo json_encode($registros);
            
            break;

            //MOSTRAR MOVIMIENTOS


            case "selectMov":
               
                $sql = "SELECT m.id_u AS id_u,
                m.tipomov AS tipomov,
                pr.nombrep AS nombrep,
                m.cant AS cantidad,
                pr.talla AS talla,
                m.fecha AS fecha
         FROM movimientos m
         JOIN prendas pr ON m.id_p = pr.id_p";

 $registros = array();

 $res = $cx->query($sql);
 if ($res && $res->num_rows > 0) {
     while ($row = $res->fetch_assoc()) {
         $registros['data'][] = array(
             'id_u' => $row['id_u'],
             'tipomov' => $row['tipomov'],
             'nombrep' => $row['nombrep'],
             'cantidad' => $row['cantidad'],
             'talla' => $row['talla'],
             'fecha' => $row['fecha']
         );
     }
     $valido['success'] = true;
     $valido['mensaje'] = "Consulta exitosa";
     $valido['data'] = $registros['data'];
 } else {
     $valido['success'] = false;
     $valido['mensaje'] = "No se encontraron registros";
 }

 echo json_encode($valido);
 break;



 //MOVIMIENTOS
            
                default:
                    echo json_encode(["error" => "Acción no válida"]);
                    break;
        }
    } else {
        echo json_encode(["error" => "Método no permitido"]);
    }
?>