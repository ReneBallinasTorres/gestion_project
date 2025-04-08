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
include '../connection/connection.php';
// Obtener los proyectos creados por el líder
$query = "SELECT p.id_proyecto FROM proyectos p
        JOIN usuarios u ON u.id_usuario = p.id_usuario WHERE u.id_rol = 2 AND u.id_usuario = '$id'";
$resultado_proyectos = mysqli_query($conexion, $query);

// Guardar los ID de los proyectos en un array
$proyectos_lider = [];
while ($row = mysqli_fetch_assoc($resultado_proyectos)) {
    $proyectos_lider[] = $row['id_proyecto'];
}

if (!empty($proyectos_lider)) {
    $proyectos_lider_list = implode(',', $proyectos_lider);

    // Consulta para obtener los equipos creados por el líder junto con el nombre del proyecto
    $query_equipos = "SELECT e.*, p.n_proyecto, GROUP_CONCAT(u.n_usuario SEPARATOR ', ') AS miembros
                FROM equipos e LEFT JOIN detalle_equipos de ON e.id_equipo = de.id_equipo
                LEFT JOIN usuarios u ON de.id_usuario = u.id_usuario LEFT JOIN proyectos p ON e.id_proyecto = p.id_proyecto
                WHERE e.id_usuario = '$id' GROUP BY e.id_equipo, p.n_proyecto";
    $resultado_equipos = mysqli_query($conexion, $query_equipos);
}

// Consulta para obtener los datos de proyectos
$query1 = "SELECT id_proyecto, n_proyecto FROM proyectos WHERE id_usuario = '$id'";
$result1 = $conexion->query($query1);

$query2 = "SELECT id_proyecto, n_proyecto FROM proyectos WHERE id_usuario = '$id'";
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
                <h4 class="me-auto"><b>Creación de Equipos</b></h4>
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

    <!-- Modal para crear Equipo -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Mensaje de alerta -->
                    <?php if (isset($_SESSION['tipo_accion']) && $_SESSION['tipo_accion'] === 'create'): ?>
                        <div id="alerta-mensaje" class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?> alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['mensaje']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <form id="createForm" method="POST" action="../Lider_CRUD/Craer_Equipo.php">
                        <div class="mb-3">
                            <label class="form-label">Fecha de Creación</label>
                            <input type="date" class="form-control" name="fecha_creacion" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control" name="descripcion" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado del Equipo</label>
                            <select class="form-select" name="estado_equipo">
                                <option value="Activo">Activo</option>
                                <option value="En Pausa">En Pausa</option>
                                <option value="Finalizado">Finalizado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Proyecto</label>
                            <select name="proyecto" class="form-select" required>
                                <option selected>Escoge un Proyecto</option>
                                <?php
                                // Consulta para obtener solo los proyectos que no han sido asignados a un equipo
                                $queryProyectos = "SELECT id_proyecto, n_proyecto FROM proyectos 
                                                    WHERE id_proyecto NOT IN (SELECT id_proyecto FROM equipos)";
                                $result1 = $conexion->query($queryProyectos);
                                if ($result1->num_rows > 0) {
                                    while ($row = $result1->fetch_assoc()) {
                                        echo "<option value='" . $row['id_proyecto'] . "'>" . $row['n_proyecto'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <!-- <div class="mb-3">
                            <label class="form-label">Miembros del equipo</label>
                            <div class="form-check">
                                <!?php
                                $queryUsuarios = "SELECT id_usuario, n_usuario FROM usuarios WHERE id_rol = 3";
                                $resultUsuarios = $conexion->query($queryUsuarios);
                                while ($row = $resultUsuarios->fetch_assoc()) {
                                    echo "<div class='form-check'>
                                        <input class='form-check-input' type='checkbox' name='miembros[]' value='{$row['id_usuario']}' id='miembro{$row['id_usuario']}'>
                                        <label class='form-check-label' for='miembro{$row['id_usuario']}'>
                                            {$row['n_usuario']}
                                        </label>
                                    </div>";
                                }
                                ?>
                            </div>
                        </div> -->
                        <div class="mb-3">
                            <label class="form-label">Miembros del equipo (Ctrl + Click para seleccionar múltiples)</label>
                            <select name="miembros[]" class="form-select" multiple size="3" required>
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
                    <!-- Mensaje de alerta -->
                    <?php if (isset($_SESSION['tipo_accion']) && $_SESSION['tipo_accion'] === 'edit'): ?>
                        <div id="alerta-mensaje" class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?> alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['mensaje']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <form id="editForm" method="POST" action="../Lider_CRUD/Editar_Equipo.php">
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
        //Script de Modal de creación
        function openCreateModal() {
            const createModal = new bootstrap.Modal(document.getElementById('createModal'));
            createModal.show();
        }

        //Script de Modal de editar
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
            let tipoAccion = '<?php echo isset($_SESSION['tipo_accion']) ? $_SESSION['tipo_accion'] : ''; ?>'; // Obtenemos el tipo de acción desde la sesión

            // Si existe un mensaje
            if (alert) {
                // Mostrar el modal adecuado según el tipo de acción
                if (tipoAccion === 'create') {
                    const modalCreate = new bootstrap.Modal(document.getElementById('createModal'));
                    modalCreate.show();
                } else if (tipoAccion === 'edit') {
                    const modalEdit = new bootstrap.Modal(document.getElementById('editModal'));
                    modalEdit.show();
                }

                // Desaparecer la alerta después de 3 segundos
                setTimeout(function() {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 2050);
                <?php
                // Limpiar la sesión después de mostrar el modal
                unset($_SESSION['tipo_accion']);
                unset($_SESSION['tipo_mensaje']);
                unset($_SESSION['mensaje']);
                ?>
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

        //Script de eliminación de proyecto
        function showDeleteModal(id_equipo) {
            const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            document.getElementById('confirmDeleteBtn').href = `../Lider_CRUD/Eliminar_Equipo.php?id_equipo=${id_equipo}`;
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