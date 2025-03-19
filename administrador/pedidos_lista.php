<?php
require "funciones/conecta.php";
$con = conecta();

// Obtener los pedidos cerrados (status = 1)
$sql = "SELECT id, fecha, id_cliente FROM pedidos WHERE status = 1";
$res = $con->query($sql);
$pedidos = $res->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos Cerrados</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header, nav, .container {
            margin: 0 auto;
        }

        /* Header */
        header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        /* Navigation */
        nav {
            background-color: #333;
            display: flex;
            justify-content: center;
            padding: 10px 0;
        }
        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 16px;
        }
        nav a:hover {
            background-color: #575757;
        }

        /* Main Container */
        .container {
            width: 80%;
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-warning {
            background-color: #4CAF50;
        }
        .btn-warning:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <header>Lista de Pedidos Cerrados</header>

    <nav>
        <a href="bienvenido.php">Inicio</a>
        <a href="empleados_lista.php">Empleados</a>
        <a href="productos_lista.php">Productos</a>
        <a href="promociones_lista.php">Promociones</a>
        <a href="pedidos_lista.php">Pedidos</a>
        <a href="salir.php">Cerrar sesión</a>
    </nav>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Fecha</th>
                    <th>ID Cliente</th>
                    <th>Ver Detalle</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pedido['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($pedido['fecha'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($pedido['id_cliente'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <form action="pedidos_ver.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $pedido['id']; ?>">
                                <button type="submit" class="btn btn-warning">Ver</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
