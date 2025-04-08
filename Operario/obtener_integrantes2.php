<?php
include '../connection/connection.php';

if (isset($_GET['id_proyecto'])) {
    $id_proyecto = intval($_GET['id_proyecto']);
    
    // Consulta para obtener los miembros del equipo asignados al proyecto
    $query = "SELECT u.id_usuario, u.n_usuario 
            FROM detalle_equipos de
            JOIN usuarios u ON de.id_usuario = u.id_usuario
            JOIN equipos e ON de.id_equipo = e.id_equipo
            WHERE e.id_proyecto = ? AND u.id_rol = 3"; // Rol 3 para operarios
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_proyecto);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $options = "";
    while ($row = $result->fetch_assoc()) {
        $options .= "<option value='{$row['id_usuario']}'>{$row['n_usuario']}</option>";
    }
    
    echo $options;
    
    $stmt->close();
    $conexion->close();
}
?>