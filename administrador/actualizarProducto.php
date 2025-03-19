<?php
require "funciones/conecta.php";
$con = conecta();

// Para depurar, imprime los datos enviados
error_log("Datos recibidos: " . print_r($_POST, true));

$id_producto = $_POST['id'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$codigo = $_POST['codigo'] ?? null;
$descripcion = $_POST['descripcion'] ?? null;
$costo = $_POST['costo'] ?? null;
$stock = $_POST['stock'] ?? null;

// Verificar que se reciben todos los datos necesarios
if ($id_producto === null || $nombre === null || $codigo === null || $descripcion === null || $costo === null || $stock === null) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos.']);
    exit;
}

// Actualizar los datos del producto (sin la foto por ahora)
$sql = "UPDATE productos SET nombre = ?, codigo = ?, descripcion = ?, costo = ?, stock = ? WHERE id = ?";
$stmt = $con->prepare($sql);

if (!$stmt) {
    error_log("Error en la preparación de la consulta: " . $con->error);
    echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta.']);
    exit;
}

$stmt->bind_param('sssssi', $nombre, $codigo, $descripcion, $costo, $stock, $id_producto);

// Ejecutar la consulta para actualizar datos del producto
if (!$stmt->execute()) {
    error_log("Error al ejecutar la consulta: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el producto.']);
    exit;
}

// Manejo de la subida de la foto
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
    $nombreArchivo = $_FILES['foto']['name'];
    $nombreEncriptado = md5(time() . $nombreArchivo); // Cambia esto según tu lógica
    $rutaDestino = 'fotos_productos/' . $nombreEncriptado;

    // Mueve el archivo a la carpeta deseada
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
        // Actualiza la base de datos con el nuevo archivo
        $sql_foto = "UPDATE productos SET archivo = ?, archivo_n = ? WHERE id = ?";
        $stmt_foto = $con->prepare($sql_foto);
        
        if (!$stmt_foto) {
            error_log("Error en la preparación de la consulta de foto: " . $con->error);
            echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta de foto.']);
            exit;
        }

        $stmt_foto->bind_param('ssi', $nombreEncriptado, $nombreArchivo, $id_producto);
        
        if (!$stmt_foto->execute()) {
            error_log("Error al actualizar la foto: " . $stmt_foto->error);
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la foto.']);
            exit;
        }

        // Cerrar la declaración de la foto
        $stmt_foto->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al subir la nueva imagen.']);
        exit;
    }
}

// Cerrar la declaración principal y la conexión
$stmt->close();
$con->close();

// Responder con éxito
echo json_encode(['success' => true]);
?>
