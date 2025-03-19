<?php
session_start();
$nombre = $_SESSION['nombreUser'];
$id = $_SESSION['idUser'];

if (!isset($_SESSION['idUser'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Alta de Promociones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        .container {
            width: 100%;
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

        input, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color: #45a049;
        }

        #mensaje {
            color: red;
            text-align: center;
            display: none;
            margin-top: 10px;
            background: #fbe4e4;
            border: 1px solid #f5c2c2;
            padding: 10px;
            border-radius: 5px;
        }

        a {
            text-decoration: none;
            color: #4CAF50;
            display: inline-block;
            text-align: center;
        }

        a:hover {
            text-decoration: underline;
        }

        .table-nav {
            width: 100%;
            background: #457845;
            color: white;
            text-align: center;
        }

        .table-nav td {
            padding: 10px;
        }

        .table-nav a {
            color: white;
            font-size: 18px;
        }
    </style>
    <script src="jquery-3.3.1.min.js"></script>
    <script>
        function miAlerta(form) {
            var nombre = form.nombre.value;
            var archivo = form.archivo.value;

            var mensajeDiv = document.getElementById('mensaje');
            mensajeDiv.style.display = "none";

            if (nombre === "") {
                mensajeDiv.innerHTML = "Faltan campos por llenar";
                mensajeDiv.style.display = "block";
                setTimeout(function() {
                    mensajeDiv.style.display = "none";
                }, 5000);
                return false; // Evita el envío del formulario
            }

            form.method = 'post';
            form.action = 'promociones_salva.php';
            form.submit();
        }
    </script>
</head>
<body>
    <table class="table-nav">
        <tr>
            <td><a href="bienvenido.php">Inicio</a></td>
            <td><a href="empleados_lista.php">Empleados</a></td>
            <td><a href="productos_lista.php">Productos</a></td>
            <td><a href="promociones_lista.php">Promociones</a></td>
            <td><a href="bienvenido.php">Pedidos</a></td>
            <td><a href="salir.php">Cerrar sesión</a></td>
        </tr>
    </table>

    <div class="container">
        <h2>Alta de Promociones</h2>
        <form name="form01" method="post" enctype="multipart/form-data" onsubmit="return miAlerta(this);">
            <label for="nombre">Nombre de la promoción:</label>
            <input type="text" name="nombre" placeholder="Escribe el nombre de la promoción" id="nombre" />

            <label for="archivo">Imagen de la promoción:</label>
            <input type="file" id="archivo" name="archivo" accept="image/*" />

            <button type="submit">Enviar</button>
        </form>

        <div id='mensaje'></div>
    </div>
</body>
</html>
