<?php
session_start();
$nombre = $_SESSION['nombreUser'];
$id = $_SESSION['idUser'];

if (!isset($_SESSION['idUser'])) {
    header("Location: index.php");
    exit();
}

require "funciones/conecta.php";
$con = conecta();

$sql = "SELECT * FROM promociones WHERE eliminado = 0";
$res = $con->query($sql);
$num = $res->num_rows;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Promociones</title>
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

        /* Buttons */
        .registro {
            display: inline-block;
            margin: 10px 0;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .registro:hover {
            background-color: #45a049;
        }
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-danger {
            background-color: #f44336;
        }
        .btn-danger:hover {
            background-color: #45a049;
        }
        .btn-warning {
            background-color: #4CAF50;
        }
        .btn-warning:hover {
            background-color: #45a049;
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
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function eliminaremAjx(id) {
            if (confirm("¿Estás seguro de que deseas eliminar esta promoción?")) {
                $.ajax({
                    url: 'promociones_elimina.php',
                    type: 'post',
                    dataType: 'json',
                    data: { id: id },
                    success: function(res) {
                        if (res.success) {
                            $('#promociones_' + id).remove();
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
    <header>Lista de Promociones</header>

    <nav>
        <a href="bienvenido.php">Inicio</a>
        <a href="empleados_lista.php">Empleados</a>
        <a href="productos_lista.php">Productos</a>
        <a href="promociones_lista.php">Promociones</a>
        <a href="pedidos_lista.php">Pedidos</a>
        <a href="salir.php">Cerrar sesión</a>
    </nav>

    <div class="container">
        <a href="promociones_alta.php" class="registro">Crear nuevo registro</a>
        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Nombre</th>
                    <th>Eliminar</th>
                    <th>Ver detalle</th>
                    <th>Editar</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $res->fetch_array()): ?>
                    <tr id="promociones_<?php echo $row['id']; ?>">
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nombre']; ?></td>
                        <td>
                            <button onclick="eliminaremAjx(<?php echo $row['id']; ?>)" class="btn btn-danger">Eliminar</button>
                        </td>
                        <td>
                            <form action="promociones_ver.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-warning">Ver</button>
                            </form>
                        </td>
                        <td>
                            <form action="promociones_modificar.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-warning">Modificar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
