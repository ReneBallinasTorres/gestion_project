<?php
session_start();
include '../connection/connection.php'; // Conexión a la BD

// Verifica si el usuario ha iniciado sesión y es admin
if (!isset($_SESSION['usuarios'])) {
    echo '<script>
        alert("Por favor, inicia sesión.");
        window.location="../components/Login_Admin.php";
    </script>';
    session_destroy();
    die();
}

$id_admin = $_SESSION['usuarios'];
$consulta_admin = "SELECT * FROM usuarios WHERE id_usuario = '$id_admin' AND id_rol = 1";
$resultado_admin = mysqli_query($conexion, $consulta_admin);
$Admin = mysqli_fetch_assoc($resultado_admin);

// Si el usuario no es admin, redirigirlo
if (!$Admin) {
    echo '<script>
        alert("Acceso denegado. No tienes permisos de administrador.");
        window.location="../Index.php";
    </script>';
    session_destroy();
    die();
}

// Verifica si se recibió el ID del usuario a eliminar
if (isset($_GET['id_proyecto'])) {
    $id_proyecto = $_GET['id_proyecto'];

    // Consulta para eliminar el usuario
    $query = "DELETE FROM proyectos WHERE id_proyecto = '$id_proyecto'";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        $_SESSION['mensaje'] = "Proyecto Eliminado correctamente";
        $_SESSION['tipo_mensaje'] = "success";
        // echo '<script>
        //     alert("Usuario eliminado correctamente");
        //     window.location="../Admin/Dashboard_proyecto.php";
        // </script>';
    } else {
        $_SESSION['mensaje'] = "Error al Eliminar Proyecto";
        $_SESSION['tipo_mensaje'] = "danger";
        // echo '<script>
        //     alert("Error al eliminar usuario");
        //     window.location="../Admin/Dashboard_proyecto.php";
        // </script>';
    }
} else {
        $_SESSION['mensaje'] = "Error al Eliminar el Proyecto";
        $_SESSION['tipo_mensaje'] = "danger";
    // echo '<script>
    //     alert("ID de usuario no válido");
    //     window.location="../Admin/Dashboard_proyecto.php";
    // </script>';
}

// Cierra la conexión
mysqli_close($conexion);
header("Location: ../admin/Dashboard_proyecto.php");
?>
