<?php
session_start();
require "funciones/conecta.php";
$con = conecta();
$autenticado = isset($_SESSION['idUser']);
// Verificar si el usuario está autenticado
if (!isset($_SESSION['idUser'])) {
    header("Location: login1.php");
    exit;
}

$id_cliente = $_SESSION['idUser']; // ID del cliente desde la sesión

// Obtener el ID del pedido abierto
$sql_pedido = "SELECT id FROM pedidos WHERE id_cliente = ? AND status = 0";
$stmt_pedido = $con->prepare($sql_pedido);
$stmt_pedido->bind_param("i", $id_cliente);
$stmt_pedido->execute();
$res_pedido = $stmt_pedido->get_result();

if ($res_pedido->num_rows > 0) {
    $row = $res_pedido->fetch_assoc();
    $id_pedido = $row['id']; // Obtener el ID del pedido abierto
} else {
    $id_pedido = null; // Si no hay pedido abierto
}

$stmt_pedido->close();

// Verificar si el pedido existe
if ($id_pedido !== null) {
    // Obtener todos los productos en el carrito del cliente (tabla pedidos_productos), agrupados por id_producto
    $sql = "SELECT pp.id_producto, p.nombre, SUM(pp.cantidad) AS cantidad, p.costo, (SUM(pp.cantidad) * p.costo) AS subtotal
            FROM pedidos_productos pp
            JOIN productos p ON pp.id_producto = p.id
            WHERE pp.id_pedido = ?
            GROUP BY pp.id_producto";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_pedido);
    $stmt->execute();
    $res = $stmt->get_result();

    $productos = [];
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $productos[] = $row;
        }
    }

    // Cerrar la conexión
    $stmt->close();
} else {
    $productos = []; // Si no hay un pedido abierto, no hay productos en el carrito
}

$con->close();


// Revertir la confirmación del pedido (cambiar el estatus)
if (isset($_POST['regresar'])) {
    $confirmado = false; // Revertir la confirmación

    // Cambiar el estado del pedido en la base de datos a pendiente (status = 0)
    $con = conecta();
    $sql_regresar = "UPDATE pedidos SET status = 0 WHERE id = ?";
    $stmt_regresar = $con->prepare($sql_regresar);
    $stmt_regresar->bind_param("i", $id_pedido);
    $stmt_regresar->execute();
    $stmt_regresar->close();
   
    
    // Al regresar, recargar los productos en el carrito, ya que no se eliminaron
    // Obtener todos los productos en el carrito del cliente (tabla pedidos_productos), agrupados por id_producto
    $sql = "SELECT pp.id_producto, p.nombre, SUM(pp.cantidad) AS cantidad, p.costo, (SUM(pp.cantidad) * p.costo) AS subtotal
            FROM pedidos_productos pp
            JOIN productos p ON pp.id_producto = p.id
            WHERE pp.id_pedido = ?
            GROUP BY pp.id_producto";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_pedido);
    $stmt->execute();
    $res = $stmt->get_result();

    $productos = [];
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $productos[] = $row;
        }
    }


}



// Confirmar pedido (cambiar el estatus)
$confirmado = false;
if (isset($_POST['confirmar'])) {
    $confirmado = true;
    // Actualizar el estado del pedido en la base de datos como confirmado (status = 1)
    $con = conecta();
    $sql_confirmar = "UPDATE pedidos SET status = 0 WHERE id = ?";
    $stmt_confirmar = $con->prepare($sql_confirmar);
    $stmt_confirmar->bind_param("i", $id_pedido);
    $stmt_confirmar->execute();
    $stmt_confirmar->close();
    $con->close();
}

// Finalizar pedido (cambiar el estatus)
if (isset($_POST['finalizar'])) {
    $con = conecta(); // Reabrimos la conexión
    $sql_finalizar = "UPDATE pedidos SET status = 1 WHERE id = ?";
    $stmt_finalizar = $con->prepare($sql_finalizar);
    $stmt_finalizar->bind_param("i", $id_pedido);
    $stmt_finalizar->execute();
    $stmt_finalizar->close();
    $con->close();
    // Redirigir al usuario después de finalizar el pedido
    header("Location: carrito.php?finalizado=true");
    exit;
}

// Actualizar la cantidad de un producto (AJAX)
if (isset($_POST['actualizar_cantidad_ajax'])) {
    $id_producto = $_POST['id_producto'];
    $nueva_cantidad = $_POST['cantidad'];

    // Validar que la cantidad sea un número positivo
    if ($nueva_cantidad > 0) {
        $con = conecta();
        $sql_actualizar = "UPDATE pedidos_productos SET cantidad = ? WHERE id_pedido = ? AND id_producto = ?";
        $stmt_actualizar = $con->prepare($sql_actualizar);
        $stmt_actualizar->bind_param("iii", $nueva_cantidad, $id_pedido, $id_producto);
        $stmt_actualizar->execute();
        $stmt_actualizar->close();

        // Obtener el subtotal actualizado para este producto
        $sql_subtotal = "SELECT (cantidad * costo) AS subtotal FROM pedidos_productos WHERE id_pedido = ? AND id_producto = ?";
        $stmt_subtotal = $con->prepare($sql_subtotal);
        $stmt_subtotal->bind_param("ii", $id_pedido, $id_producto);
        $stmt_subtotal->execute();
        $res_subtotal = $stmt_subtotal->get_result();
        $row_subtotal = $res_subtotal->fetch_assoc();
        $nuevo_subtotal = $row_subtotal['subtotal'];

        // Calcular el total actualizado
        $sql_total = "SELECT SUM(cantidad * costo) AS total FROM pedidos_productos WHERE id_pedido = ?";
        $stmt_total = $con->prepare($sql_total);
        $stmt_total->bind_param("i", $id_pedido);
        $stmt_total->execute();
        $res_total = $stmt_total->get_result();
        $row_total = $res_total->fetch_assoc();
        $total = $row_total['total'];

        $stmt_subtotal->close();
        $stmt_total->close();
        $con->close();

        // Responder con el nuevo subtotal y el total actualizado
        $response = [
            'success' => true,
            'id_producto' => $id_producto,
            'nuevo_subtotal' => number_format($nuevo_subtotal, 2),
            'total' => number_format($total, 2)
        ];
        echo json_encode($response);
        exit;
    } else {
        $response = ['success' => false];
        echo json_encode($response);
        exit;
    }
}

// Eliminar producto (AJAX)
if (isset($_POST['eliminar_producto_ajax'])) {
    $id_producto = $_POST['id_producto'];

    // Eliminar el producto del carrito
    $con = conecta();
    $sql_eliminar = "DELETE FROM pedidos_productos WHERE id_pedido = ? AND id_producto = ?";
    $stmt_eliminar = $con->prepare($sql_eliminar);
    $stmt_eliminar->bind_param("ii", $id_pedido, $id_producto);
    $stmt_eliminar->execute();
    $stmt_eliminar->close();

    // Calcular el total actualizado
    $sql_total = "SELECT SUM(cantidad * costo) AS total FROM pedidos_productos WHERE id_pedido = ?";
    $stmt_total = $con->prepare($sql_total);
    $stmt_total->bind_param("i", $id_pedido);
    $stmt_total->execute();
    $res_total = $stmt_total->get_result();
    $row_total = $res_total->fetch_assoc();
    $total = $row_total['total'];

    $stmt_total->close();
    $con->close();

    // Responder con el nuevo total
    $response = [
        'success' => true,
        'total' => number_format($total, 2)
    ];
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
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
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
        }
        td, th {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .registro, .finalizar, .regresar {
            display: block;
            text-align: center;
            margin: 20px auto;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .registro:hover, .finalizar:hover, .regresar:hover {
            background-color: #45a049;
        }
        .input-cantidad {
            width: 50px;
            text-align: center;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<header>
    <h1>Carrito de Compras</h1>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="proyecto_productos.php">Productos</a>
    <a href="contacto.php">Contacto</a>
    <a href="Salir.php">Salir</a>
</nav>

<div class="container">
    <h2>Tu Carrito</h2>
    <table>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Subtotal</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($productos as $producto): ?>
        <tr data-id_producto="<?php echo $producto['id_producto']; ?>">
            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
            <td>
                <input class="input-cantidad" type="number" value="<?php echo $producto['cantidad']; ?>" min="1" data-id_producto="<?php echo $producto['id_producto']; ?>" <?php echo $confirmado ? 'disabled' : ''; ?>>
            </td>
            <td><?php echo number_format($producto['costo'], 2); ?></td>
            <td class="subtotal"><?php echo number_format($producto['subtotal'], 2); ?></td>
            <td>
                <?php if (!$confirmado): ?>
                <button class="eliminar" data-id_producto="<?php echo $producto['id_producto']; ?>">Eliminar</button>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div>
        <?php if (!$confirmado): ?>
        <form action="carrito.php" method="post">
            <button type="submit" name="confirmar" class="confirmar">Confirmar Pedido</button>
        </form>
        <?php endif; ?>

        <?php if ($confirmado): ?>
        <form action="carrito.php" method="post">
            <button type="submit" name="finalizar" class="finalizar">Finalizar Pedido</button>
        </form>
        <form action="carrito.php" method="post">
            <button type="submit" name="regresar" class="regresar">Regresar a Confirmación</button>
        </form>
        <?php endif; ?>
    </div>

    <div class="total">
        <strong>Total: $<span id="total"><?php echo number_format(array_sum(array_column($productos, 'subtotal')), 2); ?></span></strong>
    </div>
</div>





<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).on('change', '.input-cantidad', function() {
        var id_producto = $(this).data('id_producto');
        var cantidad = $(this).val();
        
        $.post('carrito.php', {
            actualizar_cantidad_ajax: true,
            id_producto: id_producto,
            cantidad: cantidad
        }, function(response) {
            if (response.success) {
                $('tr[data-id_producto="' + id_producto + '"] .subtotal').text(response.nuevo_subtotal);
                $('#total').text(response.total);
            } else {
                alert("Error al actualizar la cantidad.");
            }
        }, 'json');
    });

    $(document).on('click', '.eliminar', function() {
    var id_producto = $(this).data('id_producto');
    // Mostrar confirmación
    if (confirm("¿Estás seguro de que deseas eliminar este producto?")) {
        // Si el usuario confirma, realiza la petición AJAX
        $.post('carrito.php', {
            eliminar_producto_ajax: true,
            id_producto: id_producto
        }, function(response) {
            if (response.success) {
                // Si la eliminación es exitosa, elimina la fila correspondiente
                $('tr[data-id_producto="' + id_producto + '"]').remove();
                // Actualizar el total
                $('#total').text(response.total);
            } else {
                // Si hubo un error, mostrar un mensaje
                alert("Error al eliminar el producto.");
            }
        }, 'json');
    } // El bloque `if` cierra aquí
});

</script>

</body>
</html>