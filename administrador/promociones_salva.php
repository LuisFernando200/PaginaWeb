<?php
require "funciones/conecta.php";
$con = conecta();

$nombre = $_REQUEST['nombre'];
$archivo = '';

if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $archivo = $_FILES['archivo'];
   
    // Nombre encriptado
    $nombreEncriptado = md5(uniqid(rand(), true)) . ".jpg"; 

    // Carpeta destino
    $carpetaDestino = 'fotos_promociones/';
    $rutaArchivo = $carpetaDestino . $nombreEncriptado;

    // Mover el archivo
    if (move_uploaded_file($archivo['tmp_name'], $rutaArchivo)) {
        // Guardar en la base de datos
        $sql = "INSERT INTO promociones 
                (nombre, archivo)
                VALUES ('$nombre', '$nombreEncriptado')";
        $con->query($sql);
    } else {
        echo "Error al subir el archivo.";
        exit();
    }
} else {
    // Insertar sin foto
    $sql = "INSERT INTO promociones 
            (nombre )
            VALUES ('$nombre')";
    $con->query($sql);
}

// Redirigir a la lista de empleados
header("Location: promociones_lista.php");
exit();
?>

