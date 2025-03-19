<?php
session_start();
require "funciones/conecta.php";
$con = conecta();
$autenticado = isset($_SESSION['idUser']);
$nombre = $autenticado ? $_SESSION['nombreUser'] : '';
$id = $autenticado ? $_SESSION['idUser'] : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Alta de Empleados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            text-align: center;
        }
        nav {
            background-color: #333;
            overflow: hidden;
            display: flex;
            justify-content: center;
        }
        nav a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        nav a:hover {
            background-color: #575757;
        }
        .form-container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .form-container input[type="text"],
        .form-container input[type="email"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }
        #mensaje {
            width: 100%;
            background: #EFEFEF;
            border-radius: 5px;
            color: #f00;
            font-size: 16px;
            line-height: 25px;
            text-align: center;
            margin-top: 20px;
            padding: 5px;
            display: none;
        }
        #correoMensaje {
            color: red;
            display: none;
            font-size: 14px;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
            margin-top: 20px;
        }
        .logo {
            position: absolute;
            top: 10px; /* Separación superior */
            left: 10px; /* Separación izquierda */
        }
        
        .logo img {
            height: 90px; /* Tamaño del logo */
            width: auto; /* Mantener proporciones */
        }
        #carrito {
        position: fixed; /* Fija el div en una posición de la ventana */
        top: 15px; /* Separación desde la parte superior */
        right: 15px; /* Separación desde la derecha */
        background-color: #4CAF50; /* Color de fondo */
    }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function miAlerta(form) {
            var nombre = form.nombre.value;
            var apellido = form.apellido.value;
            var correo = form.correo.value;
            var comentario = form.comentario.value;

            var mensajeDiv = document.getElementById('mensaje');
            mensajeDiv.style.display = "none";

            // Validación de campos vacíos
            if (nombre === "" || apellido === "" || correo === "" || comentario === "") {
                mensajeDiv.innerHTML = "Faltan campos por llenar";
                mensajeDiv.style.display = "block";
                setTimeout(function() {
                    mensajeDiv.style.display = "none";
                }, 5000);
                return false; // Evita el envío del formulario
            } 
            
            // Validar si el mensaje de error de correo está visible
            if ($('#correoMensaje').is(':visible')) {
                return false; // No permite el envío si el correo ya existe
            }
            
            // Si todo está bien, envía el formulario
            form.method = 'post';
            form.action = 'contacto_salva.php';
            form.submit();
        }

    </script>
</head>
<body>
<header>
<?php if (isset($_SESSION['idUser'])):?>
    <h1>Formulario de Alta de Empleados</h1>
    <?php endif; ?>
</header>

<div id="carrito" onclick="verCarrito()">
<?php if (isset($_SESSION['idUser'])):?>
    <a href="carrito.php?id=<?php ?>">
        🛒 Carrito 
</a>
<?php endif; ?>
    </div>
<div class="logo">
        <img src="logo/logo1.png" alt="Logo de la Empresa">
    </div>

<nav>
<?php if (!isset($_SESSION['idUser'])): ?>
    <a href="login1.php">Iniciar Sesion</a>
    <?php endif; ?>
    <a href="index.php">Home</a>
    <a href="proyecto_productos.php">Productos</a>
    <a href="contacto.php">Contacto</a>
    <?php if (isset($_SESSION['idUser'])): ?>
    <a href="salir.php">Salir</a>
    <?php endif; ?>
</nav>

<div class="form-container">
    <form name="froma01" method="post" enctype="multipart/form-data" onsubmit="return miAlerta(this);">

    <form name="froma01" method="post" enctype="multipart/form-data" onsubmit="return miAlerta(this);">
        <input type="text" name="nombre" placeholder="Escribe tu nombre" /> <br>
        <input type="text" name="apellido" placeholder="Escribe tus apellidos" /> <br>
        <input type="email" name="correo" placeholder="Escribe tu correo" /> <br>
        <input type="text" name="comentario" placeholder="Escribe tu comentario" /> <br>
        <input type="submit" value="Enviar" />
    </form>
    <span id="correoMensaje"></span>
    <div id="mensaje"></div>
</div>

<footer>
    <p>&copy; 2024 Tienda Online. Todos los derechos reservados.</p>
</footer>

</body>
</html>
