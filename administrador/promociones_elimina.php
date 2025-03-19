<?php
require "funciones/conecta.php";
$con = conecta();


$response = array('success' => false, 'message' => 'Error inesperado.');

if (isset($_POST['id'])) {
    $id = intval($_POST['id']); 


    $sql = "UPDATE promociones SET eliminado = 1 WHERE id = $id";
    if ($con->query($sql)) {

        $response['success'] = true;
        $response['message'] = 'Producto eliminado exitosamente.';
    } else {
      
        $response['message'] = 'Error en la base de datos: ' . $con->error;
    }
} else {
    $response['message'] = 'ID no proporcionado.';
}


header('Content-Type: application/json');
echo json_encode($response);
?>
