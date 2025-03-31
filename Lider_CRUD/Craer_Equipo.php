<?php
session_start();
include '../connection/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha_creacion = $_POST['fecha_creacion'];
    $descripcion = $_POST['descripcion'];
    $estado_equipo = $_POST['estado_equipo'];
    $proyecto = $_POST['proyecto'];
    $miembros = $_POST['miembros']; // Lista de miembros seleccionados

    // Verificar que la variable $miembros sea un array válido y no supere el límite
    if (!isset($miembros) || !is_array($miembros) || count($miembros) > 3) {
        $_SESSION['mensaje'] = "Debes seleccionar entre 1 y 3 miembros.";
        $_SESSION['tipo_mensaje'] = "danger";
        header("Location: ../Lider/Dashboard_equipo.php");
        exit();
    }

    // Obtener el ID del usuario líder (de la sesión)
    $id_usuario = $_SESSION['id_usuario']; // Asegúrate de que este valor está definido

    // Verificar si el usuario líder existe en la base de datos
    $checkUserQuery = "SELECT id_usuario FROM usuarios WHERE id_usuario = '$id_usuario'";
    $result = mysqli_query($conexion, $checkUserQuery);
    
    if (mysqli_num_rows($result) == 0) {
        $_SESSION['mensaje'] = "El usuario líder no existe en la base de datos.";
        $_SESSION['tipo_mensaje'] = "danger";
        header("Location: ../Lider/Dashboard_equipo.php");
        exit();
    }

    // Insertar el equipo en la base de datos
    $query = "INSERT INTO equipos (fecha_creacion, descripcion, estado_equipo, id_proyecto, id_usuario) 
            VALUES ('$fecha_creacion', '$descripcion', '$estado_equipo', '$proyecto', '$id_usuario')";

    if (mysqli_query($conexion, $query)) {
        $id_equipo = mysqli_insert_id($conexion); // Obtener el ID del equipo recién creado

        // Insertar miembros en la tabla detalle_equipos
        foreach ($miembros as $miembro) {
            $checkUser = "SELECT id_usuario FROM usuarios WHERE id_usuario = '$miembro'";
            $result = mysqli_query($conexion, $checkUser);

            if (mysqli_num_rows($result) > 0) {
                $queryDetalle = "INSERT INTO detalle_equipos (id_equipo, id_usuario) VALUES ('$id_equipo', '$miembro')";
                mysqli_query($conexion, $queryDetalle);
            }
        }

        $_SESSION['mensaje'] = "Equipo creado exitosamente.";
        $_SESSION['tipo_mensaje'] = "success"; // mensaje de color verde
        $_SESSION['tipo_accion'] = "create";
    } else {
        $_SESSION['mensaje'] = "Error al crear el Equipo.";
        $_SESSION['tipo_mensaje'] = "danger"; // mensaje de color rojo
        $_SESSION['tipo_accion'] = "create";
    }

    header("Location: ../Lider/Dashboard_equipo.php");
    exit();
}

?>
