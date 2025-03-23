<?php
session_start();
include '../connection/connection.php'; // Conexión a la BD

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['usuarios'])) {
    echo '<script> 
            alert("Por favor, inicia sesión");
            window.location="../components/Login_Lider.php";
        </script>';
    session_destroy();
    die();
}

// Obtiene el ID del usuario desde la sesión
$id = $_SESSION['usuarios'];

// Consulta para verificar si el usuario tiene rol de lider (id_rol = 2)
$consulta = "SELECT * FROM usuarios WHERE id_usuario = '$id' AND id_rol = 2";
$resultado = mysqli_query($conexion, $consulta);
$Lider = mysqli_fetch_assoc($resultado);

// Si el usuario no es Lider, redirigirlo
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
$query = "SELECT equipos.*, GROUP_CONCAT(usuarios.n_usuario SEPARATOR ', ') AS miembros
            FROM equipos LEFT JOIN detalle_equipos ON equipos.id_equipo = detalle_equipos.id_equipo
            LEFT JOIN usuarios ON detalle_equipos.id_usuario = usuarios.id_usuario
            GROUP BY equipos.id_equipo";
$resultado_equipos = mysqli_query($conexion, $query);

// Consulta para obtener los datos de docente_tutor y tutor
$query1 = "SELECT id_proyecto, n_proyecto FROM proyectos";
$result1 = $conexion->query($query1);

$query2 = "SELECT id_proyecto, n_proyecto FROM proyectos";
$result2 = $conexion->query($query2);


// Consulta para obtener los equipos, filtrando si hay un término de búsqueda
$search = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conexion, $_GET['search']);
    $query = "SELECT proyectos.* 
            FROM proyectos
            WHERE proyectos.n_proyecto LIKE '%$search%' 
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
                <h4 class="me-auto"><b>Dashboard</b></h4>
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
                <button class="btn btn-primary" onclick="openCreateModal()">Crear Nuevo Equipo</button>
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
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($equipos = mysqli_fetch_assoc($resultado_equipos)) : ?>
                        <tr>
                            <td><?php echo $equipos['id_equipo']; ?></td>
                            <td><?php echo $equipos['fecha_creacion']; ?></td>
                            <td><?php echo $equipos['descripcion']; ?></td>
                            <td><?php echo $equipos['estado_equipo']; ?></td>
                            <td><?php echo $equipos['id_proyecto']; ?></td>
                            <td><?php echo $equipos['miembros']; ?></td> <!-- Aquí se muestran los miembros -->
                            <td>
                                <button class="btn btn-warning btn-sm" style="margin-bottom: 5px;"
                                    onclick="editUser(
                                            '<?php echo $equipos['id_equipo']; ?>', 
                                            '<?php echo $equipos['fecha_creacion']; ?>', 
                                            '<?php echo $equipos['descripcion']; ?>', 
                                            '<?php echo $equipos['estado_equipo']; ?>', 
                                            '<?php echo $equipos['id_proyecto']; ?>',
                                            '<?php echo $equipos['miembros']; ?>')">
                                    Editar
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="showDeleteModal(<?php echo $proyecto['id_equipo']; ?>)">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para crear Equipo -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Proyecto</h5>
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
                    <form id="createForm" method="POST" action="../Lider_CRUD/Craer_Equipo.php">
                        <div class="mb-3">
                            <label class="form-label">Fecha de Creación</label>
                            <input type="date" class="form-control" name="fecha_creacion" min="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control" name="descripcion" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado del Equipo</label>
                            <select class="form-select" name="estado_equipo">
                                <option value="activo">Activo</option>
                                <option value="pausa">En Pausa</option>
                                <option value="finalizado">Finalizado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Proyecto</label>
                            <select name="proyecto" class="form-select" required>
                                <option selected>Escoge un Proyecto</option>
                                <?php
                                if ($result1->num_rows > 0) {
                                    while ($row = $result1->fetch_assoc()) {
                                        echo "<option value='" . $row['id_proyecto'] . "'>" . $row['n_proyecto'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Miembros del equipo</label>
                            <select name="miembros[]" class="form-select" multiple size="5" required>
                                <?php
                                $queryUsuarios = "SELECT id_usuario, n_usuario FROM usuarios WHERE id_rol = 3"; // Suponiendo que id_rol=3 son miembros
                                $resultUsuarios = $conexion->query($queryUsuarios);
                                while ($row = $resultUsuarios->fetch_assoc()) {
                                    echo "<option value='{$row['id_usuario']}'>{$row['n_usuario']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Crear Equipo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar Equipo -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Equipo</h5>
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
                    <form id="editForm" method="POST" action="../Lider_CRUD/Editar_Equipo.php">
                        <input type="hidden" name="id_proyecto" id="edit-id">
                        <div class="mb-3">
                            <label class="form-label">Fecha de Creación</label>
                            <input type="date" class="form-control" name="fecha_creacion" id="edit-fecha_creacion" min="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control" name="descripcion" id="edit-descripcion" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado del Equipo</label>
                            <select class="form-select" name="estado_equipo" id="edit-estado_equipo">
                                <option value="activo">Activo</option>
                                <option value="pausa">En Pausa</option>
                                <option value="finalizado">Finalizado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Proyecto</label>
                            <select name="proyecto" class="form-select" id="edit-proyecto" required>
                                <option selected>Escoge un Proyecto</option>
                                <?php
                                if ($result1->num_rows > 0) {
                                    while ($row = $result1->fetch_assoc()) {
                                        echo "<option value='" . $row['id_proyecto'] . "'>" . $row['n_proyecto'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Actualizar Equipo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmación de Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar este Equipo? Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Eliminar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap y JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        //Script de Modal de creación
        function openCreateModal() {
            const createModal = new bootstrap.Modal(document.getElementById('createModal'));
            createModal.show();
        }

        //Script de Modal de Edición
        function editUser(id, fecha_creacion, descripcion, estado_equipo, proyecto) {
            // Rellenar el modal con los datos del proyecto
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-fecha_creacion').value = fecha_creacion;
            document.getElementById('edit-descripcion').value = descripcion;
            document.getElementById('edit-estado_equipo').value = estado_equipo;
            document.getElementById('edit-proyecto').value = proyecto;

            // Mostrar el modal
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }

        //Script de eliminación de equipo
        function showDeleteModal(id_equipo) {
            // Establece el enlace de confirmación para eliminar el usuario
            var confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            confirmDeleteBtn.href = '../Lider_CRUD/Eliminar_Equipo.php?id_equipo=' + id_equipo;

            // Muestra el modal de confirmación
            var deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            deleteModal.show();
        }

        //Script de tiempo de visualización del mensaje del formulario
        document.addEventListener("DOMContentLoaded", function() {
            let alert = document.getElementById('alerta-mensaje');

            // Mostrar el modal solo si existe la alerta (es decir, si hay un mensaje en sesión)
            if (alert) {
                const modal = new bootstrap.Modal(document.getElementById('createModal'));
                modal.show();

                // Desaparecer la alerta después de 3 segundos
                setTimeout(function() {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 2200);
            }
        });

        //Script para la fecha de creación
        document.addEventListener("DOMContentLoaded", function() {
            let today = new Date().toISOString().split('T')[0];
            document.getElementById("edit-fecha_inicio").setAttribute("min", today);
        });

        //Script para seleecionar solo 3 operarios por equipo
        document.addEventListener("DOMContentLoaded", function() {
            let selectMiembros = document.querySelector("select[name='miembros[]']");

            selectMiembros.addEventListener("change", function() {
                let selectedOptions = Array.from(selectMiembros.selectedOptions);

                if (selectedOptions.length > 3) {
                    alert("Solo puedes seleccionar un máximo de 3 miembros.");
                    selectedOptions.forEach(option => option.selected = false); // Desselecciona todos
                }
            });
        });
    </script>
</body>

</html>