<?php
session_start();
require "funciones/conecta.php";
$con = conecta();

$response = array('existe' => false, 'message' => 'Error inesperado.');

// Verificar si los campos 'correo' y 'pass' han sido enviados
if (isset($_POST['correo']) && isset($_POST['pass'])) {
    $correo = $_POST['correo'];
    $pass = md5($_POST['pass']);

    // Consulta SQL para verificar si el usuario existe y está activo
    $sql = "SELECT * FROM empleados WHERE correo = ? AND pass = ? AND eliminado = 0";
    
    if ($stmt = $con->prepare($sql)) {
        // Vincular los parámetros
        $stmt->bind_param("ss", $correo, $pass);
        $stmt->execute();
        $result = $stmt->get_result();

        // Si hay coincidencias, el usuario existe
        if ($result->num_rows > 0) {
            $row = $result->fetch_array();
            $id = $row["id"];
            $nombre   =$row["nombre"].' '.$row["apellidos"];
            $correo  =$row["correo"];

            $_SESSION['idUser']  = $id;
            $_SESSION['nombreUser']  = $nombre;
            $_SESSION['correoUser']  = $correo;

            $response['existe'] = true;
            $response['message'] = 'Usuario verificado';
        } else {
            $response['message'] = 'Credenciales incorrectas o usuario no activo';
        }

        // Cerrar la sentencia preparada
        $stmt->close();
    } else {
        $response['message'] = 'Error en la consulta SQL.';
    }
} else {
    $response['message'] = 'Faltan campos por llenar.';

}

// Enviar la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);

// Cerrar la conexión
$con->close();
?>
