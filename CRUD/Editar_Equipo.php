<?php
include '../connection/connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_equipo = $_POST['id_equipo'];
    $descripcion = $_POST['descripcion'];
    $estado_equipo = $_POST['estado_equipo'];
    $fecha_creacion = $_POST['fecha_creacion'];
    $id_proyecto = $_POST['proyecto'];
    $miembros = isset($_POST['miembros']) ? $_POST['miembros'] : [];

    // Validar que no haya campos vacíos
    if (empty($id_equipo) || empty($descripcion) || empty($estado_equipo) || empty($fecha_creacion) || empty($id_proyecto)) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios.";
        $_SESSION['tipo_mensaje'] = "danger";
        header("Location: ../Admin/Dashboard_equipo.php");
        exit();
    }

    // Actualizar la información del equipo
    $sqlUpdate = "UPDATE equipos SET 
                    descripcion='$descripcion', 
                    estado_equipo='$estado_equipo', 
                    fecha_creacion='$fecha_creacion', 
                    id_proyecto='$id_proyecto' 
                WHERE id_equipo='$id_equipo'";
    mysqli_query($conexion, $sqlUpdate);

    // Validar que no haya más de 3 miembros seleccionados
    if (count($miembros) > 3) {
        $_SESSION['mensaje'] = "Puedes seleccionar un máximo de 3 miembros.";
        $_SESSION['tipo_mensaje'] = "danger";
        header("Location: ../Admin/Dashboard_equipo.php");
        exit();
    }

    // Obtener los miembros actuales del equipo
    $sqlCurrentMembers = "SELECT id_usuario FROM detalle_equipos WHERE id_equipo='$id_equipo'";
    $resultCurrentMembers = mysqli_query($conexion, $sqlCurrentMembers);
    $currentMembers = [];
    while ($row = mysqli_fetch_assoc($resultCurrentMembers)) {
        $currentMembers[] = $row['id_usuario'];
    }

    // Si los miembros han sido actualizados (es decir, hay nuevos miembros)
    if (!empty($miembros)) {
        // Eliminar miembros que ya no están seleccionados
        $membersToDelete = array_diff($currentMembers, $miembros);
        if (!empty($membersToDelete)) {
            $sqlDelete = "DELETE FROM detalle_equipos WHERE id_equipo='$id_equipo' AND id_usuario IN (" . implode(',', $membersToDelete) . ")";
            mysqli_query($conexion, $sqlDelete);
        }

        // Insertar nuevos miembros seleccionados
        $newMembers = array_diff($miembros, $currentMembers);
        if (!empty($newMembers)) {
            foreach ($newMembers as $miembro) {
                $sqlInsert = "INSERT INTO detalle_equipos (id_equipo, id_usuario) VALUES ('$id_equipo', '$miembro')";
                mysqli_query($conexion, $sqlInsert);
            }
        }
    }

    $_SESSION['mensaje'] = "Equipo actualizado correctamente.";
    $_SESSION['tipo_mensaje'] = "success";

    // Cerrar conexión y redirigir
    mysqli_close($conexion);
    header("Location: ../Admin/Dashboard_equipo.php");
    exit();
}
