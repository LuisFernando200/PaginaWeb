<?php
require "funciones/conecta.php";
$con = conecta();
session_start();
$nombre = $_SESSION['nombreUser'];
$id = $_SESSION['idUser'];

if (!isset($_SESSION['idUser'])) {
    header("Location: index.php");
    exit(); 
}

$id_producto = $_REQUEST['id'];
$sql = "SELECT * FROM productos WHERE id = ? AND eliminado = 0";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $id_producto);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();

if (!$producto) {
    echo "Producto no encontrado";
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 15px;
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
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        #mensaje {
            width: 100%;
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            text-align: center;
            display: none;
            margin-bottom: 20px;
        }
        .registro {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
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
    <script src="jquery-3.3.1.min.js"></script>
</head>
<body>
    <header>
        <h1>Editar Producto</h1>
    </header>
    <nav>
        <a href="bienvenido.php">Inicio</a>
        <a href="empleados_lista.php">Empleados</a>
        <a href="productos_lista.php">Productos</a>
        <a href="promociones_lista.php">Promociones</a>
        <a href="pedidos_lista.php">Pedidos</a>
        <a href="perfil.php"><?php echo $nombre; ?></a>
        <a href="salir.php">Cerrar sesión</a>
    </nav>
    <div class="container">
        <form id="editarProductoForm" enctype="multipart/form-data">
            <input type="hidden" id="id" name="id" value="<?php echo $producto['id']; ?>">

            <div id="mensaje"></div>

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo $producto['nombre']; ?>">

            <label for="codigo">Código:</label>
            <input type="text" id="codigo" name="codigo" value="<?php echo $producto['codigo']; ?>">

            <label for="descripcion">Descripción:</label>
            <input type="text" id="descripcion" name="descripcion" value="<?php echo $producto['descripcion']; ?>">

            <label for="costo">Costo:</label>
            <input type="text" id="costo" name="costo" value="<?php echo $producto['costo']; ?>">

            <label for="stock">Stock:</label>
            <input type="text" id="stock" name="stock" value="<?php echo $producto['stock']; ?>">

            <label for="foto">Subir Foto:</label>
            <input type="file" id="foto" name="foto" accept="image/*">

            <button type="button" id="saveButton">Guardar</button>
        </form>
        <a href="productos_lista.php" class="registro">Regresar</a>
    </div>
    <footer>
        <p>&copy; 2024 Tienda Online. Todos los derechos reservados.</p>
    </footer>

    <script>
        $('#saveButton').on('click', function () {
            const formData = new FormData($('#editarProductoForm')[0]);
            const id = $('#id').val();
            const currentCodigo = '<?php echo $producto["codigo"]; ?>';

            $('#mensaje').hide();

            if (!formData.get('nombre') || !formData.get('codigo') || !formData.get('descripcion') || !formData.get('costo') || !formData.get('stock')) {
                $('#mensaje').text('Faltan campos por llenar').show();
                return;
            }

            if (isNaN(formData.get('costo')) || isNaN(formData.get('stock'))) {
                $('#mensaje').text('Costo y Stock deben ser numéricos').show();
                return;
            }

            if (formData.get('codigo') === currentCodigo) {
                enviarFormulario(formData);
            } else {
                $.ajax({
                    url: 'validar_codigo.php',
                    type: 'POST',
                    data: { codigo: formData.get('codigo') },
                    dataType: 'json',
                    success: function (response) {
                        if (response.existe) {
                            $('#mensaje').text(`El código ${formData.get('codigo')} ya existe.`).show();
                        } else {
                            enviarFormulario(formData);
                        }
                    },
                    error: function () {
                        $('#mensaje').text('Error en la conexión').show();
                    }
                });
            }
        });

        function enviarFormulario(formData) {
            $.ajax({
                url: 'actualizarProducto.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        window.location.href = 'productos_lista.php';
                    } else {
                        $('#mensaje').text('Error al actualizar: ' + response.message).show();
                    }
                },
                error: function () {
                    $('#mensaje').text('Error en la conexión al actualizar').show();
                }
            });
        }
    </script>
</body>
</html>

