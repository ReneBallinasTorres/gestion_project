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
                    <h4 class="me-auto">Dashboard</h4>
                    <div class="d-flex align-items-center">
                        <span class="me-3"><i class="fas fa-bell"></i> Notificaciones</span>
                        <span class="me-3"><i class="fas fa-user"></i> Admin</span>
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
                            <th>Contraseña</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Datos ficticios de usuarios
                        $usuarios = [
                            [
                                'id' => 1,
                                'nombre' => 'Juan',
                                'apellido_paterno' => 'Pérez',
                                'apellido_materno' => 'Gómez',
                                'edad' => 28,
                                'correo' => 'juan.perez@example.com',
                                'contraseña' => 'juan123',
                                'rol' => 'Desarrollador'
                            ],
                            [
                                'id' => 2,
                                'nombre' => 'Ana',
                                'apellido_paterno' => 'García',
                                'apellido_materno' => 'López',
                                'edad' => 34,
                                'correo' => 'ana.garcia@example.com',
                                'contraseña' => 'ana456',
                                'rol' => 'Revisor'
                            ],
                            [
                                'id' => 3,
                                'nombre' => 'Carlos',
                                'apellido_paterno' => 'Martínez',
                                'apellido_materno' => 'Sánchez',
                                'edad' => 22,
                                'correo' => 'carlos.martinez@example.com',
                                'contraseña' => 'carlos789',
                                'rol' => 'Administrador'
                            ],
                            [
                                'id' => 4,
                                'nombre' => 'María',
                                'apellido_paterno' => 'Rodríguez',
                                'apellido_materno' => 'Fernández',
                                'edad' => 30,
                                'correo' => 'maria.rodriguez@example.com',
                                'contraseña' => 'maria101',
                                'rol' => 'Diseñador'
                            ],
                        ];

                        // Recorrer los usuarios y mostrarlos en la tabla
                        foreach ($usuarios as $usuario) {
                            echo "<tr>";
                            echo "<td>{$usuario['id']}</td>";
                            echo "<td>{$usuario['nombre']}</td>";
                            echo "<td>{$usuario['apellido_paterno']}</td>";
                            echo "<td>{$usuario['apellido_materno']}</td>";
                            echo "<td>{$usuario['edad']}</td>";
                            echo "<td>{$usuario['correo']}</td>";
                            echo "<td>{$usuario['contraseña']}</td>";
                            echo "<td>{$usuario['rol']}</td>";
                            echo "<td>
                                <button class='btn btn-info btn-sm' onclick='editUser({$usuario['id']}, \"{$usuario['nombre']}\", \"{$usuario['apellido_paterno']}\", \"{$usuario['apellido_materno']}\", {$usuario['edad']}, \"{$usuario['correo']}\", \"{$usuario['contraseña']}\", \"{$usuario['rol']}\")'>Editar</button>
                                <button class='btn btn-danger btn-sm'>Eliminar</button>
                            </td>";
                            echo "</tr>";
                        }
                        ?>
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
                        <form id="createForm" method="POST" action="crear_usuario.php">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Apellido Paterno</label>
                                <input type="text" class="form-control" name="apellido_paterno" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Apellido Materno</label>
                                <input type="text" class="form-control" name="apellido_materno" required>
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
                                <input type="password" class="form-control" name="contraseña" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rol</label>
                                <input type="text" class="form-control" name="rol" required>
                            </div>
                            <button type="submit" class="btn btn-success">Crear Usuario</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Bootstrap y JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            function openCreateModal() {
                const createModal = new bootstrap.Modal(document.getElementById('createModal'));
                createModal.show();
            }


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
        </script>
    </body>

    </html>