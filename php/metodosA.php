<?php
require_once "config.php";
header('Content-Type: text/html; charset=utf-8');

$valido['success']=array('success'=>false,'mensaje'=>"");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case "guardar":
            $a = $_POST['nombrep'];
            $b = $_POST['descripcion'];
            $c = $_POST['precio'];
            $d = $_POST['talla'];
            $e = $_POST['cantidadp'];
            
            $fileName = $_FILES['fotop']['name'];
            $fileTmpName = $_FILES['fotop']['tmp_name'];
            $uploadDirectory = '../assets/img_prendas/'; 
            
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0755, true);
            }
            
            $filePath = $uploadDirectory . basename($fileName);
            
            if (move_uploaded_file($fileTmpName, $filePath)) {
                
        $sql = "INSERT INTO prendas (nombrep, descripcion, precio, talla, cantidadp, fotop) VALUES ('$a','$b','$c','$d','$e', '$filePath')";
                
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
                        $registros['data'][]=array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8]);
                    }
                }
                
                echo json_encode($registros);
            
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
            
            
            case "select":
            
                $valido['success']=array('success'=>false,
            'mensaje'=>"",
            'idp'=>"",
            'nombrep'=>"",
            'descripcion'=>"",
            'precio'=>"",
            'talla'=>"",
            'cantidadp'=>"",);
            
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
            
            echo json_encode($valido);
            
            break;

            
case "update":

    $idp=$_POST['idp'];
    $a=$_POST['nombrep'];
    $b=$_POST['descripcion'];
    $c=$_POST['precio'];
    $d=$_POST['talla'];
    $e=$_POST['cantidadp'];
    $f=$_POST['fotop'];

    $sql="UPDATE prendas SET nombrep='$a',
    descripcion='$b',
    precio='$c',
    talla='$d',
    cantidadp='$e',
    fotop='$f'
    WHERE id_p=$idp";

    if($cx->query($sql)){
       $valido['success']=true;
       $valido['mensaje']="SE ACTUALIZÓ CORRECTAMENTE EL PRODUCTO";
    }else{
        $valido['success']=false;
       $valido['mensaje']="ERROR AL ACTUALIZAR EN BD"; 
    }
    echo json_encode($valido);
   break;

    }

} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>
