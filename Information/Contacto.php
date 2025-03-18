<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .contact-section {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
        }

        .contact-section div:first-child {
            flex: 2;
            padding-right: 40px;
        }

        .contact-section div:last-child {
            flex: 1;
        }

        .contact-section img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .contact-title {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #007bff;
            /* Cambia el color del título */
        }

        .contact-text {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-body {
            padding: 20px;
            text-align: center;
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .card-text {
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .bi {
            color: #007bff;
        }

        h1 {
            color: #333;
        }

        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        h3 {
            color: #007bff;
        }
    </style>
</head>

<body>
    <?php include '../components/Navbar-Inf.php'; ?>

    <div class="container">
        <div class="contact-section">
            <div>
                <h1>Ponte en contacto con nosotros para obtener información sobre GP Gestión de Proyectos</h1> <br>
                <p>En GP Gestión de Proyectos, nos apasiona ayudarte a alcanzar el éxito en tus proyectos. Te ofrecemos soluciones integrales para optimizar la planificación, ejecución y seguimiento de tus iniciativas, impulsando la eficiencia y la productividad de tu equipo. Descubre cómo podemos colaborar para llevar tus proyectos al siguiente nivel. </p> <br>
                <h3>¡Contáctanos y comencemos a construir juntos!</h3>
            </div>
            <div>
                <img src="../Img/contaco1.jpg" class="img-thumbnail" alt="Contacto">
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="card h-90">
                    <div class="card-body">
                        <i class="bi bi-telephone-fill" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Llámanos Directamente</h5>
                        <p class="card-text">+52 9321040897</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <i class="bi bi-chat-square-text-fill" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Habla con Nuestro Equipo</h5>
                        <button type="button" class="btn btn-primary mt-3" onclick="location.href='nosotros_contacto.php'">Ver Contactos</button>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <i class="bi bi-people-fill" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Conoce Sobre Nosotros</h5>
                        <button type="button" class="btn btn-primary mt-3" onclick="location.href='../Information/Sobre_Nosotros.php'">Conocenos</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../components/Footer-Inf.php'; ?>
</body>

</html>