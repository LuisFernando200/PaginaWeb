<?php
    require "funciones/conecta.php";
    $con = conecta();

    $codigo = $_POST['codigo'];


    $sql = "SELECT * FROM productos WHERE codigo = '$codigo' AND eliminado = 0";
    $res = $con->query($sql);

    // Verificamos si hay algún resultado
    if ($res->num_rows > 0) {
        echo json_encode(['existe' => true]);
    } else {
        echo json_encode(['existe' => false]);
    }
?>
