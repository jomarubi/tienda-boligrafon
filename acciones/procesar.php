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

$producto = $_POST['producto'];
$cantidad = (int)$_POST['cantidad'];
$accion = $_POST['accion'];

if ($accion == "venta") {
    $sql = "UPDATE inventario SET cantidad = cantidad - ? WHERE producto = ?";
} elseif ($accion == "reposicion") {
    $sql = "UPDATE inventario SET cantidad = cantidad + ? WHERE producto = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $cantidad, $producto);

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Resultado de Acción</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f8ff;
            color: #333;
            text-align: center;
            padding: 50px;
        }
        h1 {
            color: #2e8b57;
            font-size: 2.5em;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 30px;
            display: inline-block;
            width: 80%;
            max-width: 600px;
        }
        .result {
            font-size: 1.2em;
            margin-top: 20px;
            padding: 15px;
            background-color: #e0ffe0;
            border-radius: 5px;
            display: inline-block;
            width: 100%;
        }
        .error {
            font-size: 1.2em;
            margin-top: 20px;
            padding: 15px;
            background-color: #ffe0e0;
            border-radius: 5px;
            display: inline-block;
            width: 100%;
            color: red;
        }
        .btn {
            padding: 10px 20px;
            background-color: #2e8b57;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #3cb371;
        }
        .table-container {
            margin-top: 40px;
            text-align: left;
            display: none;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #2e8b57;
            color: white;
        }
    </style>
</head>
<body>";

if ($stmt->execute()) {
    echo "<div class='container'>
            <h1>¡Acción Realizada!</h1>
            <div class='result'>
                <p>La acción de $accion se realizó con éxito en el producto <strong>$producto</strong>.</p>
                <p><strong>Cantidad afectada:</strong> $cantidad</p>
            </div>
            <button class='btn' onclick='history.back()'>Volver</button>
        </div>";
} else {
    echo "<div class='container'>
            <h1>Error</h1>
            <div class='error'>
                <p>Error al realizar la acción: " . $stmt->error . "</p>
            </div>
            <button class='btn' onclick='history.back()'>Intentar de nuevo</button>
        </div>";
}

// Mostrar la tabla de la base de datos
echo "<div class='container'>
        <button class='btn' onclick='mostrarTabla()'>Mostrar Inventario</button>
        <div class='table-container' id='tableContainer'>
            <h2>Inventario Actual</h2>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>";

$sql = "SELECT producto, cantidad FROM inventario";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Mostrar los datos de la tabla
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['producto'] . "</td><td>" . $row['cantidad'] . "</td></tr>";
    }
} else {
    echo "<tr><td colspan='2'>No hay productos en el inventario.</td></tr>";
}

echo "        </tbody>
            </table>
        </div>
    </div>";

$stmt->close();
$conn->close();

echo "<script>
    function mostrarTabla() {
        var tableContainer = document.getElementById('tableContainer');
        if (tableContainer.style.display === 'none' || tableContainer.style.display === '') {
            tableContainer.style.display = 'block';
        } else {
            tableContainer.style.display = 'none';
        }
    }
</script>";

echo "</body>
</html>";
?>
