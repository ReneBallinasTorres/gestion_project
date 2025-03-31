<?php
session_start();
include '../connection/connection.php';

if (isset($_GET['id_proyecto'])) {
    $id_proyecto = $_GET['id_proyecto'];

    $query = "DELETE FROM proyectos WHERE id_proyecto = $id_proyecto";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        $_SESSION['eliminado'] = true;
    } else {
        $_SESSION['eliminado'] = false;
    }
}

header("Location: ../Admin/Dashboard_proyecto.php");
exit();
?>
