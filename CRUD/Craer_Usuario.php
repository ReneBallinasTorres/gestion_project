<?php
session_start();
include '../connection/connection.php'; // Conexión a la BD

// Verificar si se envió el formulario mediante POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $n_usuario = trim($_POST['n_usuario']);
    $a_p = trim($_POST['a_p']);
    $a_m = trim($_POST['a_m']);
    $edad = intval($_POST['edad']);
    $correo = trim($_POST['correo']);
    $pass = trim($_POST['pass']);
    $rol = intval($_POST['rol']);

    // Validación básica de datos
    if (empty($n_usuario) || empty($a_p) || empty($a_m) || empty($correo) || empty($pass) || $edad <= 0) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios y deben ser válidos.";
        $_SESSION['tipo_mensaje'] = "danger"; // mensaje de color rojo
        header("Location: ../admin/Dashboard_usuario.php");
        // echo '<script>
        //         alert("Todos los campos son obligatorios y deben ser válidos.");
        //         window.location="../admin/Dashboard_usuario.php";
        //     </script>';
        exit();
    }

    // Verificar si el correo ya está registrado en la base de datos
    $verificarCorreo = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $resultadoCorreo = mysqli_query($conexion, $verificarCorreo);

    if (mysqli_num_rows($resultadoCorreo) > 0) {
        $_SESSION['mensaje'] = "El correo ya está registrado. Usa otro.";
        $_SESSION['tipo_mensaje'] = "warning"; // mensaje de color amarillo
        header("Location: ../admin/Dashboard_usuario.php");
        // echo '<script>
        //         alert("El correo ya está registrado. Usa otro correo.");
        //         window.location="../admin/Dashboard_usuario.php";
        //     </script>';
        exit();
    }

    // Insertar el nuevo usuario en la base de datos
    $sql = "INSERT INTO usuarios (n_usuario, a_p, a_m, edad, correo, pass, id_rol) 
            VALUES ('$n_usuario', '$a_p', '$a_m', '$edad', '$correo', '$pass', '$rol')";

    if (mysqli_query($conexion, $sql)) {
        $_SESSION['mensaje'] = "Usuario creado exitosamente.";
        $_SESSION['tipo_mensaje'] = "success"; // mensaje de color verde
        header("Location: ../admin/Dashboard_usuario.php");
        // echo '<script>
        //         alert("Usuario creado exitosamente.");
        //         window.location="../admin/Dashboard_usuario.php";
        //     </script>';
    } else {
        $_SESSION['mensaje'] = "Error al crear el usuario.";
        $_SESSION['tipo_mensaje'] = "danger"; // mensaje de color rojo
        header("Location: ../admin/Dashboard_usuario.php");
        // echo '<script>
        //         alert("Error al crear el usuario.");
        //         window.location="../admin/Dashboard_usuario.php";
        //     </script>';
    }

    // Cerrar conexión
    mysqli_close($conexion);
} 
?>
