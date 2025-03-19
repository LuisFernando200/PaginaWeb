<?php
require "funciones/conecta.php";
$con = conecta();
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['idUser'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No se ha encontrado el usuario.']);
    exit;
}

$id_cliente = $_SESSION['idUser'];

// Validar los parámetros recibidos
if (!isset($_POST['id'], $_POST['cantidad'], $_POST['costo']) || !is_numeric($_POST['id']) || !is_numeric($_POST['cantidad']) || !is_numeric($_POST['costo'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Parámetros inválidos.']);
    exit;
}

$id_producto = (int) $_POST['id'];
$cantidad = (int) $_POST['cantidad'];
$costo = (float) $_POST['costo'];
$fecha_actual = date('Y-m-d H:i:s');

// Verificar si existe un pedido abierto para el cliente
$sql = "SELECT id FROM pedidos WHERE id_cliente = ? AND status = 0";
$stmt_verificar = $con->prepare($sql);
$stmt_verificar->bind_param("i", $id_cliente);
$stmt_verificar->execute();
$res_verificar = $stmt_verificar->get_result();

if (!$res_verificar) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error al verificar el pedido: ' . $con->error]);
    exit;
}

if ($res_verificar->num_rows > 0) {
    // Si ya existe un pedido abierto, usar su ID
    $row = $res_verificar->fetch_assoc();
    $id_pedido = $row['id'];
} else {
    // Crear un nuevo pedido si no existe uno abierto
    $sql_insert_pedido = "INSERT INTO pedidos (fecha, id_cliente, status) VALUES (?, ?, 0)";
    $stmt_insert_pedido = $con->prepare($sql_insert_pedido);
    $stmt_insert_pedido->bind_param("si", $fecha_actual, $id_cliente);
    $stmt_insert_pedido->execute();
    
    if ($stmt_insert_pedido->affected_rows > 0) {
        $id_pedido = $con->insert_id; // Obtener el ID del nuevo pedido
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error al crear el pedido: ' . $con->error]);
        exit;
    }
}

// Insertar el producto en la tabla pedidos_productos
$sql_insert_producto = "INSERT INTO pedidos_productos (id_pedido, id_producto, cantidad, costo) VALUES (?, ?, ?, ?)";
$stmt_insert_producto = $con->prepare($sql_insert_producto);
$stmt_insert_producto->bind_param("iiid", $id_pedido, $id_producto, $cantidad, $costo);
$stmt_insert_producto->execute();

if ($stmt_insert_producto->affected_rows > 0) {
    $response = ['success' => true, 'message' => 'Producto agregado al pedido con éxito.'];
} else {
    $response = ['success' => false, 'message' => 'Error al agregar el producto al pedido: ' . $con->error];
}

// Enviar la respuesta JSON
header('Content-Type: application/json');
echo json_encode($response);

// Cerrar conexiones
$stmt_verificar->close();
if (isset($stmt_insert_pedido)) $stmt_insert_pedido->close();
$stmt_insert_producto->close();
$con->close();
?>
