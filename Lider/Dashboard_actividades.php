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
    $query_actividades = "SELECT 
    ap.id_actividad_proyecto, 
    ap.n_actividad, 
    ap.descripcion, 
    ap.fecha_inicio, 
    ap.fecha_fin, 
    ap.horas_estimadas, 
    ap.horas_utilizadas, 
    p.id_proyecto,
    p.n_proyecto AS nombre_proyecto,
    u.id_usuario,
    u.n_usuario AS nombre_responsable
FROM actividades_proyecto ap
JOIN proyectos p ON ap.id_proyecto = p.id_proyecto
JOIN usuarios u ON ap.id_usuario = u.id_usuario
WHERE p.id_usuario = '$id'"; // Solo obtiene actividades de proyectos del líder
    $resultado_actividad = mysqli_query($conexion, $query_actividades);
}

// Consulta para obtener los datos de proyectos
$query1 = "SELECT id_proyecto, n_proyecto FROM proyectos WHERE id_usuario = '$id'";
$result1 = $conexion->query($query1);

$query2 = "SELECT id_proyecto, n_proyecto FROM proyectos WHERE id_usuario = '$id'";
$result2 = $conexion->query($query2);


// Consulta para obtener los equipos, filtrando si hay un término de búsqueda
// Inicializamos la variable de búsqueda
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
                <h4 class="me-auto"><b>Creación de Actividades</b></h4>
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
                <button class="btn btn-primary" onclick="openCreateModal()">Crear Nueva Actividad</button>
                <form class="d-flex" method="GET">
                    <input class="form-control me-2" type="search" placeholder="Buscar usuario" name="search">
                    <button class="btn btn-outline-success" type="submit">Buscar</button>
                </form>
            </div>
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Actividad</th>
                        <th>Descripción</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Horas Diarias</th>
                        <th>Horas Utilizadas</th>
                        <th>Proyecto</th>
                        <th>Responsable</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($actividad = mysqli_fetch_assoc($resultado_actividad)) : ?>
                        <tr>
                            <td><?php echo $actividad['n_actividad']; ?></td>
                            <td><?php echo $actividad['descripcion']; ?></td>
                            <td><?php echo $actividad['fecha_inicio']; ?></td>
                            <td><?php echo $actividad['fecha_fin']; ?></td>
                            <td><?php echo $actividad['horas_estimadas']; ?></td>
                            <td><?php echo $actividad['horas_utilizadas']; ?></td>
                            <td><?php echo $actividad['nombre_proyecto']; ?></td>
                            <td><?php echo $actividad['nombre_responsable']; ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" style="margin-bottom: 5px;"
                                    onclick="editUser(
                                            '<?php echo $actividad['id_actividad_proyecto']; ?>', 
                                            '<?php echo $actividad['n_actividad']; ?>', 
                                            '<?php echo $actividad['descripcion']; ?>', 
                                            '<?php echo $actividad['fecha_inicio']; ?>', 
                                            '<?php echo $actividad['fecha_fin']; ?>',
                                            '<?php echo $actividad['horas_estimadas']; ?>',
                                            '<?php echo $actividad['horas_utilizadas']; ?>',
                                            '<?php echo $actividad['id_proyecto']; ?>',
                                            '<?php echo $actividad['id_usuario']; ?>')">
                                    Editar
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="showDeleteModal(<?php echo $actividad['id_actividad_proyecto']; ?>)">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para crear Actividad -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Actividad</h5>
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
                    <form id="createForm" method="POST" action="../Lider_CRUD/Craer_Actividad.php">
                        <div class="mb-3">
                            <label class="form-label">Actividad</label>
                            <input type="text" class="form-control" name="n_actividad" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control" name="descripcion" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de Creación</label>
                            <input type="date" class="form-control" name="fecha_inicio" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de Finalización</label>
                            <input type="date" class="form-control" name="fecha_fin" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Horas Diarias</label>
                            <input type="number" class="form-control" name="horas_estimadas" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Horas Utilizadas</label>
                            <input type="number" class="form-control" name="horas_utilizadas" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Proyecto</label>
                            <select name="proyecto" class="form-select" required id="selectProyecto" onchange="cargarResponsables()">
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
                            <label class="form-label">Responsable</label>
                            <select name="responsable" class="form-select" required id="selectResponsable">
                                <option selected>Escoge un Responsable</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Crear Actividad</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar act -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Actividad</h5>
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
                    <form id="createForm" method="POST" action="../Lider_CRUD/Editar_Actividad.php">
                        <input type="hidden" name="id_actividad_proyecto" id="edit-id">
                        <div class="mb-3">
                            <label class="form-label">Actividad</label>
                            <input type="text" class="form-control" name="n_actividad" id="edit-n_actividad" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control" name="descripcion" id="edit-descripcion" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de Creación</label>
                            <input type="date" class="form-control" name="fecha_inicio" id="edit-fecha_inicio" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de Finalización</label>
                            <input type="date" class="form-control" name="fecha_fin" id="edit-fecha_fin" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Horas Diarias</label>
                            <input type="number" class="form-control" name="horas_estimadas" id="edit-horas_estimadas" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Horas Utilizadas</label>
                            <input type="number" class="form-control" name="horas_utilizadas" id="edit-horas_utilizadas" disabled>
                        </div>
                        <!-- Selector de Proyecto -->
                        <div class="mb-3">
                            <label class="form-label">Proyecto</label>
                            <select name="proyecto" class="form-select" id="edit-proyecto" required>
                                <?php
                                $queryProyectos = "SELECT id_proyecto, n_proyecto FROM proyectos WHERE id_usuario = '$id'";
                                $resultProyectos = $conexion->query($queryProyectos);

                                while ($proyecto = $resultProyectos->fetch_assoc()) {
                                    echo "<option value='{$proyecto['id_proyecto']}'>{$proyecto['n_proyecto']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Selector de Responsable -->
                        <div class="mb-3">
                            <label class="form-label">Responsable</label>
                            <select name="responsable" class="form-select" id="edit-responsable" required>
                                <?php
                                $queryUsuarios = "SELECT id_usuario, n_usuario FROM usuarios WHERE id_rol = 3";
                                $resultUsuarios = $conexion->query($queryUsuarios);

                                while ($usuario = $resultUsuarios->fetch_assoc()) {
                                    echo "<option value='{$usuario['id_usuario']}'>{$usuario['n_usuario']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Actualizar Actividad</button>
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
                    ¿Estás seguro de que deseas eliminar esta actividad? Esta acción no se puede deshacer.
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
                    ¡La actividad ha sido eliminado exitosamente!
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

        // Función para editar actividad
        function editUser(id, n_actividad, descripcion, fecha_inicio, fecha_fin, horas_estimadas, horas_utilizadas, id_proyecto, id_usuario) {
            // Asignar valores a los campos básicos
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-n_actividad').value = n_actividad;
            document.getElementById('edit-descripcion').value = descripcion;
            document.getElementById('edit-fecha_inicio').value = fecha_inicio;
            document.getElementById('edit-fecha_fin').value = fecha_fin;
            document.getElementById('edit-horas_estimadas').value = horas_estimadas;
            document.getElementById('edit-horas_utilizadas').value = horas_utilizadas;

            // Seleccionar el proyecto actual
            const proyectoSelect = document.getElementById('edit-proyecto');
            for (let option of proyectoSelect.options) {
                if (option.value == id_proyecto) {
                    option.selected = true;
                    break;
                }
            }

            // Seleccionar el responsable actual
            const responsableSelect = document.getElementById('edit-responsable');
            for (let option of responsableSelect.options) {
                if (option.value == id_usuario) {
                    option.selected = true;
                    break;
                }
            }

            // Mostrar el modal de edición
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }

        // Función para cargar responsables en el modal de edición
        function cargarResponsablesEdit(id_proyecto, id_usuario_seleccionado = null) {
            if (!id_proyecto) {
                document.getElementById("edit-responsable").innerHTML = "<option value=''>Seleccione un responsable</option>";
                return;
            }

            fetch(`obtener_integrantes2.php?id_proyecto=${id_proyecto}`)
                .then(response => response.text())
                .then(data => {
                    const selectResponsable = document.getElementById("edit-responsable");
                    selectResponsable.innerHTML = "<option value=''>Seleccione un responsable</option>" + data;

                    // Seleccionar el responsable actual si está definido
                    if (id_usuario_seleccionado) {
                        for (let option of selectResponsable.options) {
                            if (option.value == id_usuario_seleccionado) {
                                option.selected = true;
                                break;
                            }
                        }
                    }
                })
                .catch(error => console.error('Error al cargar responsables:', error));
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

        //Script de eliminación de actividad
        function showDeleteModal(id_actividad) {
            const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            document.getElementById('confirmDeleteBtn').href = `../Lider_CRUD/Eliminar_Actividad.php?id_actividad_proyecto=${id_actividad}`;
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

        //Script para escoger un integrante del equipo para cada act por proyecto
        function cargarResponsables() {
            var id_proyecto = document.getElementById("selectProyecto").value;

            // Verifica que el usuario haya seleccionado un proyecto válido
            if (id_proyecto !== "Escoge un Proyecto") {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "obtener_integrantes.php?id_proyecto=" + id_proyecto, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        document.getElementById("selectResponsable").innerHTML = xhr.responseText;
                    }
                };
                xhr.send();
            } else {
                document.getElementById("selectResponsable").innerHTML = "<option selected>Escoge un Responsable</option>";
            }
        }
    </script>
</body>

</html>