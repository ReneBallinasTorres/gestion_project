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

    <?php
    /*Consultas para invocar datos de BD y Mostarlos (Aqui seran tosdas las invocaciopnes, consultas, etc.
    Para que no choque con los permisos y validaciones de acceso que estan arriba)*/
    include '../connection/connection.php';
    $query = "SELECT usuarios.*, roles.n_rol 
        FROM usuarios JOIN roles ON usuarios.id_rol = roles.id_rol";
    $resultado_usuarios = mysqli_query($conexion, $query);
    

    // Consulta para obtener los datos de docente_tutor y tutor
    $query1 = "SELECT id_rol, n_rol FROM roles;";
    $result1 = $conexion->query($query1);

    $query2 = "SELECT id_rol, n_rol FROM roles;";
    $result2 = $conexion->query($query2);


    // Consulta para obtener los usuarios, filtrando si hay un término de búsqueda
    $search = "";
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = mysqli_real_escape_string($conexion, $_GET['search']);
        $query = "SELECT usuarios.*, roles.n_rol 
            FROM usuarios 
            JOIN roles ON usuarios.id_rol = roles.id_rol
            WHERE usuarios.n_usuario LIKE '%$search%' 
            OR usuarios.a_p LIKE '%$search%' 
            OR usuarios.a_m LIKE '%$search%' 
            OR usuarios.correo LIKE '%$search%'";
    } else {
        $query = "SELECT usuarios.*, roles.n_rol 
            FROM usuarios 
            JOIN roles ON usuarios.id_rol = roles.id_rol";
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
                    <h4 class="me-auto"><b>Dashboard</b></h4>
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
                    <button class="btn btn-primary" onclick="openCreateModal()">Crear usuario nuevo</button>
                    <form class="d-flex" method="GET">
                        <input class="form-control me-2" type="search" placeholder="Buscar usuario" name="search">
                        <button class="btn btn-outline-success" type="submit">Buscar</button>
                    </form>
                </div>
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Usuario</th>
                            <th>Nombre</th>
                            <th>Apellido Paterno</th>
                            <th>Apellido Materno</th>
                            <th>Edad</th>
                            <th>Correo</th>
                            <th>password</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($usuario = mysqli_fetch_assoc($resultado_usuarios)) : ?>
                            <tr>
                                <td><?php echo $usuario['id_usuario']; ?></td>
                                <td><?php echo $usuario['n_usuario']; ?></td>
                                <td><?php echo $usuario['a_p']; ?></td>
                                <td><?php echo $usuario['a_m']; ?></td>
                                <td><?php echo $usuario['edad']; ?></td>
                                <td><?php echo $usuario['correo']; ?></td>
                                <td><?php echo $usuario['pass']; ?></td>
                                <td><?php echo $usuario['n_rol']; ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm"
                                        onclick="editUser(
                                            '<?php echo $usuario['id_usuario']; ?>', 
                                            '<?php echo $usuario['n_usuario']; ?>', 
                                            '<?php echo $usuario['a_p']; ?>', 
                                            '<?php echo $usuario['a_m']; ?>', 
                                            '<?php echo $usuario['edad']; ?>', 
                                            '<?php echo $usuario['correo']; ?>', 
                                            '<?php echo $usuario['pass']; ?>', 
                                            '<?php echo $usuario['id_rol']; ?>')">
                                        Editar
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="showDeleteModal(<?php echo $usuario['id_usuario']; ?>)">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal para crear usuario -->
        <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Crear Usuario</h5>
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
                        <form id="createForm" method="POST" action="../CRUD/Craer_Usuario.php">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="n_usuario" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Apellido Paterno</label>
                                <input type="text" class="form-control" name="a_p" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Apellido Materno</label>
                                <input type="text" class="form-control" name="a_m" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Edad</label>
                                <input type="number" class="form-control" name="edad" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Correo</label>
                                <input type="email" class="form-control" name="correo" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="pass" id="create-password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('create-password', 'toggleCreateIcon')">
                                        <i id="toggleCreateIcon" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rol</label>
                                <select name="rol" class="form-select" required>
                                    <option selected>Escoge un Rol</option>
                                    <?php
                                    if ($result1->num_rows > 0) {
                                        while ($row = $result1->fetch_assoc()) {
                                            echo "<option value='" . $row['id_rol'] . "'>" . $row['n_rol'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Crear Usuario</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para editar usuario -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Usuario</h5>
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
                        <form id="editForm" method="POST" action="../CRUD/Editar_Usuario.php">
                            <input type="hidden" name="id_usuario" id="edit-id">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="n_usuario" id="edit-nombre" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Apellido Paterno</label>
                                <input type="text" class="form-control" name="a_p" id="edit-apellido-paterno" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Apellido Materno</label>
                                <input type="text" class="form-control" name="a_m" id="edit-apellido-materno" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Edad</label>
                                <input type="number" class="form-control" name="edad" id="edit-edad" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Correo</label>
                                <input type="email" class="form-control" name="correo" id="edit-correo" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="pass" id="edit-contraseña" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('edit-contraseña', 'toggleEditIcon')">
                                        <i id="toggleEditIcon" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rol</label>
                                <select name="rol" id="edit-rol" class="form-select" required>
                                    <option selected>Escoge un Rol</option>
                                    <?php
                                    if ($result2->num_rows > 0) {
                                        while ($row = $result2->fetch_assoc()) {
                                            echo "<option value='" . $row['id_rol'] . "'>" . $row['n_rol'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Actualizar Usuario</button>
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
                        ¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.
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
            function editUser(id, nombre, apellidoPaterno, apellidoMaterno, edad, correo, contraseña, rol) {
                // Rellenar el modal con los datos del usuario
                document.getElementById('edit-id').value = id;
                document.getElementById('edit-nombre').value = nombre;
                document.getElementById('edit-apellido-paterno').value = apellidoPaterno;
                document.getElementById('edit-apellido-materno').value = apellidoMaterno;
                document.getElementById('edit-edad').value = edad;
                document.getElementById('edit-correo').value = correo;
                document.getElementById('edit-contraseña').value = contraseña;
                document.getElementById('edit-rol').value = rol;

                // Mostrar el modal
                const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                editModal.show();
            }

            //Script para mostrar y ocultar contraseña
            function togglePassword(inputId, iconId) {
                let passwordInput = document.getElementById(inputId);
                let icon = document.getElementById(iconId);

                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                } else {
                    passwordInput.type = "password";
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                }
            }

            //Script de eliminación de usuario
            function showDeleteModal(id_usuario) {
                // Establece el enlace de confirmación para eliminar el usuario
                var confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
                confirmDeleteBtn.href = '../CRUD/Eliminar_Usuario.php?id_usuario=' + id_usuario;

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
        </script>
    </body>

    </html>