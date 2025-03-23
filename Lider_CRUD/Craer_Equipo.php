<?php
session_start();
include '../connection/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha_creacion = $_POST['fecha_creacion'];
    $descripcion = $_POST['descripcion'];
    $estado_equipo = $_POST['estado_equipo'];
    $proyecto = $_POST['proyecto'];
    $miembros = $_POST['miembros']; // Lista de miembros seleccionados

    // Verificar que no se seleccionen más de 3 miembros
    if (count($miembros) > 3) {
        $_SESSION['mensaje'] = "Solo puedes seleccionar un máximo de 3 miembros.";
        $_SESSION['tipo_mensaje'] = "danger";
        header("Location: ../Lider/Dashboard_equipo.php");
        exit();
    }

    // Insertar el equipo en la base de datos
    $query = "INSERT INTO equipos (fecha_creacion, descripcion, estado_equipo, id_proyecto) VALUES ('$fecha_creacion', '$descripcion', '$estado_equipo', '$proyecto')";
    if (mysqli_query($conexion, $query)) {
        $id_equipo = mysqli_insert_id($conexion);

        // Insertar miembros en la tabla detalle_equipos
        foreach ($miembros as $miembro) {
            $queryDetalle = "INSERT INTO detalle_equipos (id_equipo, id_usuario) VALUES ('$id_equipo', '$miembro')";
            mysqli_query($conexion, $queryDetalle);
        }

        $_SESSION['mensaje'] = "Equipo creado exitosamente.";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error al crear el equipo.";
        $_SESSION['tipo_mensaje'] = "danger";
    }
    mysqli_close($conexion);
    header("Location: ../Lider/Dashboard_equipo.php");
    exit();
}
?>
