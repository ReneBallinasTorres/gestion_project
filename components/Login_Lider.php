<!--?php
session_start();

if (isset($_SESSION['estudiante'])) {
    header("location: ../students/MenuEst.php");
}
// Conexión a la base de datos
//incluyendo conexion a  mi BD
include '../connection/connection.php';

// Iniciar sesión
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $matricula = $_POST['matricula'];
    $contraseña = $_POST['contraseña'];

    // Consulta para verificar credenciales
    $validar_Est = mysqli_query($conexion, "SELECT * FROM estudiante WHERE matricula='$matricula' and contraseña='$contraseña'");

    // Si hay una fila en el resultado, las credenciales son correctas
    if (mysqli_num_rows($validar_Est) > 0) {
        $_SESSION['estudiante'] = $matricula;
        header("location: ../students/MenuEst.php");
        exit;
    } else {
        $error = "<b><i>Correo o Contraseña Incorrectos</i></b>";
    }
}
?-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Lider</title>
    <link rel="stylesheet" href="../Styles/style_logins.css">
</head>

<body>
    <div class="container">
        <div class="login-form">
            <h2>Iniciar Sesión Lider</h2>
            <form action="#" method="POST">
                <div class="input-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" required placeholder="Ingresa tu correo">
                </div>
                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required placeholder="Ingresa tu contraseña">
                </div>
                <button type="submit" class="btn-login">Iniciar sesión</button>
            </form>
        </div>
    </div>
</body>

</html>