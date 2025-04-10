<?php
include '../connection/connection.php'; // Conexión a la BD
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    echo '<script> 
            alert("Por favor, inicia sesión");
            window.location="../components/Login_Admin.php";
        </script>';
    session_destroy();
    die();
}

// Obtiene el ID del usuario desde la sesión
$id = $_SESSION['id_usuario'];

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

<?php
// Conexión a la BD (reabrimos para las consultas)
include '../connection/connection.php';

// Contar usuarios
$consulta_usuarios = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM usuarios");
$total_usuarios = mysqli_fetch_assoc($consulta_usuarios)['total'];

// Contar proyectos
$consulta_peroyectos = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM proyectos");
$total_proyectos = mysqli_fetch_assoc($consulta_peroyectos)['total'];

// Contar equipos
$consulta_equipos = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM equipos");
$total_equipos = mysqli_fetch_assoc($consulta_equipos)['total'];
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

<style>
    .card {
        border-radius: 1rem;
    }
</style>

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
                    <span class="me-3"><i class="fas fa-user"></i> <b><?php echo $Admin['n_usuario']; ?> <?php echo $Admin['a_p']; ?> <?php echo $Admin['a_m']; ?></b></span>
                    <a href="../Logout.php" class="btn btn-danger btn-sm">Cerrar sesión</a>
                </div>
            </div>
        </nav>

        <div class="container mt-4">
            <div class="row g-3">
                <div class="row g-3">
                    <!-- Cuadro de Total de Usuarios -->
                    <div class="col-md-12">
                        <div class="card text-dark bg-light mb-3 shadow">
                            <div class="card-body">
                                <h5 class="card-title">Usuarios Registrados (Total: <?php echo $total_usuarios; ?>)</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID Usuario</th>
                                                <th>Nombre</th>
                                                <th>Apellido Paterno</th>
                                                <th>Apellido Materno</th>
                                                <th>Edad</th>
                                                <th>Correo</th>
                                                <th>Contraseña</th>
                                                <th>Rol</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Obtener todos los usuarios con su rol
                                            $resultado_usuarios = mysqli_query($conexion, "SELECT u.*, r.n_rol 
                                            FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol LIMIT 3");
                                            while ($usuario = mysqli_fetch_assoc($resultado_usuarios)) : ?>
                                                <tr>
                                                    <td><?php echo $usuario['id_usuario']; ?></td>
                                                    <td><?php echo $usuario['n_usuario']; ?></td>
                                                    <td><?php echo $usuario['a_p']; ?></td>
                                                    <td><?php echo $usuario['a_m']; ?></td>
                                                    <td><?php echo $usuario['edad']; ?></td>
                                                    <td><?php echo $usuario['correo']; ?></td>
                                                    <td><?php echo $usuario['pass']; ?></td>
                                                    <td><?php echo $usuario['n_rol']; ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="Dashboard_usuario.php" class="btn btn-primary btn-sm">Ver más</a>
                            </div>
                        </div>
                    </div>

                    <!-- Cuadro de Proyectos -->
                    <div class="col-md-6">
                        <div class="card text-dark bg-light mb-3 shadow">
                            <div class="card-body">
                                <h5 class="card-title">Proyectos Creados (Total: <?php echo $total_proyectos; ?>)</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>Descripción</th>
                                                <th>Fecha Inicial</th>
                                                <th>Fecha Final</th>
                                                <th>Lider</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Obtener todos los proyecto
                                            $resultado_proyectos = mysqli_query($conexion, "SELECT p.*, u.n_usuario, u.a_p, u.a_m 
                                            FROM proyectos p JOIN usuarios u ON p.id_usuario = u.id_usuario WHERE u.id_rol LIMIT 2");
                                            while ($proyecto = mysqli_fetch_assoc($resultado_proyectos)) : ?>
                                                <tr>
                                                    <td><?php echo $proyecto['id_proyecto']; ?></td>
                                                    <td><?php echo $proyecto['n_proyecto']; ?></td>
                                                    <td><?php echo $proyecto['descripcion']; ?></td>
                                                    <td><?php echo $proyecto['fecha_inicio']; ?></td>
                                                    <td><?php echo $proyecto['fecha_fin']; ?></td>
                                                    <td><?php echo $proyecto['n_usuario'] . ' ' . $proyecto['a_p'] . ' ' . $proyecto['a_m']; ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="Dashboard_proyecto.php" class="btn btn-success btn-sm">Ver más</a>
                            </div>
                        </div>
                    </div>

                    <!-- Cuadro de Equipos -->
                    <div class="col-md-6">
                        <div class="card text-dark bg-light mb-3 shadow">
                            <div class="card-body">
                                <h5 class="card-title">Equipos Creados por Líderes (Total: <?php echo $total_equipos; ?>)</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Proyecto</th>
                                                <th>Lider</th>
                                                <th>Descripción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $resultado_equipos = mysqli_query($conexion, "SELECT e.*, p.n_proyecto, u.n_usuario
                                            FROM equipos e JOIN usuarios u ON e.id_usuario = u.id_usuario
                                            JOIN proyectos p ON e.id_proyecto = p.id_proyecto WHERE u.id_rol = 2 LIMIT 2");
                                            while ($equipo = mysqli_fetch_assoc($resultado_equipos)) : ?>
                                                <tr>
                                                    <td><?php echo $equipo['id_equipo']; ?></td>
                                                    <td><?php echo $equipo['n_proyecto']; ?></td>
                                                    <td><?php echo $equipo['n_usuario']; ?></td>
                                                    <td><?php echo $equipo['descripcion']; ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="Dashboard_equipo.php" class="btn btn-warning btn-sm">Ver más</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Bootstrap y JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>