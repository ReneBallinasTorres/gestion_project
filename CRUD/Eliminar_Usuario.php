<?php
session_start();
include '../connection/connection.php';

if (isset($_GET['id_usuario'])) {
    $id_usuario = $_GET['id_usuario'];

    $query = "DELETE FROM usuarios WHERE id_usuario = $id_usuario";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        $_SESSION['eliminado'] = true;
    } else {
        $_SESSION['eliminado'] = false;
    }
}

header("Location: ../Admin/Dashboard_usuario.php");
exit();
?>
