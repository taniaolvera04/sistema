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
            $a = $_POST['nombrep'];
            $b = $_POST['descripcion'];
            $c = $_POST['precio'];
            $d = $_POST['talla'];
            $e = $_POST['cantidadp'];
            $h = $_POST['idc'];
            
            $fileName = $_FILES['fotop']['name'];
            $fileTmpName = $_FILES['fotop']['tmp_name'];
            $uploadDirectory = '../assets/img_prendas/'; 
            
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0755, true);
            }
            
            $filePath = $uploadDirectory . basename($fileName);
            
            if (move_uploaded_file($fileTmpName, $filePath)) {
                
        $sql = "INSERT INTO prendas (nombrep, descripcion, precio, talla, cantidadp, fotop, id_c) VALUES ('$a','$b','$c','$d','$e', '$filePath','$h')";
                
                if ($cx->query($sql)) {
                    $valido['success'] = true;
                    $valido['mensaje'] = "PRENDA SE GUARDÓ CORRECTAMENTE";
                } else {
                    $valido['mensaje'] = "ERROR AL GUARDAR PRENDA EN BD";
                }
            } else {
                $valido['mensaje'] = "ERROR AL SUBIR IMAGEN";
            }

            echo json_encode($valido);
            break;
        


            case "selectAll":

                $sql="SELECT * FROM prendas";
                $registros=array('data'=>array());
                $res=$cx->query($sql);
                if($res->num_rows>0){
                    while($row=$res->fetch_array()){
                        $registros['data'][]=array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7]);
                    }
                }
                
                echo json_encode($registros);
            
            break;

            $idp = $_POST['idp'];
    
            // Respuesta por defecto
            $valido = array(
                'success' => false,
                'mensaje' => 'Error al procesar la solicitud'
            );
        
            // Verificar si el ID de la prenda es numérico
            if (!is_numeric($idp)) {
                $valido['mensaje'] = "ID de prenda no válido";
            } else {
                // Preparar la consulta SQL para eliminar la prenda con una consulta preparada
                $sql = "DELETE FROM prendas WHERE id_p = ?";
                $stmt = $cx->prepare($sql);
                $stmt->bind_param("i", $idp); // "i" indica que el parámetro es un entero (ID de la prenda)
                
                // Ejecutar la consulta para eliminar la prenda
                if ($stmt->execute()) {
                    $valido['success'] = true;
                    $valido['mensaje'] = "Se eliminó la prenda correctamente";
                } else {
                    $valido['mensaje'] = "Error al eliminar la prenda: " . $stmt->error;
                }
            }
        
            // Devolver respuesta en formato JSON
            echo json_encode($valido);
            
            
            
            
            case "select":
            
                $valido['success']=array('success'=>false,
            'mensaje'=>"",
            'idp'=>"",
            'nombrep'=>"",
            'descripcion'=>"",
            'precio'=>"",
            'talla'=>"",
            'cantidadp'=>"",
            'idc'=>"",);
            
            $idp=$_POST['idp'];
                $sql="SELECT * FROM prendas WHERE id_p=$idp";
            
                $res=$cx->query($sql);
                $row=$res->fetch_array();
                
                $valido['success']==true;
                $valido['mensaje']="SE ENCONTRÓ PRODUCTO";
            
                $valido['idp']=$row[0];
                $valido['nombrep']=$row[1];
                $valido['descripcion']=$row[2];
                $valido['precio']=$row[3];
                $valido['talla']=$row[4];
                $valido['cantidadp']=$row[5];
                $valido['fotop']=$row[6];
                $valido['idc']=$row[7];
            
            echo json_encode($valido);
            
            break;

            
case "update":

    $idp=$_POST['idp'];
    $a=$_POST['nombrep'];
    $b=$_POST['descripcion'];
    $c=$_POST['precio'];
    $d=$_POST['talla'];
    $e=$_POST['cantidadp'];
    $h=$_POST['idc'];

    $fileName = $_FILES['fotop']['name'];
    $fileTmpName = $_FILES['fotop']['tmp_name'];
    $uploadDirectory = '../assets/img_prendas/'; 
    
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0755, true);
    }
    
    $filePath = $uploadDirectory . basename($fileName);
    
    if (move_uploaded_file($fileTmpName, $filePath)) {

    $sql="UPDATE prendas SET nombrep='$a',
    descripcion='$b',
    precio='$c',
    talla='$d',
    cantidadp='$e',
    fotop='$filePath',
    id_c='$h'
    WHERE id_p=$idp";

    if($cx->query($sql)){
       $valido['success']=true;
       $valido['mensaje']="SE ACTUALIZÓ CORRECTAMENTE EL PRODUCTO";
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

            $idp=$_POST['idp'];
        
            $sql="DELETE FROM prendas WHERE id_p=$idp";
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
                              m.id_p AS id_p,
                               pr.nombrep AS nombrep,
                               m.cant AS cant,
                               pr.precio AS precio,
                               m.id_p AS total_a_pagar,
                               pr.talla AS talla,
                               m.fecha AS fecha
                        FROM movimientos m
                        JOIN prendas pr ON m.id_p = pr.id_p";
                
                $registros = array();
                
                $res = $cx->query($sql);
                if ($res && $res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $total_pagar = $row['cant'] * $row['precio'];

                        $registros['data'][] = array(
                            'id_u' => $row['id_u'],
                            'id_p' => $row['id_p'],
                            'nombrep' => $row['nombrep'],
                            'cant' => $row['cant'],
                            'precio' => $row['precio'],
                            'total_a_pagar' => $total_pagar,
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


            
                default:
                    echo json_encode(["error" => "Acción no válida"]);
                    break;
        }
    } else {
        echo json_encode(["error" => "Método no permitido"]);
    }
?>