<?php
session_start();
$nombre = $_SESSION['nombreUser'];
$id = $_SESSION['idUser'];

if (!isset($_SESSION['idUser'])) {
    header("Location: index.php");
    exit(); }

    require "funciones/conecta.php";
    $con = conecta();

    $sql = "SELECT * FROM productos WHERE eliminado = 0";
    $res = $con->query($sql);
    $num = $res->num_rows;

    
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
        .container {
            width: 90%;
            margin: auto;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 10px 15px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            text-decoration: none;
        }
        .btn-eliminar {
            background-color: #e74c3c;
        }
        .btn-eliminar:hover {
            background-color: #c0392b;
        }
        .btn-ver, .btn-modificar {
            background-color: #4CAF50;
        }
        .btn-ver:hover, .btn-modificar:hover {
            background-color: #45a049;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function eliminarAjx(id) {
            if (confirm("¿Estás seguro de que deseas eliminar este producto?")) {
                $.ajax({
                    url: 'productos_elimina.php',
                    type: 'post',
                    dataType: 'json',
                    data: { id: id },
                    success: function(res) {
                        if (res.success) {
                            $('#producto_' + id).remove();
                            alert(res.message);
                        } else {
                            alert('Error: ' + res.message);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            }
        }
    </script>
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
    <a href="productos_alta.php" class="btn btn-ver" style="margin-bottom: 20px;">Crear nuevo registro</a>
    <table>
        <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Código</th>
                <th>Descripción</th>
                <th>Costo</th>
                <th>Stock</th>
                <th>Eliminar</th>
                <th>Ver</th>
                <th>Editar</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $res->fetch_array()): ?>
                <?php
                    $id = $row['id'];
                    $nombre = $row['nombre'];
                    $codigo = $row['codigo'];
                    $descripcion = $row['descripcion'];
                    $costo = $row['costo'];
                    $stock = $row['stock'];
                ?>
                <tr id="producto_<?php echo $id; ?>">
                    <td><?php echo $id; ?></td>
                    <td><?php echo $nombre; ?></td>
                    <td><?php echo $codigo; ?></td>
                    <td><?php echo $descripcion; ?></td>
                    <td><?php echo $costo; ?></td>
                    <td><?php echo $stock; ?></td>
                    <td><button onclick="eliminarAjx(<?php echo $id; ?>)" class="btn btn-eliminar">Eliminar</button></td>
                    <td>
                        <form action="productos_ver.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <button type="submit" class="btn btn-modificar">Ver</button>
                        </form>
                    </td>
                    <td>
                        <form action="productos_modificar.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <button type="submit" class="btn btn-modificar">Modificar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<footer>
    <p>&copy; 2024 Tienda Online. Todos los derechos reservados.</p>
</footer>
</body>
</html>
