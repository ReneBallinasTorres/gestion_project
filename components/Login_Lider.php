<?php
include '../connection/connection.php'; // Conexión a la base de datos
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $pass = mysqli_real_escape_string($conexion, $_POST['pass']);

    // Consulta para verificar si el usuario existe
    $consulta = "SELECT * FROM usuarios WHERE correo = '$correo' and pass = '$pass'";
    $resultado = mysqli_query($conexion, $consulta);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);

        // Verificación de contraseña
        if ($pass === $usuario['pass']) { // O usa password_verify($password, $usuario['pass']) si las contraseñas están encriptadas
            $_SESSION['id_usuario'] = $usuario['id_usuario']; // Guardar el ID en sesión
            $_SESSION['rol'] = $usuario['id_rol']; // Guardar el rol del usuario en sesión

            // Redirigir al dashboard según el rol
            if ($usuario['id_rol'] == 2) {
                header("Location: ../Lider/Dashboard.php");
            } else {
                header("Location: Login_Lider.php");
            }
            exit();
        } else {
            echo '<script>alert("Contraseña incorrecta"); window.location="Login_Lider.php";</script>';
        }
    } else {
        echo '<script>alert("Correo no registrado"); window.location="Login_Lider.php";</script>';
    }
}
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Lider</title>
</head>

<body>

    <?php include 'Navbar-Login.php'; ?>

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
            <h2 class="text-center mb-4">Iniciar Sesión Lider</h2>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" id="correo" name="correo" required placeholder="Ingresa tu correo">
                </div>
                <div class="mb-3">
                    <label for="pass" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="pass" name="pass" required placeholder="Ingresa tu contraseña">
                </div>
                <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
            </form>
        </div>
    </div>
</body>

</html>