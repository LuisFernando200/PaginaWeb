<?php
require "funciones/conecta.php";
$con = conecta();
session_start();
$nombre = $_SESSION['nombreUser'];
$id = $_SESSION['idUser'];

if (!isset($_SESSION['idUser'])) {
    header("Location: index.php");
    exit(); }

if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo "ID de empleado no recibido.";
    exit;
}

$id = intval($_POST['id']);
$sql = "SELECT * FROM empleados WHERE id = ? AND eliminado = 0";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$empleado = $result->fetch_assoc();

if (!$empleado) {
    echo "Empleado no encontrado";
    exit;
}

$rol = $empleado['rol'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empleado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        h1 {
            text-align: center;
            color: #333;
            margin: 20px 0;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        #mensaje {
            background: #fbe4e4;
            border: 1px solid #f5c2c2;
            color: #d9534f;
            text-align: center;
            padding: 10px;
            margin-top: -10px;
            margin-bottom: 15px;
            border-radius: 5px;
            display: none;
        }

        a {
            text-decoration: none;
            color: #4CAF50;
            display: inline-block;
            margin-top: 10px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
    <script src="jquery-3.3.1.min.js"></script>
</head>
<body>
    <h1>Editar Empleado</h1>
    <div class="container">
        <form id="editarEmpleadoForm" enctype="multipart/form-data">
            <input type="hidden" id="id" name="id" value="<?php echo $empleado['id']; ?>">

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo $empleado['nombre']; ?>">

            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" value="<?php echo $empleado['apellidos']; ?>">

            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" value="<?php echo $empleado['correo']; ?>">

            <label for="rol">Rol:</label>
            <select id="rol" name="rol">
                <option value="1" <?php echo ($rol == 1) ? 'selected' : ''; ?>>Administrador</option>
                <option value="2" <?php echo ($rol == 2) ? 'selected' : ''; ?>>Empleado</option>
            </select>

            <label for="pass">Contraseña:</label>
            <input type="password" id="pass" name="pass" placeholder="Dejar en blanco para no cambiar">

            <label for="foto">Subir Foto:</label>
            <input type="file" id="foto" name="foto" accept="image/*">

            <button type="button" id="saveButton">Guardar Cambios</button>
            <div id="mensaje"></div>
            <a href="empleados_lista.php">Regresar a la lista</a>
        </form>
    </div>

    <script>
        $('#saveButton').on('click', function () {
            const formData = new FormData($('#editarEmpleadoForm')[0]);
            const id = $('#id').val();
            const currentEmail = '<?php echo $empleado["correo"]; ?>'; // Correo actual

            $('#mensaje').hide();

            // Validar campos obligatorios
            if (!formData.get('nombre') || !formData.get('apellidos') || !formData.get('correo')) {
                mostrarMensaje('Todos los campos son obligatorios.');
                return;
            }

            // Validar correo
            if (formData.get('correo') !== currentEmail) {
                validarCorreo(formData);
            } else {
                enviarFormulario(formData);
            }
        });

        function validarCorreo(formData) {
            $.ajax({
                url: 'validar_correo.php',
                type: 'POST',
                data: { correo: formData.get('correo') },
                dataType: 'json',
                success: function (response) {
                    if (response.existe) {
                        mostrarMensaje(`El correo ${formData.get('correo')} ya existe.`);
                    } else {
                        enviarFormulario(formData);
                    }
                },
                error: function () {
                    mostrarMensaje('Error al validar el correo.');
                }
            });
        }

        function enviarFormulario(formData) {
            $.ajax({
                url: 'actualizarEmpleado.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        window.location.href = 'empleados_lista.php';
                    } else {
                        mostrarMensaje('Error al actualizar los datos.');
                    }
                },
                error: function () {
                    mostrarMensaje('Error en la conexión al servidor.');
                }
            });
        }

        function mostrarMensaje(mensaje) {
            $('#mensaje').text(mensaje).fadeIn();
            setTimeout(() => { $('#mensaje').fadeOut(); }, 5000);
        }
    </script>
</body>
</html>
