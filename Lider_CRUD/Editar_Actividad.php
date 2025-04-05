<?php
session_start();
include '../connection/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_actividad_proyecto'])) {
    $id_actividad = $_POST['id_actividad_proyecto'];
    $n_actividad = $_POST['n_actividad'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $horas_estimadas = $_POST['horas_estimadas'];
    $id_proyecto = $_POST['proyecto'];
    $id_usuario = $_POST['responsable'];

    // Actualizar la actividad
    $query = "UPDATE actividades_proyecto SET 
                n_actividad = '$n_actividad', 
                descripcion = '$descripcion', 
                fecha_inicio = '$fecha_inicio', 
                fecha_fin = '$fecha_fin', 
                horas_estimadas = '$horas_estimadas', 
                id_proyecto = '$id_proyecto', 
                id_usuario = '$id_usuario' 
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

    header("Location: ../Lider/Dashboard_actividades.php");
    exit();
}
?>
