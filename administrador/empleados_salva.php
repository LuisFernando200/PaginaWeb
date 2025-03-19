<?php
require "funciones/conecta.php";
$con = conecta();


$nombre = $_REQUEST['nombre'];
$apellidos = $_REQUEST['apellidos'];
$correo = $_REQUEST['correo'];
$pass = $_REQUEST['pass'];
$rol = $_REQUEST['rol'];


$passEnc = md5($pass);


$archivo_n = '';
$archivo = '';

// Verifica 
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $archivo = $_FILES['archivo'];
    $archivo_n = basename($archivo['name']); // Nombre original

    // nombre ubico 
    $nombreEncriptado = md5(uniqid(rand(), true)) . "." . pathinfo($archivo_n, PATHINFO_EXTENSION);

    // Carpeta den
    $carpetaDestino = 'fotos/';

    // "fotos"
    $rutaArchivo = $carpetaDestino . $nombreEncriptado;
    if (move_uploaded_file($archivo['tmp_name'], $rutaArchivo)) {
        // Guardar 
        $sql = "INSERT INTO empleados 
        (nombre, apellidos, correo, pass, rol, archivo_n, archivo)
        VALUES ('$nombre', '$apellidos', '$correo', '$passEnc', $rol, '$archivo_n', '$nombreEncriptado')";
        $con->query($sql);
    } else {
        echo "Error al subir el archivo.";
        exit();
    }
} else {
    //  insertar sin foto
    $sql = "INSERT INTO empleados 
    (nombre, apellidos, correo, pass, rol, archivo_n, archivo)
    VALUES ('$nombre', '$apellidos', '$correo', '$passEnc', $rol, '', '')";
    $con->query($sql);
}

// Redirigir a la lista de empleados
header("Location: empleados_lista.php");
exit();
?>
