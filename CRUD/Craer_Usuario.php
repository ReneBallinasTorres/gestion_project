<?php
session_start();
include '../connection/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $n_usuario = trim($_POST['n_usuario']);
    $a_p = trim($_POST['a_p']);
    $a_m = trim($_POST['a_m']);
    $edad = intval($_POST['edad']);
    $correo = trim($_POST['correo']);
    $pass = trim($_POST['pass']);
    $rol = intval($_POST['rol']);

    // Validación básica
    if (empty($n_usuario) || empty($a_p) || empty($a_m) || empty($correo) || empty($pass) || $edad <= 0) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios y deben ser válidos.";
        $_SESSION['tipo_mensaje'] = "danger";
        $_SESSION['tipo_accion'] = "create"; // Identificar que viene del modal de creación
        header("Location: ../admin/Dashboard_usuario.php");
        exit();
    }

    // Verificar correo existente
    $verificarCorreo = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $resultadoCorreo = mysqli_query($conexion, $verificarCorreo);

    if (mysqli_num_rows($resultadoCorreo) > 0) {
        $_SESSION['mensaje'] = "El correo ya está registrado. Usa otro.";
        $_SESSION['tipo_mensaje'] = "warning";
        $_SESSION['tipo_accion'] = "create";
        header("Location: ../admin/Dashboard_usuario.php");
        exit();
    }

    // Insertar usuario
    $sql = "INSERT INTO usuarios (n_usuario, a_p, a_m, edad, correo, pass, id_rol) 
            VALUES ('$n_usuario', '$a_p', '$a_m', '$edad', '$correo', '$pass', '$rol')";

    if (mysqli_query($conexion, $sql)) {
        $_SESSION['mensaje'] = "Usuario creado exitosamente.";
        $_SESSION['tipo_mensaje'] = "success";
        $_SESSION['tipo_accion'] = "create";
    } else {
        $_SESSION['mensaje'] = "Error al crear el usuario: " . mysqli_error($conexion);
        $_SESSION['tipo_mensaje'] = "danger";
        $_SESSION['tipo_accion'] = "create";
    }

    mysqli_close($conexion);
    header("Location: ../admin/Dashboard_usuario.php");
    exit();
}
?>