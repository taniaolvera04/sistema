<?php
// Incluir archivo de configuración de la base de datos
include("../../php/config.php");

// Realizar la consulta SQL para obtener los usuarios
$query = "SELECT * FROM usuarios";
$result = $cx->query($query);

if ($result) {
    $lista = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $lista = [];
}

// HTML para generar el PDF
$html = '
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
            <tbody>';

// Iterar sobre los usuarios obtenidos
foreach ($lista as $item) {
    $html .= '
                <tr>
                    <td>' . $item['id_u'] . '</td>
                    <td>' . $item['usuario'] . '</td>
                    <td>' . $item['password'] . '</td>
                    <td>' . $item['nombre'] . '</td>
                    <td>' . $item['foto'] . '</td>
                    <td>' . $item['tipo'] . '</td>
                </tr>';
}

$html .= '
            </tbody>
        </table>
    </div>
</body>
</html>';

// Generar el PDF usando Dompdf
require_once '../../assets/libreria/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();

// Cargar el HTML generado
$dompdf->loadHtml($html);

// Opcional: configurar tamaño y orientación del papel
$dompdf->setPaper('A4', 'portrait'); // 'portrait' o 'landscape'

// Renderizar el PDF
$dompdf->render();

// Descargar o mostrar el PDF generado
$dompdf->stream("reporte_usuarios.pdf", array("Attachment" => false));
?>
