<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipo de Trabajo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <style>
        /* Espaciado para que las tarjetas no choquen con el navbar */
        .cards-container {
            margin-top: 80px; /* Ajusta según el tamaño del navbar */
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: white;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
        }

        .card-img-top {
            width: 100px;
            height: 280px;
            object-fit: cover;
            border-radius: 30%;
            margin: 20px auto;
            display: block;
            
        }

        .card-body {
            text-align: center;
        }

        .card-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .card-text {
            font-size: 14px;
            color: #5a5a5a;
        }
    </style>
</head>

<body>
    <?php include '../components/Navbar-Inf.php'; ?>

    <div class="container cards-container">
        <div class="row d-flex justify-content-center g-4"> 
            <div class="col-md-3">
                <div class="card">
                    <img src="../Img/batalla.jpg" class="card-img-top" alt="Contacto 1">
                    <div class="card-body">
                        <h5 class="card-title">Elizabeth de la Cruz Batalla</h5>
                        <p class="card-text">+52 932 114 8706</p>
                        <p class="card-text">batalla05.26@gmail.com</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <img src="../Img/cindy.jpg" class="card-img-top" alt="Contacto 2">
                    <div class="card-body">
                        <h5 class="card-title">Cindy Juelidy Rodriguez Perez</h5>
                        <p class="card-text">+52 993 426 7940</p>
                        <p class="card-text">cindyjuleidyperez@gmail.com</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <img src="../Img/rene.jpg" class="card-img-top" alt="Contacto 3">
                    <div class="card-body">
                        <h5 class="card-title">Rene Ballinas Torres</h5>
                        <p class="card-text">+52 932 117 5568</p>
                        <p class="card-text">rene10torres10@gmail.com</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <img src="../Img/luisj.jpg" class="card-img-top" alt="Contacto 4">
                    <div class="card-body">
                        <h5 class="card-title">Luis Javier Castro Gómez</h5>
                        <p class="card-text">+52 932 104 0897</p>
                        <p class="card-text">luisjaviercastrogomez245@gmail.com</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <img src="../Img/santi.jpg" class="card-img-top" alt="Contacto 5">
                    <div class="card-body">
                        <h5 class="card-title">Santiago Beltran Dominguez</h5>
                        <p class="card-text">+52 932 146 8676</p>
                        <p class="card-text">sanbeltranchuggo@gmail.com</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
