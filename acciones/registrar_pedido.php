<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tienda";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos del formulario
$cliente_nombre = $_POST['cliente_nombre'];
$cliente_email = $_POST['cliente_email'];
$producto = $_POST['producto'];
$cantidad = (int)$_POST['cantidad'];
$metodo_pago = $_POST['metodo_pago']; // Nuevo campo para el método de pago

// Insertar el pedido en la base de datos
$sql = "INSERT INTO pedidos (cliente_nombre, cliente_email, producto, cantidad, metodo_pago)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssis", $cliente_nombre, $cliente_email, $producto, $cantidad, $metodo_pago);

if ($stmt->execute()) {
    echo "Pedido registrado con éxito.";
} else {
    echo "Error al registrar el pedido: " . $stmt->error;
}

// Enviar correo al administrador
$admin_email = "admin@tienda.com";  // Reemplaza con el correo del administrador
$asunto = "Nuevo Pedido Recibido";
$cuerpo = "Nuevo pedido registrado:
\n\nCliente: $cliente_nombre
\nEmail: $cliente_email
\nProducto: $producto
\nCantidad: $cantidad
\nMétodo de Pago: $metodo_pago";

$headers = "From: josemanuelrubiopi@gmail.com";

if (mail($admin_email, $asunto, $cuerpo, $headers)) {
    echo "Correo de notificación enviado al administrador.";
} else {
    echo "Error al enviar el correo de notificación.";
}

// Enviar correo al cliente
$asunto_cliente = "Confirmación de Pedido";
$cuerpo_cliente = "Gracias por tu pedido, $cliente_nombre:
\n\nProducto: $producto
\nCantidad: $cantidad
\nMétodo de Pago: $metodo_pago
\nTu pedido está en proceso y te notificaremos cuando se haya enviado.";

$headers_cliente = "From: jomarubi@gmail.com";

if (mail($cliente_email, $asunto_cliente, $cuerpo_cliente, $headers_cliente)) {
    echo "Correo de confirmación enviado al cliente.";
} else {
    echo "Error al enviar el correo de confirmación.";
}

$stmt->close();
$conn->close();
?>
