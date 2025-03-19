<?php
require "funciones/conecta.php";
$con = conecta();

session_start();
if (!isset($_SESSION['idUser'])) {
    header("Location: index.php");
    exit();
}

$id_producto = $_REQUEST['id'];

$sql = "SELECT nombre, codigo, descripcion, costo, stock, archivo_n, archivo FROM productos WHERE id = $id_producto AND eliminado=0";
$res = $con->query($sql);
$row = $res->fetch_array();

if (!$row) {
    echo "Producto no encontrado";
    exit;
}

$nombre = $row['nombre'];
$codigo = $row['codigo'];
$descripcion = $row['descripcion'];
$costo = $row['costo'];
$stock = $row['stock'];
$archivo = $row['archivo']; 
$archivo_n = $row['archivo_n'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Producto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f4f4f4;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        td, th {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
            text-align: center;
        }
        img {
            width: 150px;
            height: 150px;
            border-radius: 5px;
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
        <h1>Detalle del Producto</h1>
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
        <a href="productos_lista.php" class="registro">Volver a la lista</a>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Código</th>
                <th>Descripción</th>
                <th>Costo</th>
                <th>Stock</th>
                <th>Foto</th>
            </tr>
            <tr>
                <td><?php echo $nombre; ?></td>
                <td><?php echo $codigo; ?></td>
                <td><?php echo $descripcion; ?></td>
                <td><?php echo $costo; ?></td>
                <td><?php echo $stock; ?></td>
                <td>
                    <?php if ($archivo): ?>
                        <img src="fotos_productos/<?php echo $archivo; ?>" alt="<?php echo $archivo_n; ?>">
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
