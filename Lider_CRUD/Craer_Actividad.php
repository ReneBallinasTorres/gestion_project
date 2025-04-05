<?php
session_start();
include '../connection/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $n_actividad = $_POST['n_actividad'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $horas_estimadas = $_POST['horas_estimadas'];
    $id_proyecto = $_POST['proyecto'];
    $id_usuario = $_POST['responsable']; // Corregido

    // Validación de campos vacíos
    if (empty($n_actividad) || empty($descripcion) || empty($fecha_inicio) || empty($fecha_fin) || empty($id_proyecto) || empty($id_usuario)) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios.";
        $_SESSION['tipo_mensaje'] = "danger";
        header("Location: ../Lider/Dashboard_actividades.php");
        exit();
    }

    // Verificar si el responsable existe en la base de datos
    $checkUserQuery = "SELECT id_usuario FROM usuarios WHERE id_usuario = '$id_usuario'";
    $result = mysqli_query($conexion, $checkUserQuery);

    if (mysqli_num_rows($result) == 0) {
        $_SESSION['mensaje'] = "El usuario responsable no existe.";
        $_SESSION['tipo_mensaje'] = "danger";
        header("Location: ../Lider/Dashboard_actividades.php");
        exit();
    }

    // Insertar la actividad en la base de datos
    $query = "INSERT INTO actividades_proyecto (n_actividad, descripcion, fecha_inicio, fecha_fin, horas_estimadas, id_proyecto, id_usuario) 
            VALUES ('$n_actividad', '$descripcion', '$fecha_inicio', '$fecha_fin', '$horas_estimadas', '$id_proyecto', '$id_usuario')";

    if (mysqli_query($conexion, $query)) {
        $_SESSION['mensaje'] = "Actividad creada exitosamente.";
        $_SESSION['tipo_mensaje'] = "success";
        $_SESSION['tipo_accion'] = "create";
    } else {
        $_SESSION['mensaje'] = "Error al crear la actividad: " . mysqli_error($conexion);
        $_SESSION['tipo_mensaje'] = "danger";
        $_SESSION['tipo_accion'] = "create";
    }

    header("Location: ../Lider/Dashboard_actividades.php");
    exit();
}
?>
