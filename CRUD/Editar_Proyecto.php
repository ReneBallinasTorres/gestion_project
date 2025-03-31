<?php
include '../connection/connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $id_proyecto = $_POST['id_proyecto'];
    $n_proyecto = $_POST['n_proyecto'];
    $objetivos = $_POST['objetivos'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $observaciones = $_POST['observaciones'];
    $lider = $_POST['lider'];

    // Consulta SQL para actualizar el usuario
    $query = "UPDATE proyectos SET 
                n_proyecto='$n_proyecto', 
                objetivos='$objetivos', 
                descripcion='$descripcion', 
                fecha_inicio='$fecha_inicio', 
                fecha_fin='$fecha_fin', 
                observaciones='$observaciones', 
                id_usuario='$lider' 
            WHERE id_proyecto='$id_proyecto'";

    if (mysqli_query($conexion, $query)) {
        $_SESSION['mensaje'] = "Proyecto actualizado correctamente";
        $_SESSION['tipo_mensaje'] = "success";
        $_SESSION['tipo_accion'] = "edit";
    } else {
        $_SESSION['mensaje'] = "Error al actualizar el Proyecto";
        $_SESSION['tipo_mensaje'] = "danger";
        $_SESSION['tipo_accion'] = "edit";
    }

    mysqli_close($conexion);
    header("Location: ../admin/Dashboard_proyecto.php");
    exit();
}
?>
