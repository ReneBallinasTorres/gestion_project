<?php
include '../connection/connection.php'; // Conexión a la BD
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    echo '<script> 
            alert("Por favor, inicia sesión");
            window.location="../components/Login_Lider.php";
        </script>';
    session_destroy();
    die();
}

// Obtiene el ID del usuario desde la sesión
$id = $_SESSION['id_usuario'];

// Consulta para verificar si el usuario tiene rol de Lider (id_rol = 2)
$consulta = "SELECT * FROM usuarios WHERE id_usuario = '$id' AND id_rol = 2";
$resultado = mysqli_query($conexion, $consulta);
$Lider = mysqli_fetch_assoc($resultado);

// Si el usuario no es admin, redirigirlo
if (!$Lider) {
    echo '<script>
            alert("Acceso denegado. No tienes permisos de Lider.");
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

// Contar proyectos
$consulta_peroyectos = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM proyectos WHERE id_usuario = '$id'");
$total_proyectos = mysqli_fetch_assoc($consulta_peroyectos)['total'];

// Contar equipos
$consulta_equipos = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM equipos WHERE id_usuario = '$id'");
$total_equipos = mysqli_fetch_assoc($consulta_equipos)['total'];

// Contar actividades
$consulta_actividades = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM actividades_proyecto ap
    JOIN proyectos p ON ap.id_proyecto = p.id_proyecto WHERE p.id_usuario = '$id'");
$total_actividades = mysqli_fetch_assoc($consulta_actividades)['total'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lider Dashboard</title>
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
        <h2>Lider Panel</h2>
        <ul>
            <li><a href="Dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="Dashboard_proyecto.php"><i class="fas fa-box"></i> Proyectos</a></li>
            <li><a href="Dashboard_equipo.php"><i class="fas fa-users"></i> Equipos</a></li>
            <li><a href="Dashboard_actividades.php"><i class="fas fa-users"></i> Actividades</a></li>
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
                    <span class="me-3"><i class="fas fa-user"></i> <b><?php echo $Lider['n_usuario']; ?> <?php echo $Lider['a_p']; ?> <?php echo $Lider['a_m']; ?></b></span>
                    <a href="../Logout.php" class="btn btn-danger btn-sm">Cerrar sesión</a>
                </div>
            </div>
        </nav>

        <!-- Proyectos Asignados -->
        <div class="container mt-4">
            <div class="row g-3">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="card text-dark bg-light mb-3 shadow">
                            <div class="card-body">
                                <h5 class="card-title"><b>Proyectos Asignados (Total: </b><?php echo $total_proyectos; ?>)</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th scope="col">ID Proyecto</th>
                                                <th scope="col">Nombre Proyecto</th>
                                                <th scope="col">Fecha de Inicio</th>
                                                <th scope="col">Fecha de Fin</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Consulta para obtener los proyectos designado al líder
                                            $consulta_proyectos = "SELECT * FROM proyectos WHERE id_usuario = '$id' LIMIT 3";
                                            $resultado_proyectos = mysqli_query($conexion, $consulta_proyectos);
                                            while ($proyecto = mysqli_fetch_assoc($resultado_proyectos)) : ?>
                                                <tr>
                                                    <td><?php echo $proyecto['id_proyecto']; ?></td>
                                                    <td><?php echo $proyecto['n_proyecto']; ?></td>
                                                    <td><?php echo $proyecto['fecha_inicio']; ?></td>
                                                    <td><?php echo $proyecto['fecha_fin']; ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="Dashboard_proyecto.php" class="btn btn-primary btn-sm">Ver más</a>
                            </div>
                        </div>
                    </div>

                    <!-- Equipos Creados por el Líder -->
                    <div class="col-md-6">
                        <div class="card text-dark bg-light mb-3 shadow">
                            <div class="card-body">
                                <h5><b>Equipos Creados (Total: </b><?php echo $total_equipos; ?>)</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th scope="col">ID Equipo</th>
                                                <th scope="col">Proyecto</th>
                                                <th scope="col">Miembros</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Consulta para obtener los equipos creados por el líder
                                            $resultado_equipos = mysqli_query($conexion, "SELECT e.id_equipo, p.n_proyecto, GROUP_CONCAT(u.n_usuario SEPARATOR ', ') AS miembros
                                            FROM equipos e JOIN proyectos p ON e.id_proyecto = p.id_proyecto JOIN detalle_equipos de ON e.id_equipo = de.id_equipo
                                            JOIN usuarios u ON de.id_usuario = u.id_usuario WHERE e.id_usuario = '$id'
                                            GROUP BY e.id_equipo, p.n_proyecto LIMIT 3");
                                            // Verifica si la consulta tiene resultados
                                            if ($resultado_equipos) {
                                                while ($equipo = mysqli_fetch_assoc($resultado_equipos)) : ?>
                                                    <tr>
                                                        <td><?php echo $equipo['id_equipo']; ?></td>
                                                        <td><?php echo $equipo['n_proyecto']; ?></td>
                                                        <td><?php echo $equipo['miembros']; ?></td>
                                                    </tr>
                                            <?php endwhile;
                                            } else {
                                                echo "No se encontraron equipos.";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="Dashboard_equipo.php" class="btn btn-success btn-sm">Ver más</a>
                            </div>
                        </div>
                    </div>

                    <!-- Actividades Creadas por el Líder -->
                    <div class="col-md-6">
                        <div class="card text-dark bg-light mb-3 shadow">
                            <div class="card-body">
                                <h5><b>Actividades Creadas (Total: </b><?php echo $total_actividades; ?>)</b></h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th scope="col">ID Actividad</th>
                                                <th scope="col">Actividad</th>
                                                <th scope="col">Fecha Inical</th>
                                                <th scope="col">Fecha Final</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Consulta para obtener las actividades asignadas al líder
                                            $consulta_actividades = "SELECT ap.id_actividad_proyecto, ap.n_actividad, ap.descripcion, ap.fecha_inicio, ap.fecha_fin, ap.horas_estimadas, 
                                            ap.horas_utilizadas, p.id_proyecto, p.n_proyecto AS nombre_proyecto, u.id_usuario, u.n_usuario AS nombre_responsable
                                            FROM actividades_proyecto ap JOIN proyectos p ON ap.id_proyecto = p.id_proyecto JOIN usuarios u ON ap.id_usuario = u.id_usuario
                                            WHERE p.id_usuario = '$id' LIMIT 3";
                                            $resultado_actividades = mysqli_query($conexion, $consulta_actividades);
                                            while ($actividad = mysqli_fetch_assoc($resultado_actividades)) : ?>
                                                <tr>
                                                    <td><?php echo $actividad['id_actividad_proyecto']; ?></td>
                                                    <td><?php echo $actividad['n_actividad']; ?></td>
                                                    <td><?php echo $actividad['fecha_inicio']; ?></td>
                                                    <td><?php echo $actividad['fecha_fin']; ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="Dashboard_actividades.php" class="btn btn-warning btn-sm">Ver más</a>
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