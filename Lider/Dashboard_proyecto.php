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
/*Consultas para invocar datos de BD y Mostarlos (Aqui seran tosdas las invocaciopnes, consultas, etc.
Para que no choque con los permisos y validaciones de acceso que estan arriba)*/
include '../connection/connection.php';
$query = "SELECT proyectos.*, usuarios.n_usuario, usuarios.a_p, usuarios.a_m 
        FROM proyectos JOIN usuarios ON proyectos.id_usuario = usuarios.id_usuario
        WHERE proyectos.id_usuario = '$id'"; // Filtra proyectos solo para el usuario actual (líder)
$resultado_proyectos = mysqli_query($conexion, $query);

// Consulta para obtener los datos de proyectos
$query1 = "SELECT id_usuario, n_usuario, a_p, a_m FROM usuarios WHERE id_rol = 2";
$result1 = $conexion->query($query1);

$query2 = "SELECT id_usuario, n_usuario, a_p, a_m FROM usuarios WHERE id_rol = 2";
$result2 = $conexion->query($query2);

// Consulta para obtener los proyectos, filtrando si hay un término de búsqueda
$search = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conexion, $_GET['search']);
    $query = "SELECT proyectos.* 
            FROM proyectos WHERE proyectos.n_proyecto LIKE '%$search%' 
            OR proyectos.descripcion LIKE '%$search%' 
            OR proyectos.objetivos LIKE '%$search%' 
            OR proyectos.fecha_inicio LIKE '%$search%' 
            OR proyectos.fecha_fin LIKE '%$search%'";
} else {
    $query = "SELECT proyectos.* FROM proyectos";
}
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
                <h4 class="me-auto"><b>Proyectos Designados</b></h4>
                <div class="d-flex align-items-center">
                    <span class="me-3"><i class="fas fa-bell"></i> <b>Notificaciones</b></span>
                    <span class="me-3"><i class="fas fa-user"></i> <b><?php echo $Lider['n_usuario']; ?> <?php echo $Lider['a_p']; ?> <?php echo $Lider['a_m']; ?></b></span>
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
                        <th>ID Proyecto</th>
                        <th>Nombre</th>
                        <th>Objetivos</th>
                        <th>Descripción</th>
                        <th>Fecha de Inicio</th>
                        <th>Fecha de Finalización</th>
                        <th>Observaciones</th>
                        <th>Lider Encargado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($proyecto = mysqli_fetch_assoc($resultado_proyectos)) : ?>
                        <tr>
                            <td><?php echo $proyecto['id_proyecto']; ?></td>
                            <td><?php echo $proyecto['n_proyecto']; ?></td>
                            <td><?php echo $proyecto['objetivos']; ?></td>
                            <td><?php echo $proyecto['descripcion']; ?></td>
                            <td><?php echo $proyecto['fecha_inicio']; ?></td>
                            <td><?php echo $proyecto['fecha_fin']; ?></td>
                            <td><?php echo $proyecto['observaciones']; ?></td>
                            <td><?php echo $proyecto['n_usuario'] . ' ' . $proyecto['a_p'] . ' ' . $proyecto['a_m']; ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" style="margin-bottom: 5px;"
                                    onclick="editUser(
                                            '<?php echo $proyecto['id_proyecto']; ?>', 
                                            '<?php echo $proyecto['n_proyecto']; ?>', 
                                            '<?php echo $proyecto['objetivos']; ?>', 
                                            '<?php echo $proyecto['descripcion']; ?>', 
                                            '<?php echo $proyecto['fecha_inicio']; ?>', 
                                            '<?php echo $proyecto['fecha_fin']; ?>', 
                                            '<?php echo $proyecto['observaciones']; ?>', 
                                            '<?php echo $proyecto['id_usuario']; ?>')">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para editar Proyecto -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Proyecto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Mostrar el mensaje -->
                    <?php if (isset($_SESSION['mensaje'])): ?>
                        <div id="alerta-mensaje" class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?> alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['mensaje']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['mensaje']);
                        unset($_SESSION['tipo_mensaje']); ?>
                    <?php endif; ?>
                    <form id="editForm" method="POST" action="../Lider_CRUD/Editar_Proyecto.php">
                        <input type="hidden" name="id_proyecto" id="edit-id">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="n_proyecto" id="edit-nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Objetivo</label>
                            <input type="text" class="form-control" name="objetivos" id="edit-objetivos" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control" name="descripcion" id="edit-descripcion" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" id="edit-fecha_inicio" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de Finalización</label>
                            <input type="date" class="form-control" name="fecha_fin" id="edit-fecha_fin" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <input type="text" class="form-control" name="observaciones" id="edit-observaciones" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lider</label>
                            <select name="lider" id="edit-lider" class="form-select" disabled>
                                <option selected>Escoge un Lider</option>
                                <?php
                                if ($result2->num_rows > 0) {
                                    while ($row = $result2->fetch_assoc()) {
                                        echo "<option value='" . $row['id_usuario'] . "'>" . $row['n_usuario'] . " " . $row['a_p'] . " " . $row['a_m'] . "</option>";
                                    }
                                }
                                ?>
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Actualizar Proyecto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap y JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        //Script de Modal de Edición
        function editUser(id, nombre, objetivos, descripcion, fecha_inicio, fecha_fin, observaciones, lider) {
            // Rellenar el modal con los datos del proyecto
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nombre').value = nombre;
            document.getElementById('edit-objetivos').value = objetivos;
            document.getElementById('edit-descripcion').value = descripcion;
            document.getElementById('edit-fecha_inicio').value = fecha_inicio;
            document.getElementById('edit-fecha_fin').value = fecha_fin;
            document.getElementById('edit-observaciones').value = observaciones;
            document.getElementById('edit-lider').value = lider;

            // Mostrar el modal
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }

        //Script de tiempo de visualización del mensaje del formulario
        document.addEventListener("DOMContentLoaded", function() {
            let alert = document.getElementById('alerta-mensaje');

            // Mostrar el modal solo si existe la alerta (es decir, si hay un mensaje en sesión)
            if (alert) {
                const modal = new bootstrap.Modal(document.getElementById('editModal'));
                modal.show();

                // Desaparecer la alerta después de 3 segundos
                setTimeout(function() {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 2400);
            }
        });
    </script>

</body>

</html>