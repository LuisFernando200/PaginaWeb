<?php
require "funciones/conecta.php";
$con = conecta();

error_log("Datos recibidos: " . print_r($_POST, true));

$id_promocion = $_POST['id'] ?? null;
$nombre = $_POST['nombre'] ?? null;

// Verificar que se reciben todos los datos necesarios
if ($id_promocion === null || $nombre === null) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos.']);
    exit;
}

// Actualizar los datos de la promoción (sin la foto por ahora)
$sql = "UPDATE promociones SET nombre = ? WHERE id = ?";
$stmt = $con->prepare($sql);

if (!$stmt) {
    error_log("Error en la preparación de la consulta: " . $con->error);
    echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta.']);
    exit;
}

$stmt->bind_param('si', $nombre, $id_promocion);

// Ejecutar la consulta para actualizar datos de la promoción
if (!$stmt->execute()) {
    error_log("Error al ejecutar la consulta: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la promoción.']);
    exit;
}

// Manejo de la subida de la foto
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
    $nombreArchivo = $_FILES['foto']['name'];
    $nombreEncriptado = md5(time() . $nombreArchivo);
    $rutaDestino = 'fotos_promociones/' . $nombreEncriptado;

    // Crear la carpeta si no existe
    if (!is_dir('fotos_promociones')) {
        mkdir('fotos_promociones', 0777, true);
    }

    // Mover el archivo a la carpeta deseada
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
        // Actualiza la base de datos con el nuevo archivo
        $sql_foto = "UPDATE promociones SET archivo = ? WHERE id = ?";
        $stmt_foto = $con->prepare($sql_foto);

        if (!$stmt_foto) {
            error_log("Error en la preparación de la consulta de foto: " . $con->error);
            echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta de foto.']);
            exit;
        }

        $stmt_foto->bind_param('si', $nombreEncriptado, $id_promocion);

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
