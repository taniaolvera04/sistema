<?php
// Incluir el archivo de configuración de la base de datos
include("../../php/config.php");

// Realizar la consulta SQL para obtener los usuarios
$query = "SELECT * FROM usuarios";
$result = $cx->query($query);

if ($result) {
    $lista = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $lista = [];
}

ob_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Usuarios</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="text-center">
    <div class="container">
        <h1 class="mt-5">Reporte de Usuarios</h1>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID_U</th>
                    <th>USUARIO</th>
                    <th>PASSWORD</th>
                    <th>NOMBRE</th>
                    <th>FOTO</th>
                    <th>TIPO</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista as $item): ?>
                    <tr>
                        <td><?php echo $item['id_u']; ?></td>
                        <td><?php echo $item['usuario']; ?></td>
                        <td><?php echo $item['password']; ?></td>
                        <td><?php echo $item['nombre']; ?></td>
                        <td><?php echo $item['foto']; ?></td>
                        <td><?php echo $item['tipo']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$html = ob_get_clean();

// Incluir Dompdf
require_once '../libreria/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// Inicializar Dompdf
$dompdf = new Dompdf();

// Cargar el HTML
$dompdf->loadHtml($html);

// Opcional: Configurar tamaño y orientación del papel
$dompdf->setPaper('A4', 'portrait'); // 'portrait' o 'landscape'

// Renderizar el PDF
$dompdf->render();

// Descargar o mostrar el PDF generado
$dompdf->stream("reporte_usuarios.pdf", array("Attachment" => false));
?>
