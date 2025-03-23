<?php
include '../connection/connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_proyecto = $_POST['id_proyecto'];
    $n_proyecto = $_POST['n_proyecto'];
    $objetivos = $_POST['objetivos'];
    $descripcion = $_POST['descripcion'];
    $observaciones = $_POST['observaciones'];

    // Obtener los valores actuales de la BD para los campos deshabilitados
    $query_select = "SELECT fecha_inicio, fecha_fin, id_usuario FROM proyectos WHERE id_proyecto='$id_proyecto'";
    $result = mysqli_query($conexion, $query_select);
    $row = mysqli_fetch_assoc($result);

    // Mantener los valores originales si no se enviaron
    $fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : $row['fecha_inicio'];
    $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : $row['fecha_fin'];
    $lider = isset($_POST['lider']) ? $_POST['lider'] : $row['id_usuario'];

    // Query de actualización
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
    } else {
        $_SESSION['mensaje'] = "Error al actualizar el Proyecto";
        $_SESSION['tipo_mensaje'] = "danger";
    }

    mysqli_close($conexion);
    header("Location: ../Lider/Dashboard_proyecto.php");
    exit();
}
?>
