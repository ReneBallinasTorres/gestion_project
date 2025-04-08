<?php
session_start();
include '../connection/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_actividad_proyecto'])) {
    $id_actividad = $_POST['id_actividad_proyecto'];
    $horas_utilizadas = $_POST['horas_utilizadas'];

    // Actualizar la actividad
    $query = "UPDATE actividades_proyecto SET horas_utilizadas = '$horas_utilizadas'
            WHERE id_actividad_proyecto = '$id_actividad'";

    if (mysqli_query($conexion, $query)) {
        $_SESSION['mensaje'] = "Actividad actualizada exitosamente.";
        $_SESSION['tipo_mensaje'] = "success";
        $_SESSION['tipo_accion'] = "edit";
    } else {
        $_SESSION['mensaje'] = "Error al actualizar la actividad.";
        $_SESSION['tipo_mensaje'] = "danger";
        $_SESSION['tipo_accion'] = "edit";
    }

    header("Location: ../Operario/Dashboard_actividades.php");
    exit();
}
?>
