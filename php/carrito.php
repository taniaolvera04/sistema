<?php
require_once "config.php";
header('Content-Type: application/json; charset=utf-8');

$valido = ['success' => false, 'mensaje' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {

        case "agregarC":
            $idProducto = $_POST['id_p'] ?? '';
            $usuario = $_POST['usuario'] ?? '';
            $cantidad = $_POST['cantidad'] ?? 1;

            // Obtener id_u del usuario
            $sqlUsuario = "SELECT id_u FROM usuarios WHERE usuario = '$usuario'";
            $resultadoUsuario = $cx->query($sqlUsuario);

            if ($resultadoUsuario->num_rows > 0) {
                $row = $resultadoUsuario->fetch_assoc();
                $idUsuario = $row['id_u'];

                // Obtener detalles del producto
                $sqlProducto = "SELECT nombrep, precio FROM prendas WHERE id_p = '$idProducto'";
                $resultadoProducto = $cx->query($sqlProducto);

                if ($resultadoProducto->num_rows > 0) {
                    $rowProducto = $resultadoProducto->fetch_assoc();
                    $nombreProducto = $rowProducto['nombrep'];
                    $precioProducto = $rowProducto['precio'];

                    // Insertar en la tabla carrito
                    $sqlInsert = "INSERT INTO carrito (id_p, nombrep, precio, cantidad, id_u) 
                                  VALUES ('$idProducto', '$nombreProducto', '$precioProducto', '$cantidad', '$idUsuario')";

                    if ($cx->query($sqlInsert)) {
                        $valido['success'] = true;
                        $valido['mensaje'] = "Producto agregado al carrito correctamente";
                    } else {
                        $valido['mensaje'] = "Error al agregar producto al carrito: " . $cx->error;
                    }
                } else {
                    $valido['mensaje'] = "No se encontró el producto con ID $idProducto";
                }
            } else {
                $valido['mensaje'] = "No se encontró el usuario '$usuario'";
            }
            echo json_encode($valido);
            break;

        case "eliminarC":
            $idCarrito = $_POST['id_carrito'] ?? '';

            if (!empty($idCarrito)) {
                $sqlDelete = "DELETE FROM carrito WHERE id_carrito = '$idCarrito'";
                if ($cx->query($sqlDelete)) {
                    $valido['success'] = true;
                    $valido['mensaje'] = "Producto eliminado del carrito correctamente";
                } else {
                    $valido['mensaje'] = "Error al eliminar producto del carrito: " . $cx->error;
                }
            } else {
                $valido['mensaje'] = "ID de carrito no proporcionado";
            }
            echo json_encode($valido);
            break;

        case "listarC":
            $usuario = $_POST['usuario'] ?? '';

            // Obtener id_u del usuario
            $sqlUsuario = "SELECT id_u FROM usuarios WHERE usuario = '$usuario'";
            $resultadoUsuario = $cx->query($sqlUsuario);

            if ($resultadoUsuario->num_rows > 0) {
                $row = $resultadoUsuario->fetch_assoc();
                $idUsuario = $row['id_u'];

                // Consulta para obtener productos en el carrito del usuario
                $sqlCarrito = "SELECT id_carrito, id_p, nombrep, precio, cantidad FROM carrito WHERE id_u = '$idUsuario'";
                $resultadoCarrito = $cx->query($sqlCarrito);
                $carrito = [];

                if ($resultadoCarrito->num_rows > 0) {
                    while ($rowCarrito = $resultadoCarrito->fetch_assoc()) {
                        $carrito[] = [
                            'id_carrito' => $rowCarrito['id_carrito'],
                            'id_p' => $rowCarrito['id_p'],
                            'nombrep' => $rowCarrito['nombrep'],
                            'precio' => $rowCarrito['precio'],
                            'cantidad' => $rowCarrito['cantidad']
                        ];
                    }
                    echo json_encode(['success' => true, 'carrito' => $carrito]);
                } else {
                    echo json_encode(['success' => true, 'carrito' => []]); // No hay productos en el carrito
                }
            } else {
                echo json_encode(['success' => false, 'mensaje' => "No se encontró el usuario '$usuario'"]);
            }
            break;

        default:
            echo json_encode(['success' => false, 'mensaje' => "Acción no válida"]);
            break;
    }
} else {
    echo json_encode(['success' => false, 'mensaje' => "Método no permitido"]);
}
?>
