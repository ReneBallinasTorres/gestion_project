<?php
session_start();
include '../connection/connection.php'; // Conexión a la BD

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['usuarios'])) {
    echo '<script> 
            alert("Por favor, inicia sesión");
            window.location="../components/Login_Admin.php";
        </script>';
    session_destroy();
    die();
}

// Obtiene el ID del usuario desde la sesión
$id = $_SESSION['usuarios'];

// Consulta para verificar si el usuario tiene rol de administrador (id_rol = 1)
$consulta = "SELECT * FROM usuarios WHERE id_usuario = '$id' AND id_rol = 1";
$resultado = mysqli_query($conexion, $consulta);
$Admin = mysqli_fetch_assoc($resultado);

// Si el usuario no es admin, redirigirlo
if (!$Admin) {
    echo '<script>
            alert("Acceso denegado. No tienes permisos de administrador.");
            window.location="../Index.php";
        </script>';
    session_destroy();
    die();
}

// Cerrar conexión
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos FontAwesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../Styles/style_dashboard.css">
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="Dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="Dashboard_usuario.php"><i class="fas fa-users"></i> Usuarios</a></li>
            <li><a href="Dashboard_proyecto.php"><i class="fas fa-box"></i> Proyectos</a></li>
            <li><a href="Dashboard_equipo.php"><i class="fas fa-cog"></i> Equipos</a></li>
            <li><a href="../Logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <h4 class="me-auto"><b>Dashboard</b></h4>
                <div class="d-flex align-items-center">
                    <span class="me-3"><i class="fas fa-bell"></i> <b>Notificaciones</b></span>
                    <span class="me-3"><i class="fas fa-user"></i> <b><?php echo $Admin['n_usuario'];?> <?php echo $Admin['a_p'];?> <?php echo $Admin['a_m'];?></b></span>
                    <a href="../Logout.php" class="btn btn-danger btn-sm">Cerrar sesión</a>
                </div>
            </div>
        </nav>
    </div>

    <!-- Bootstrap y JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>