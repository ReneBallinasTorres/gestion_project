<?php
include '../connection/connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_POST['id_usuario'];
    $n_usuario = $_POST['n_usuario'];
    $a_p = $_POST['a_p'];
    $a_m = $_POST['a_m'];
    $edad = $_POST['edad'];
    $correo = $_POST['correo'];
    $pass = $_POST['pass'];
    $rol = $_POST['rol'];

    $query = "UPDATE usuarios SET 
                n_usuario='$n_usuario', 
                a_p='$a_p', 
                a_m='$a_m', 
                edad='$edad', 
                correo='$correo', 
                pass='$pass', 
                id_rol='$rol' 
            WHERE id_usuario='$id_usuario'";

    if (mysqli_query($conexion, $query)) {
        $_SESSION['mensaje'] = "Usuario actualizado correctamente";
        $_SESSION['tipo_mensaje'] = "success";
        header("Location: ../admin/Dashboard_usuario.php");
    } else {
        $_SESSION['mensaje'] = "Error al actualizar el usuario";
        $_SESSION['tipo_mensaje'] = "danger";
        header("Location: ../admin/Dashboard_usuario.php");
    }

    mysqli_close($conexion);
    header("Location: ../admin/Dashboard_usuario.php");
    exit();
}
?>
