<?php
require "funciones/conecta.php";
$con = conecta();

$idPedido = isset($_POST['id']) ? (int)$_POST['id'] : 0; // Obtener el ID del pedido desde la URL

if ($idPedido == 0) {
    echo "ID de pedido no válido.";
    exit;
}

// Validar que el pedido exista
$sql = "SELECT p.id, p.fecha, p.id_cliente, pp.id_producto, pr.nombre, SUM(pp.cantidad) AS cantidad_total, pp.costo
        FROM pedidos AS p
        JOIN pedidos_productos AS pp ON p.id = pp.id_pedido
        JOIN productos AS pr ON pp.id_producto = pr.id
        WHERE p.id = ?
        GROUP BY pp.id_producto, pr.nombre, pp.costo";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $idPedido); // Usar el ID del pedido para la consulta
$stmt->execute();
$res = $stmt->get_result();
$detalles = $res->fetch_all(MYSQLI_ASSOC);

if (empty($detalles)) {
    echo "El pedido no existe o no tiene productos.";
    exit;
}

// Calcular el total del pedido
$total = 0;
foreach ($detalles as $detalle) {
    $total += $detalle['cantidad_total'] * $detalle['costo'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Pedido</title>
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
            margin-top: 20px;
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

        /* Total */
        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>Detalle del Pedido</header>

    <nav>
        <a href="bienvenido.php">Inicio</a>
        <a href="empleados_lista.php">Empleados</a>
        <a href="productos_lista.php">Productos</a>
        <a href="promociones_lista.php">Promociones</a>
        <a href="pedidos_lista.php">Pedidos</a>
        <a href="salir.php">Cerrar sesión</a>
    </nav>

    <div class="container">
        <h1>Pedido #<?php echo htmlspecialchars($idPedido, ENT_QUOTES, 'UTF-8'); ?></h1>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Costo Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles as $detalle): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($detalle['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($detalle['cantidad_total'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>$<?php echo number_format($detalle['costo'], 2); ?></td>
                        <td>$<?php echo number_format($detalle['cantidad_total'] * $detalle['costo'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="total">Total del Pedido: $<?php echo number_format($total, 2); ?></div>
    </div>
</body>
</html>
