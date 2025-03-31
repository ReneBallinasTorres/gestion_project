    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">GP Gestión de Proyectos</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item me-3"><a class="nav-link" href="../Index.php">Inicio</a></li>
                <li class="nav-item me-3"><a class="nav-link" href="Sobre_Nosotros.php">Sobre Nosotros</a></li>
                <li class="nav-item me-3"><a class="nav-link" href="Contacto.php">Contacto</a></li>
            </ul>
            <div class="d-flex">
                <a href="../components/Opcion_Login.php" class="btn btn-outline-light me-2">Iniciar Sesión</a>
            </div>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>