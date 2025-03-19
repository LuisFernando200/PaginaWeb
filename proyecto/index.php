<?php

require "funciones/conecta.php";
$con = conecta();

// Verificar si el usuario está autenticado
$autenticado = isset($_SESSION['idUser']);
$nombre = $autenticado ? $_SESSION['nombreUser'] : '';
$id = $autenticado ? $_SESSION['idUser'] : 0;

$sql_promocion = "SELECT archivo FROM promociones WHERE eliminado = 0 ORDER BY RAND() LIMIT 3";
$res_promocion = $con->query($sql_promocion);
$promociones = $res_promocion->fetch_assoc();

// Si no hay promociones, asigna un valor por defecto
$archivo_promocion = $promociones ? $promociones['archivo'] : 'default.jpg';

// Seleccionar 6 productos aleatorios
$sql = "SELECT id, nombre, descripcion, costo, stock, archivo FROM productos WHERE eliminado = 0 ORDER BY RAND() LIMIT 6";
$res = $con->query($sql);
$productos = $res->fetch_all(MYSQLI_ASSOC);

if (empty($productos)) {
    echo "No se encontraron productos.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        header {
            background-color: #2C6B2F;
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
        .banner {
            text-align: center;
            margin-top: 20px;
        }
        .banner img {
            width: 600px;
            height: 200px;
            object-fit: cover;
        }
        .container {
            width: 90%;
            margin: auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .card img {
            width: auto;
            height: 150px;
            max-width: 100%;
            border-radius: 5px;
        }
        .card h3 {
            margin: 10px 0;
            font-size: 18px;
            color: #333;
        }
        .card p {
            font-size: 14px;
            color: #666;
        }
        .card span {
            font-weight: bold;
            font-size: 16px;
            color: #4CAF50;
        }
        .card button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
        }
        .card button:hover {
            background-color: #45a049;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
            margin-top: 20px;
        }
        .banner {
            text-align: center;
            margin-top: 20px;
            width: 100%; /* Ocupa todo el ancho */
            height: 300px; /* Ajusta según tu necesidad */
            overflow: hidden;
            position: relative;
            background-color: #f0f0f0; /* Fondo opcional */
}       

        .banner img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background-color: #fff;
            display: block;
        }
        .logo {
            position: absolute;
            top: 10px; /* Separación superior */
            left: 10px; /* Separación izquierda */
        }

        .logo img {
            height: 90px; /* Tamaño del logo */
            width: auto; /* Mantener proporciones */
        }
        #carrito {
        position: fixed; /* Fija el div en una posición de la ventana */
        top: 15px; /* Separación desde la parte superior */
        right: 15px; /* Separación desde la derecha */
        background-color: #4CAF50; /* Color de fondo */
    }
    </style>
</head>
<body>
<header>
<?php if (isset($_SESSION['idUser'])): ?>
    <h1>Bienvenido a nuestra Tienda</h1>
    <?php endif; ?>
</header>
<div id="carrito" onclick="verCarrito()">
<?php if (isset($_SESSION['idUser'])): ?>
    <form action="carrito.php" method="POST" style="display:inline;">
<button type="submit" style="background: none; border: none; cursor: pointer;">
        🛒 Carrito
    </button>
</form>
<?php endif; ?>
    </div>
<div class="logo">
        <img src="logo/logo1.png" alt="Logo de la Empresa">
    </div>
<nav>
<?php if (!isset($_SESSION['idUser'])): ?>
    <a href="login1.php">Iniciar Sesion</a>
    <?php endif; ?>
    <a href="index.php">Home</a>
    <a href="proyecto_productos.php">Productos</a>
    <a href="contacto.php">Contacto</a>
    <?php if (isset($_SESSION['idUser'])): ?>
            <!-- Mostrar solo si la sesión está iniciada -->
            <a href="salir.php">Salir</a>
        <?php endif; ?>
</nav>

<div class="banner">
    <img src="../administrador/fotos_promociones/<?php echo htmlspecialchars($archivo_promocion, ENT_QUOTES, 'UTF-8'); ?>" alt="Promoción">
</div>

<div class="container">
    <?php foreach ($productos as $producto): ?>
        <div class="card">
        <form action="proyecto_ver.php" method="POST" style="display:inline;">
                <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
                    <img src="../administrador/fotos_productos/<?php echo htmlspecialchars($producto['archivo'], ENT_QUOTES, 'UTF-8'); ?>" alt="Producto">
                </button>
            </form>
            <h3><?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <p><?php echo htmlspecialchars($producto['descripcion'], ENT_QUOTES, 'UTF-8'); ?></p>
            <span>$<?php echo number_format($producto['costo'], 2); ?></span>
            <br>
            </a>
            </form>
                <input type="number" id="cantidad_<?php echo $producto['id']; ?>" value="1" min="1" max="<?php echo $producto['stock']; ?>">
             <button onclick="insertarCarro(<?php echo $producto['id']; ?>, document.getElementById('cantidad_<?php echo $producto['id']; ?>').value, <?php echo $producto['costo']; ?>, <?php echo $producto['stock']; ?>);">
                Comprar
                </button>
<script>
    // Esta función se llama cuando el valor de la cantidad cambia
    function actualizarCantidad(productId) {
        var cantidad = document.getElementById('cantidad_' + productId).value;
        document.getElementById('cantidad_' + productId).value = cantidad;
    }
</script>
            
        </div>
    <?php endforeach; ?>
</div>

<footer>
    <p>&copy; 2024 Tienda Online. Todos los derechos reservados.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
           function insertarCarro(idProducto, cantidad, costo, stock) {
            cantidad = parseInt(cantidad);
            stock = parseInt(stock);

            if (!<?php echo json_encode($autenticado); ?>) {
                alert("Debes iniciar sesión para realizar esta acción.");
                window.location.href = 'login1.php';
                return;
            }

            if (cantidad <= 0 || cantidad > stock) {
                alert("Por favor, selecciona una cantidad válida dentro del stock disponible.");
                return;
            }

            $.ajax({
                url: 'insertarProductos.php',
                type: 'POST',
                dataType: 'JSON',
                data: { 
                    id: idProducto, 
                    cantidad: cantidad,
                    costo: costo
                },
                success: function(res) {
                    if (res.success) {
                        alert(res.message);
                    } else {
                        alert('Error: ' + res.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if (jqXHR.responseJSON) {
                        alert('Error: ' + jqXHR.responseJSON.message);
                    } else {
                        alert('Error: ' + textStatus + ' - ' + errorThrown);
                    }
                }
            });
    }
</script>
</body>
</html>
