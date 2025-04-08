<?php
include '../connection/connection.php';

if (isset($_GET['id_proyecto'])) {
    $id_proyecto = $_GET['id_proyecto'];

    // Consulta para obtener los integrantes del equipo de ese proyecto
    $query = "SELECT u.id_usuario, u.n_usuario 
            FROM usuarios u
            JOIN detalle_equipos de ON u.id_usuario = de.id_usuario
            JOIN equipos e ON de.id_equipo = e.id_equipo
            WHERE e.id_proyecto = '$id_proyecto'";

    $result = mysqli_query($conexion, $query);

    echo '<option selected>Escoge un Responsable</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='" . $row['id_usuario'] . "'>" . $row['n_usuario'] . "</option>";
    }
}
?>

