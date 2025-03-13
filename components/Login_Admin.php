<?php
session_start();
include '../connection/connection.php'; // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $pass = mysqli_real_escape_string($conexion, $_POST['pass']);

    // Consulta para verificar si el usuario existe
    $consulta = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $resultado = mysqli_query($conexion, $consulta);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);

        // Verificación de contraseña (si está hasheada, usa password_verify)
        if ($pass === $usuario['pass']) { // O usa password_verify($password, $usuario['pass']) si las contraseñas están encriptadas
            $_SESSION['usuarios'] = $usuario['id_usuario']; // Guardar el ID en sesión
            $_SESSION['rol'] = $usuario['id_rol']; // Guardar el rol del usuario en sesión

            // Redirigir al dashboard según el rol
            if ($usuario['id_rol'] == 1) {
                header("Location: Admin/Dashboard.php");
            } else {
                header("Location: Index.php");
            }
            exit();
        } else {
            echo '<script>alert("Contraseña incorrecta"); window.location="Login_Admin.php";</script>';
        }
    } else {
        echo '<script>alert("Correo no registrado"); window.location="Login_Admin.php";</script>';
    }
}
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/style_logins.css">
    <title>Login Admin</title>

</head>

<body>
    
    <!--?php include 'components/Navbar.php'; ?--> 

    <div class="container">
        <div class="login-form">
            <h2>Iniciar Sesión</h2>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="input-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" required placeholder="Ingresa tu correo">
                </div>
                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="pass" name="pass" required placeholder="Ingresa tu contraseña">
                </div>
                <button type="submit" value="Aceptar" name="login_Admin" class="btn-login">Iniciar sesión</button>
            </form>
        </div>
    </div>
</body>


</html>