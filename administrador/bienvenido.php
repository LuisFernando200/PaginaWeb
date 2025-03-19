<?php
session_start();
$nombre = $_SESSION['nombreUser'];
$id = $_SESSION['idUser'];

// Verificar si el usuario está autenticado
if (!isset($_SESSION['idUser'])) {
    header("Location: index.php"); // Si no hay sesión, redirigir a index.php
    exit();
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de productos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
            display: flex;
            justify-content: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
            display: block;
        }

        nav a:hover {
            background-color: #575757;
        }

        .container {
            flex: 1; /* Esto permite que el contenido principal ocupe el espacio disponible */
            width: 90%;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <header>
        <h1>Lista de productos</h1>
    </header>
    <nav>
        <a href="bienvenido.php">Inicio</a>
        <a href="empleados_lista.php">Empleados</a>
        <a href="productos_lista.php">Productos</a>
        <a href="promociones_lista.php">Promociones</a>
        <a href="pedidos_lista.php">Pedidos</a>
        <a href="salir.php">Cerrar sesión</a>
    </nav>
    <div class="container">
     
    </div>
    <footer>
        <p>&copy; 2024 Tienda Online. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
