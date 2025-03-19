<?php
require "funciones/conecta.php";
$con = conecta();
session_start();
$nombre = $_SESSION['nombreUser'];
$id = $_SESSION['idUser'];

if (!isset($_SESSION['idUser'])) {
    header("Location: index.php");
    exit(); 
}
$id_promociones = $_REQUEST['id'];
$sql = "SELECT * FROM promociones WHERE id = ? AND eliminado = 0";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $id_promociones);
$stmt->execute();
$result = $stmt->get_result();
$promocion = $result->fetch_assoc();

if (!$promocion) {
    echo "Promocion no encontrado";
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Promoción</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .menu {
            text-align: center;
            margin-bottom: 20px;
        }

        .menu a {
            margin: 0 10px;
            text-decoration: none;
            color: #4CAF50;
            font-weight: bold;
        }

        .menu a:hover {
            text-decoration: underline;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        #mensaje {
            text-align: center;
            color: red;
            margin-top: 20px;
            display: none;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #007BFF;
            font-size: 16px;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
    <script src="jquery-3.3.1.min.js"></script>
</head>
<body>
    <h1>Editar Promoción</h1>

    <div class="menu">
        <a href="bienvenido.php">Inicio</a>
        <a href="empleados_lista.php">Empleados</a>
        <a href="producto_lista.php">Productos</a>
        <a href="promociones_lista.php">Promociones</a>
        <a href="bienvenido.php">Pedidos</a>
        <a href="bienvenido.php"><?php echo htmlspecialchars($nombre); ?></a>
        <a href="salir.php">Cerrar sesión</a>
    </div>

    <div class="container">
        <form id="editarPromocionForm" enctype="multipart/form-data">
            <input type="hidden" id="id" name="id" value="<?php echo $promocion['id']; ?>">

            <label for="nombre">Nombre de la Promoción:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($promocion['nombre'], ENT_QUOTES, 'UTF-8'); ?>">

            <label for="foto">Subir Foto:</label>
            <input type="file" id="foto" name="foto" accept="image/*">

            <button type="button" id="saveButton">Guardar Cambios</button>

            <div id="mensaje">Faltan campos por llenar</div>
        </form>

        <a href="promociones_lista.php" class="back-link">Regresar a la lista de promociones</a>
    </div>

    <script>
        // Función para enviar el formulario al servidor
        function enviarFormulario(formData) {
            $.ajax({
                url: 'actualizarPromocion.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        window.location.href = 'promociones_lista.php';
                    } else {
                        $('#mensaje').text('Error al actualizar: ' + response.message).show();
                    }
                },
                error: function () {
                    $('#mensaje').text('Error en la conexión al actualizar').show();
                }
            });
        }

        $('#saveButton').on('click', function () {
            const formData = new FormData($('#editarPromocionForm')[0]);
            const nombre = formData.get('nombre');

            $('#mensaje').hide();

            if (!nombre) {
                $('#mensaje').text('Faltan campos por llenar').show();
                return;
            }

            enviarFormulario(formData);
        });
    </script>
</body>
</html>
