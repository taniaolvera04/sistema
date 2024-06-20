<?php
require_once "config.php"; 

$sql = "SELECT * FROM prendas";
$resultado = $cx->query($sql);

$prendas = array();

if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $fotoURL = 'img_prendas/' . $row['fotop']; 

        $prendas[] = array(
            'id' => $row['id_p'],
            'nombre' => $row['nombrep'],
            'descripcion' => $row['descripcion'],
            'precio' => $row['precio'],
            'talla' => $row['talla'],
            'foto' => $fotoURL 
        );
    }
}



echo json_encode($prendas);
?>
