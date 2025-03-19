<?php
require "funciones/conecta.php";
$con = conecta();

// Para depurar, imprime los datos enviados
error_log("Datos recibidos: " . print_r($_POST, true));

$id = $_POST['id'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$apellidos = $_POST['apellidos'] ?? null;
$correo = $_POST['correo'] ?? null;
$rol = $_POST['rol'] ?? null;
$pass = $_POST['pass'] ?? null;


// Verificar que se reciben todos los datos necesarios
if ($id === null || $nombre === null || $apellidos === null || $correo === null || $rol === null) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos.']);
    exit;
}

// Encriptar la contraseña si se proporciona
if (!empty($pass)) {
    $pass = md5($pass); // Cambié a MD5
    $sql = "UPDATE empleados SET nombre = ?, apellidos = ?, correo = ?, rol = ?, pass = ? WHERE id = ?";
    $stmt = $con->prepare($sql);
    
    if (!$stmt) {
        error_log("Error en la preparación de la consulta: " . $con->error);
        echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta.']);
        exit;
    }

    $stmt->bind_param('sssssi', $nombre, $apellidos, $correo, $rol, $pass, $id);
} else {
    $sql = "UPDATE empleados SET nombre = ?, apellidos = ?, correo = ?, rol = ? WHERE id = ?";
    $stmt = $con->prepare($sql);
    
    if (!$stmt) {
        error_log("Error en la preparación de la consulta: " . $con->error);
        echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta.']);
        exit;
    }

    $stmt->bind_param('ssssi', $nombre, $apellidos, $correo, $rol, $id);
}

// Manejo de la subida de la foto
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
    $nombreArchivo = $_FILES['foto']['name'];
    $nombreEncriptado = md5(time() . $nombreArchivo); // Cambia esto según tu lógica
    $rutaDestino = 'fotos/' . $nombreEncriptado;

    // Mueve el archivo a la carpeta deseada
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
        // Actualiza la base de datos con el nuevo archivo
        $sql = "UPDATE empleados SET archivo = ?, archivo_n = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('ssi', $nombreEncriptado, $nombreArchivo, $id);
        
        if (!$stmt->execute()) {
            error_log("Error al actualizar la foto: " . $stmt->error);
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la foto.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al subir la nueva imagen.']);
        exit;
    }
}

// Ejecutar la consulta para actualizar datos del empleado
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log("Error al ejecutar la consulta: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el empleado.']);
}

$stmt->close();
$con->close();
?>
