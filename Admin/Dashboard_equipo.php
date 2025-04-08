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
/*Consultas para invocar datos de BD y Mostarlos (Aqui seran tosdas las invocaciopnes, consultas, etc.
    Para que no choque con los permisos y validaciones de acceso que estan arriba)*/
include '../connection/connection.php';
$query = "SELECT equipos.*, GROUP_CONCAT(usuarios.n_usuario SEPARATOR ', ') AS miembros, proyectos.n_proyecto
        FROM equipos LEFT JOIN detalle_equipos ON equipos.id_equipo = detalle_equipos.id_equipo
        LEFT JOIN usuarios ON detalle_equipos.id_usuario = usuarios.id_usuario
        LEFT JOIN proyectos ON equipos.id_proyecto = proyectos.id_proyecto GROUP BY equipos.id_equipo";
$resultado_equipos = mysqli_query($conexion, $query);

// Consulta para obtener los datos de proyectos
$query1 = "SELECT id_proyecto, n_proyecto FROM proyectos";
$result1 = $conexion->query($query1);

$query2 = "SELECT id_proyecto, n_proyecto FROM proyectos";
$result2 = $conexion->query($query2);


// Consulta para obtener los equipos, filtrando si hay un término de búsqueda
$search = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = "%" . mysqli_real_escape_string($conexion, $_GET['search']) . "%";

    // Preparamos la consulta para evitar inyecciones SQL
    $query = "SELECT equipos.* FROM equipos
            WHERE equipos.fecha_creacion LIKE ? OR equipos.descripcion LIKE ? 
            OR equipos.estado_equipo LIKE ? OR equipos.id_proyecto LIKE ?";

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
                <h4 class="me-auto"><b>Equipos Designados</b></h4>
                <div class="d-flex align-items-center">
                    <span class="me-3"><i class="fas fa-bell"></i> <b>Notificaciones</b></span>
                    <span class="me-3"><i class="fas fa-user"></i> <b><?php echo $Admin['n_usuario']; ?> <?php echo $Admin['a_p']; ?> <?php echo $Admin['a_m']; ?></b></span>
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
                            <td><?php echo $equipos['n_proyecto']; ?></td>
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
                                <button class="btn btn-danger btn-sm" onclick="showDeleteModal(<?php echo $equipos['id_equipo']; ?>)">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
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
                    <form id="editForm" method="POST" action="../CRUD/Editar_Equipo.php">
                        <input type="hidden" name="id_equipo" id="edit-id">
                        <div class="mb-3">
                            <label class="form-label">Fecha de Creación</label>
                            <input type="date" class="form-control" name="fecha_creacion" id="edit-fecha_creacion" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control" name="descripcion" id="edit-descripcion" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado del Equipo</label>
                            <select class="form-select" name="estado_equipo" id="edit-estado_equipo" required>
                                <option value="Activo">Activo</option>
                                <option value="En Pausa">En Pausa</option>
                                <option value="Finalizado">Finalizado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Proyecto</label>
                            <select name="proyecto" class="form-select" id="edit-proyecto" required>
                                <option selected>Escoge un Proyecto</option>
                                <?php
                                if ($result2->num_rows > 0) {
                                    while ($row = $result2->fetch_assoc()) {
                                        echo "<option value='" . $row['id_proyecto'] . "'>" . $row['n_proyecto'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Miembros del equipo (Ctrl + Click para seleccionar múltiples)</label>
                            <select name="miembros[]" class="form-select" multiple size="4" id="edit-miembros">
                                <?php
                                $queryUsuarios = "SELECT id_usuario, n_usuario FROM usuarios WHERE id_rol = 3"; // Suponiendo que id_rol=3 son miembros
                                $resultUsuarios = $conexion->query($queryUsuarios);
                                while ($row = $resultUsuarios->fetch_assoc()) {
                                    echo "<option value='{$row['id_usuario']}'>{$row['n_usuario']}</option>";
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

    <!-- Modal de Confirmación de Eliminación -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar este equipo? Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Eliminar</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de Mensajes de Eliminación-->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Eliminación Exitosa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¡El equipo ha sido eliminado exitosamente!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap y JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        //Script de Modal de Editar
        function editUser(id, fecha_creacion, descripcion, estado_equipo, proyecto, miembros) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-fecha_creacion').value = fecha_creacion;
            document.getElementById('edit-descripcion').value = descripcion;

            // Seleccionar el estado del equipo
            let estadoSelect = document.getElementById('edit-estado_equipo');
            for (let option of estadoSelect.options) {
                if (option.value === estado_equipo) {
                    option.selected = true;
                    break;
                }
            }

            // Seleccionar el proyecto
            let proyectoSelect = document.getElementById('edit-proyecto');
            for (let option of proyectoSelect.options) {
                if (option.value === proyecto) {
                    option.selected = true;
                    break;
                }
            }

            // Seleccionar múltiples miembros
            let miembrosSelect = document.getElementById('edit-miembros');
            let miembrosArray = miembros.split(','); // Suponiendo que miembros es una lista separada por comas
            for (let option of miembrosSelect.options) {
                option.selected = miembrosArray.includes(option.value);
            }

            document.getElementById('edit-miembros').addEventListener('change', function() {
                let selectedOptions = this.selectedOptions;
                if (selectedOptions.length > 3) {
                    alert('Puedes seleccionar un máximo de 3 miembros.');
                    // Desmarcar el último miembro seleccionado
                    this.options[selectedOptions[selectedOptions.length - 1].index].selected = false;
                }
            });


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
                }, 2050);
            }
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

        //Script de eliminación de equipo
        function showDeleteModal(id_equipo) {
            const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            document.getElementById('confirmDeleteBtn').href = `../CRUD/Eliminar_Equipo.php?id_equipo=${id_equipo}`;
            deleteModal.show();
        }

        document.addEventListener("DOMContentLoaded", function() {
            let eliminado = '<?php echo isset($_SESSION['eliminado']) ? $_SESSION['eliminado'] : ''; ?>';

            if (eliminado === '1') {
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            }

            <?php unset($_SESSION['eliminado']); ?>
        });
    </script>
</body>

</html>