<?php
session_start();
include '../connection/connection.php';

if (isset($_GET['id_actividad_proyecto'])) {
    $id_actividad = $_GET['id_actividad_proyecto'];

    $query = "DELETE FROM actividades_proyecto WHERE id_actividad_proyecto = $id_actividad";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        $_SESSION['eliminado'] = true;
    } else {
        $_SESSION['eliminado'] = false;
    }
}

header("Location: ../Lider/Dashboard_actividades.php");
exit();
?>
