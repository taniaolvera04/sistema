<?php
require_once "config.php";

// Realizar la consulta a la base de datos para obtener las prendas
$sql = "SELECT nombrep, precio, fotop FROM prendas";
$result = $cx->query($sql);

// Verificar si hay resultados
if ($result->num_rows > 0) {
    echo '<div class="grid-container">';
    while ($row = $result->fetch_assoc()) {
        $nombrep = $row['nombrep'];
        $precio = $row['precio'];
        $fotop = $row['fotop'];

        // Construir la ruta completa de la imagen
        $rutaImagen = '../assets/img_prendas/' . $fotop;

        // Mostrar cada prenda en un div de la grilla
        echo '<div class="item">';
        echo '<img src="' . $rutaImagen . '" alt="' . $nombrep . '" class="prenda-img">';
        echo '<div class="prenda-info">';
        echo '<p class="prenda-nombre">' . $nombrep . '</p>';
        echo '<p class="prenda-precio">$' . $precio . '</p>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo '<p>No se encontraron prendas.</p>';
}

// Cerrar la conexión a la base de datos al finalizar
$cx->close();
?>
