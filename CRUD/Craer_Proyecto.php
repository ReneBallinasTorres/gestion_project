<?php
session_start();
include '../connection/connection.php'; // Conexión a la BD

// Verificar si se envió el formulario mediante POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $n_proyecto = trim($_POST['n_proyecto']);
    $objetivos = trim($_POST['objetivos']);
    $descripcion = trim($_POST['descripcion']);
    $fecha_inicio = date('Y-m-d', strtotime($_POST['fecha_inicio']));
    $fecha_fin = date('Y-m-d', strtotime($_POST['fecha_fin']));
    $observaciones = trim($_POST['observaciones']);
    $lider = intval($_POST['lider']);

    // Validación básica de datos
    if (empty($n_proyecto) || empty($objetivos) || empty($descripcion) || empty($fecha_inicio) || empty($fecha_fin) || empty($observaciones)) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios y deben ser válidos.";
        $_SESSION['tipo_mensaje'] = "danger"; // mensaje de color rojo
        header("Location: ../admin/Dashboard_proyecto.php");
        // echo '<script>
        //         alert("Todos los campos son obligatorios y deben ser válidos.");
        //         window.location="../admin/Dashboard_proyecto.php";
        //     </script>';
        exit();
    }

    // Insertar el nuevo usuario en la base de datos
    $sql = "INSERT INTO proyectos (n_proyecto, objetivos, descripcion, fecha_inicio, fecha_fin, observaciones, id_usuario) 
            VALUES ('$n_proyecto', '$objetivos', '$descripcion', '$fecha_inicio', '$fecha_fin', '$observaciones', '$lider')";

    if (mysqli_query($conexion, $sql)) {
        $_SESSION['mensaje'] = "Proyecto creado exitosamente.";
        $_SESSION['tipo_mensaje'] = "success"; // mensaje de color verde
        $_SESSION['tipo_accion'] = "create";
        // echo '<script>
        //         alert("Proyecto creado exitosamente.");
        //         window.location="../admin/Dashboard_proyecto.php";
        //     </script>';
    } else {
        $_SESSION['mensaje'] = "Error al crear el Proyecto.";
        $_SESSION['tipo_mensaje'] = "danger"; // mensaje de color rojo
        $_SESSION['tipo_accion'] = "create";
        // echo '<script>
        //         alert("Error al crear el Proyecto.");
        //         window.location="../admin/Dashboard_proyecto.php";
        //     </script>';
    }

    // Cerrar conexión
    mysqli_close($conexion);
    header("Location: ../admin/Dashboard_proyecto.php");
    exit();
} 
?>
