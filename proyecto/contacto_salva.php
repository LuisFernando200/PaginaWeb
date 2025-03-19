<?php
require "funciones/conecta.php";
$con = conecta();

// Recoger los datos de la solicitud y quitar espacios innecesarios
$nombre = trim($_REQUEST['nombre']);
$apellido = trim($_REQUEST['apellido']);
$correo = trim($_REQUEST['correo']);
$comentario = trim($_REQUEST['comentario']);

// Validación de campos (si son obligatorios)
if (empty($nombre) || empty($apellido) || empty($correo) || empty($comentario)) {
    echo "Todos los campos son obligatorios.";
    exit(); // Detener el script si faltan campos
}

// Validar el correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo "El correo electrónico no es válido.";
    exit(); // Detener el script si el correo no es válido
}

// Saneamiento de datos para prevenir XSS
$nombre = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
$apellido = htmlspecialchars($apellido, ENT_QUOTES, 'UTF-8');
$correo = htmlspecialchars($correo, ENT_QUOTES, 'UTF-8');
$comentario = htmlspecialchars($comentario, ENT_QUOTES, 'UTF-8');

// Preparar la consulta con sentencias preparadas para evitar inyección SQL
$sql = "INSERT INTO contacto (nombre, apellido, correo, comentario) VALUES (?, ?, ?, ?)";

// Asegurarse de que la conexión sea válida
if ($con === false) {
    echo "Error de conexión a la base de datos.";
    exit();
}

if ($stmt = $con->prepare($sql)) {
    // Vincular los parámetros y ejecutarlo
    $stmt->bind_param("ssss", $nombre, $apellido, $correo, $comentario);

    if ($stmt->execute()) {
        // Después de insertar, vamos a recoger los correos y pasarlos al script Python

        // Obtener los correos de la base de datos
        $query = "SELECT correo FROM contacto";
        $result = $con->query($query);
        $correos = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $correos[] = $row['correo'];
            }
        }

        // Convertir el array de correos en una cadena separada por comas
        $correos_str = implode(",", $correos);

        // Llamar al script Python con los correos
        $command = escapeshellcmd("python correo.py \"$correos_str\"");
        $output = shell_exec($command);

        echo "Los correos han sido enviados: " . $output;

        // Redirigir a la lista de empleados o alguna página de éxito
        header("Location: index.php");
        exit();
    } else {
        // Si falla la ejecución, mostrar el error
        echo "Error al insertar los datos: " . $stmt->error;
    }
    $stmt->close();
} else {
    // Si no se puede preparar la consulta
    echo "Error al preparar la consulta: " . $con->error;
}

$con->close();
?>
