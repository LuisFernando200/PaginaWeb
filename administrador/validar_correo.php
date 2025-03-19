<?php
    require "funciones/conecta.php";
    $con = conecta();

    $correo = $_POST['correo'];

    // Consultar si el correo ya existe
    $sql = "SELECT * FROM empleados WHERE correo = '$correo' AND eliminado = 0";
    $res = $con->query($sql);

    // Verificamos si hay algún resultado
    if ($res->num_rows > 0) {
        echo json_encode(['existe' => true]);
    } else {
        echo json_encode(['existe' => false]);
    }
?>
