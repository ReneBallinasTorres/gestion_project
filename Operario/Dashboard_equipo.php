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

// Consulta para verificar si el usuario tiene rol de Operario (id_rol = 3)
$consulta = "SELECT * FROM usuarios WHERE id_usuario = '$id' AND id_rol = 3";
$resultado = mysqli_query($conexion, $consulta);
$Operario = mysqli_fetch_assoc($resultado);

// Si el usuario no es admin, redirigirlo
if (!$Operario) {
    echo '<script>
            alert("Acceso denegado. No tienes permisos de Operario.");
            window.location="../Index.php";
        </script>';
    session_destroy();
    die();
}

// Cerrar conexión
mysqli_close($conexion);
?>

<?php
include '../connection/connection.php';

    // Obtener los equipos donde el operario participa, pero no es el creador
    $query_equipos = "SELECT e.*, p.n_proyecto, GROUP_CONCAT(u.n_usuario SEPARATOR ', ') AS miembros FROM detalle_equipos de
    INNER JOIN equipos e ON de.id_equipo = e.id_equipo INNER JOIN proyectos p ON e.id_proyecto = p.id_proyecto
    LEFT JOIN detalle_equipos de2 ON de2.id_equipo = e.id_equipo LEFT JOIN usuarios u ON de2.id_usuario = u.id_usuario
    WHERE de.id_usuario = '$id' AND e.id_usuario != '$id' GROUP BY e.id_equipo, p.n_proyecto";
    $resultado_equipos = mysqli_query($conexion, $query_equipos);

// Inicializamos la variable de búsqueda
$search = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = "%" . mysqli_real_escape_string($conexion, $_GET['search']) . "%";

    // Preparamos y vinculamos los parámetros
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("ssss", $search, $search, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Si no hay término de búsqueda, obtenemos todos los equipos
    $query = "SELECT equipos.* FROM equipos";
    $result = $conexion->query($query);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operario Dashboard</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos FontAwesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../Styles/style_dashboard.css">
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Operario Panel</h2>
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
                <h4 class="me-auto"><b>Equipos Designados</b></h4>
                <div class="d-flex align-items-center">
                    <span class="me-3"><i class="fas fa-bell"></i> <b>Notificaciones</b></span>
                    <span class="me-3"><i class="fas fa-user"></i> <b><?php echo $Operario['n_usuario']; ?> <?php echo $Operario['a_p']; ?> <?php echo $Operario['a_m']; ?></b></span>
                    <a href="../Logout.php" class="btn btn-danger btn-sm">Cerrar sesión</a>
                </div>
            </div>
        </nav>

        <!-- Formulario y Tabla -->
        <div class="container mt-4">
            <div class="d-flex justify-content-between mb-3">
                <form class="d-flex" method="GET">
                    <input class="form-control me-2" type="search" placeholder="Buscar usuario" name="search">
                    <button class="btn btn-outline-success" type="submit">Buscar</button>
                </form>
            </div>
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID Equipo</th>
                        <th>Fecha de Creación</th>
                        <th>Descripción</th>
                        <th>Estado Equipo</th>
                        <th>Proyecto designado</th>
                        <th>Miembros</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($equipos = mysqli_fetch_assoc($resultado_equipos)) : ?>
                        <tr>
                            <td><?php echo $equipos['id_equipo']; ?></td>
                            <td><?php echo $equipos['fecha_creacion']; ?></td>
                            <td><?php echo $equipos['descripcion']; ?></td>
                            <td><?php echo $equipos['estado_equipo']; ?></td>
                            <td><?php echo $equipos['n_proyecto']; ?></td>
                            <td><?php echo $equipos['miembros']; ?></td> <!-- Aquí se muestran los miembros -->
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap y JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>