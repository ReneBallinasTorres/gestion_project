<?php
session_start();
include '../connection/connection.php'; // Conexión a la BD

// Verifica si se ha recibido el ID del equipo a eliminar
if (isset($_GET['id_equipo'])) {
    $id_equipo = $_GET['id_equipo'];

    // Eliminar miembros del equipo en la tabla detalle_equipos
    $query_detalle = "DELETE FROM detalle_equipos WHERE id_equipo = '$id_equipo'";
    mysqli_query($conexion, $query_detalle);

    // Eliminar el equipo en la tabla equipos
    $query_equipo = "DELETE FROM equipos WHERE id_equipo = '$id_equipo'";
    $resultado = mysqli_query($conexion, $query_equipo);

    if ($resultado) {
        $_SESSION['eliminado'] = true;
    } else {
        $_SESSION['eliminado'] = false;
    }
}

// Redirigir a la página de administración de equipos
header("Location: ../Admin/Dashboard_equipo.php");
exit();

// Cerrar la conexión
mysqli_close($conexion);
?>
