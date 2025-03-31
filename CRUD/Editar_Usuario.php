<?php
include '../connection/connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanitizar los datos del formulario
    $id_usuario = mysqli_real_escape_string($conexion, $_POST['id_usuario']);
    $n_usuario = mysqli_real_escape_string($conexion, $_POST['n_usuario']);
    $a_p = mysqli_real_escape_string($conexion, $_POST['a_p']);
    $a_m = mysqli_real_escape_string($conexion, $_POST['a_m']);
    $edad = intval($_POST['edad']);
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $pass = mysqli_real_escape_string($conexion, $_POST['pass']);
    $rol = intval($_POST['rol']);

    // Validación básica de datos
    if (empty($n_usuario) || empty($a_p) || empty($a_m) || empty($correo) || empty($pass) || $edad <= 0) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios y deben ser válidos.";
        $_SESSION['tipo_mensaje'] = "danger";
        $_SESSION['tipo_accion'] = "edit"; // Identificar que viene del modal de edición
        header("Location: ../admin/Dashboard_usuario.php");
        exit();
    }

    // Verificar si el correo ya está registrado por otro usuario
    $verificarCorreo = "SELECT id_usuario FROM usuarios WHERE correo = '$correo' AND id_usuario != '$id_usuario'";
    $resultadoCorreo = mysqli_query($conexion, $verificarCorreo);

    if (mysqli_num_rows($resultadoCorreo) > 0) {
        $_SESSION['mensaje'] = "El correo ya está registrado por otro usuario.";
        $_SESSION['tipo_mensaje'] = "warning";
        $_SESSION['tipo_accion'] = "edit";
        header("Location: ../admin/Dashboard_usuario.php");
        exit();
    }

    // Consulta SQL para actualizar el usuario
    $query = "UPDATE usuarios SET 
                n_usuario = '$n_usuario', 
                a_p = '$a_p', 
                a_m = '$a_m', 
                edad = '$edad', 
                correo = '$correo', 
                pass = '$pass', 
                id_rol = '$rol' 
            WHERE id_usuario = '$id_usuario'";

    if (mysqli_query($conexion, $query)) {
        $_SESSION['mensaje'] = "Usuario actualizado correctamente";
        $_SESSION['tipo_mensaje'] = "success";
        $_SESSION['tipo_accion'] = "edit"; // Identificar que viene del modal de edición
        
        // Si la contraseña fue cambiada, forzar cierre de sesión del usuario editado
        if (isset($_POST['pass_changed']) && $_POST['pass_changed'] == '1') {
            // Aquí podrías invalidar la sesión del usuario editado si es necesario
        }
    } else {
        $_SESSION['mensaje'] = "Error al actualizar el usuario: " . mysqli_error($conexion);
        $_SESSION['tipo_mensaje'] = "danger";
        $_SESSION['tipo_accion'] = "edit";
    }

    mysqli_close($conexion);
    header("Location: ../admin/Dashboard_usuario.php");
    exit();
} else {
    // Si alguien intenta acceder directamente al script sin enviar datos
    $_SESSION['mensaje'] = "Acceso no autorizado";
    $_SESSION['tipo_mensaje'] = "danger";
    $_SESSION['tipo_accion'] = "edit";
    header("Location: ../admin/Dashboard_usuario.php");
    exit();
}
?>