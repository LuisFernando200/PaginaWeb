<?php
require "funciones/conecta.php";
$con = conecta();

session_start();
$nombre = $_SESSION['nombreUser'];
$idUser = $_SESSION['idUser'];

if (!isset($_SESSION['idUser'])) {
    header("Location: index.php");
    exit(); 
}

// Validar que se reciba el ID del empleado por POST
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo "ID de empleado no recibido.";
    exit;
}

$id = intval($_POST['id']); // Sanitizamos el ID recibido
$sql = "SELECT nombre, apellidos, correo, rol, pass, archivo_n, archivo FROM empleados WHERE id = $id AND eliminado = 0";
$res = $con->query($sql);
$row = $res->fetch_array();

if (!$row) {
    echo "Empleado no encontrado";
    exit;
}

$nombre = $row['nombre'];
$apellidos = $row['apellidos'];
$correo = $row['correo'];
$rol = $row['rol'];
$archivo = $row['archivo'];
$archivo_n = $row['archivo_n'];
$rolTexto = ($rol == 1) ? 'Administrador' : 'Empleado';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Empleado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
            flex: 1;
            width: 80%;
            margin: 20px auto;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .registro {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .registro:hover {
            background-color: #45a049;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        td {
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        img {
            width: 100px;
            height: 100px;
            border-radius: 5px;
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
        <h1>Detalle del Empleado</h1>
    </header>
    <nav>
        <a href="bienvenido.php">Inicio</a>
        <a href="empleados_lista.php">Empleados</a>
        <a href="productos_lista.php">Productos</a>
        <a href="promociones_lista.php">Promociones</a>
        <a href="pedidos_lista.php">Pedidos</a>
        <a href="perfil.php"><?php echo $_SESSION['nombreUser']; ?></a>
        <a href="salir.php">Cerrar sesión</a>
    </nav>
    <div class="container">
        <a href="empleados_lista.php" class="registro">Volver a la lista</a>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Foto</th>
            </tr>
            <tr>
                <td><?php echo $nombre . ' ' . $apellidos; ?></td>
                <td><?php echo $correo; ?></td>
                <td><?php echo $rolTexto; ?></td>
                <td>
                    <?php if ($archivo): ?>
                        <img src="fotos/<?php echo $archivo; ?>" alt="<?php echo $archivo_n; ?>">
                    <?php else: ?>
                        <p>No disponible</p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
    <footer>
        <p>&copy; 2024 Tienda Online. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
