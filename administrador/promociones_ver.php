<?php
require "funciones/conecta.php";
$con = conecta();

session_start();
if (!isset($_SESSION['idUser'])) {
    header("Location: index.php");
    exit();
}

// Validar que el ID se reciba correctamente por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_promocion = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id_promocion <= 0) {
        echo "ID de promoción inválido.";
        exit();
    }

    // Consulta para obtener los datos de la promoción con el ID proporcionado
    $sql = "SELECT id, nombre, archivo, eliminado FROM promociones WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $id_promocion);
    $stmt->execute();
    $result = $stmt->get_result();
    $promocion = $result->fetch_assoc();

    if (!$promocion) {
        echo "Promoción no encontrada.";
        exit();
    }
} else {
    echo "Acceso no permitido.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Promoción</title>
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

        /* Button */
        .btn-regresar {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            width: 150px;
        }
        .btn-regresar:hover {
            background-color: #45a049;
        }

        /* Image */
        img {
            width: 150px;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header>Detalle de Promoción</header>

    <nav>
        <a href="bienvenido.php">Inicio</a>
        <a href="empleados_lista.php">Empleados</a>
        <a href="productos_lista.php">Productos</a>
        <a href="promociones_lista.php">Promociones</a>
        <a href="pedidos_lista.php">Pedidos</a>
        <a href="salir.php">Cerrar sesión</a>
    </nav>

    <div class="container">
        <h1 style="text-align: center;">Promoción ID: <?php echo htmlspecialchars($promocion['id'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Archivo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $promocion['id']; ?></td>
                    <td><?php echo htmlspecialchars($promocion['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <?php if ($promocion['archivo']): ?>
                            <img src="fotos_promociones/<?php echo htmlspecialchars($promocion['archivo'], ENT_QUOTES, 'UTF-8'); ?>" 
                                 alt="<?php echo htmlspecialchars($promocion['nombre'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php else: ?>
                            No disponible
                        <?php endif; ?>
                    </td>
                    <td><?php echo $promocion['eliminado'] == 0 ? "Activo" : "Eliminado"; ?></td>
                </tr>
            </tbody>
        </table>
        <a href="promociones_lista.php" class="btn-regresar">Regresar</a>
    </div>
</body>
</html>
