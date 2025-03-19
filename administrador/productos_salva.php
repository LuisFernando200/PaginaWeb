<?php
require "funciones/conecta.php";
$con = conecta();

$nombre = $_REQUEST['nombre'];
$codigo = $_REQUEST['codigo'];
$descripcion = $_REQUEST['descripcion'];
$costo = $_REQUEST['costo'];
$stock = $_REQUEST['stock'];
$archivo_n = '';
$archivo = '';

if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $archivo = $_FILES['archivo'];
    $archivo_n = basename($archivo['name']); // Nombre original

    // Validar la extensión del archivo
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
    $extensionArchivo = strtolower(pathinfo($archivo_n, PATHINFO_EXTENSION));

    if (!in_array($extensionArchivo, $extensionesPermitidas)) {
        echo "Tipo de archivo no permitido. Solo se permiten imágenes JPG, PNG y GIF.";
        exit();
    }

    // Nombre encriptado
    $nombreEncriptado = md5(uniqid(rand(), true)) . "." . $extensionArchivo;

    // Carpeta destino
    $carpetaDestino = 'fotos_productos/';
    $rutaArchivo = $carpetaDestino . $nombreEncriptado;

    // Mover el archivo
    if (move_uploaded_file($archivo['tmp_name'], $rutaArchivo)) {
        // Guardar en la base de datos
        $sql = "INSERT INTO productos 
                (nombre, codigo, descripcion, costo, stock, archivo_n, archivo)
                VALUES ('$nombre', '$codigo', '$descripcion', '$costo', $stock, '$archivo_n', '$nombreEncriptado')";
        $con->query($sql);
    } else {
        echo "Error al subir el archivo.";
        exit();
    }
} else {
    // Insertar sin foto
    $sql = "INSERT INTO productos 
            (nombre, codigo, descripcion, costo, stock, archivo_n, archivo)
            VALUES ('$nombre', '$codigo', '$descripcion', '$costo', $stock, '', '')";
    $con->query($sql);
}

// Redirigir a la lista de empleados
header("Location: productos_lista.php");
exit();
?>

