<?php
session_start(); // Iniciar sesión
include('../funciones/conectar.php'); // Asegurar conexión

if (!isset($_POST['funcion'])) {
    exit("Error: Función no definida.");
}

if ($_POST['funcion'] == "Agregar") {
    if (!isset($_POST['codigo']) || !isset($_POST['cantidad'])) {
        exit("Error: Datos incompletos.");
    }

    $codigo = $_POST['codigo'];
    $cantidad = (int) $_POST['cantidad'];
    $importe = isset($_POST['importe']) ? (float) $_POST['importe'] : 0;

    $stmt = $consulta->prepare("SELECT * FROM productos WHERE codigo LIKE ?");
    $stmt->execute([$codigo]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        exit("Error: Producto no encontrado.");
    }

    // Calcular precio con importe (dividido por cantidad)
    $precio_con_importe = $producto['precio'] + ($importe / $cantidad);
    $nota_importe = $importe > 0 ? ' <small class="text-muted">(+importe)</small>' : '';

    $tabla = "<tr>
        <td><input type='number' class='cantidad' value='{$cantidad}'></td>
        <td idproductos='{$producto['id_producto']}'>{$codigo}</td>
        <td>{$producto['nombre']}</td>
        <td>$" . number_format($precio_con_importe, 2) . "$nota_importe</td>
        <td>$" . number_format($precio_con_importe * $cantidad, 2) . "</td>
        <td><button class='btn btn-danger eliminar' idregistros='{$producto['idproductos']}'>Eliminar</button></td>
    </tr>";

    echo $tabla;
    exit();
}

function FolioVenta()
{
    global $consulta;
    $stmt = $consulta->query("SELECT MAX(id_ventas) + 1 AS autoincrement FROM ventas");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row['autoincrement'] ?? 1; // Si NULL, retorna 1
}

if ($_POST['funcion'] == "Guardar_Venta") {
    if (!isset($_POST['total'], $_POST['idclientes'], $_POST['efectivo'], $_POST['Detalle'])) {
        exit("Error: Datos incompletos para guardar venta.");
    }

    $total = $_POST['total'];
    $id_cliente = ($_POST['idclientes'] == 0) ? NULL : $_POST['idclientes'];
    $efectivo = $_POST['efectivo'];
    $fecha = date("Y-m-d H:i:s");

    if (!isset($_SESSION['id_usuario'])) {
        exit("Error: Sesión no iniciada.");
    }
    $id_usuario = $_SESSION['id_usuario'];

    $folio = str_pad(FolioVenta(), 4, "0", STR_PAD_LEFT);

    // Insertar venta principal
    $stmt = $consulta->prepare("INSERT INTO ventas (fecha, total, folio, id_cliente, id_usuario, efectivo) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fecha, $total, $folio, $id_cliente, $id_usuario, $efectivo]);

    $id_venta = $consulta->lastInsertId();

    // Insertar detalles de venta con importe
    foreach ($_POST["Detalle"] as $producto) {
        // Obtener el precio real (quitando el importe si existe)
        $precio = $producto['precio'];
        if (is_string($precio) && strpos($precio, '+importe') !== false) {
            $precio = floatval(preg_replace('/[^0-9.]/', '', $precio));
        }
        
        // Calcular importe total (diferencia entre precio pagado y precio base)
        $stmt_producto = $consulta->prepare("SELECT precio FROM productos WHERE id_producto = ?");
        $stmt_producto->execute([$producto['id_producto']]);
        $precio_base = $stmt_producto->fetchColumn();
        
        $importe = (floatval($producto['precio']) - floatval($precio_base)) * intval($producto['cantidad']);
        $importe = max(0, $importe); // Asegurar que no sea negativo

        // Insertar en detalle de venta
        $stmt_detalle = $consulta->prepare("INSERT INTO det_venta 
            (id_venta, id_producto, cantidad, precio_uni, importe) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt_detalle->execute([
            $id_venta,
            $producto['id_producto'],
            $producto['cantidad'],
            $precio,
            $importe
        ]);
    }

    echo str_pad($id_venta, 4, "0", STR_PAD_LEFT);
    exit();
}

// Nueva función para verificar si es bebida alcohólica
if ($_POST['funcion'] == "VerificarBebida") {
    if (!isset($_POST['codigo'])) {
        exit("Error: Código no proporcionado.");
    }

    $codigo = $_POST['codigo'];
    
    $stmt = $consulta->prepare("SELECT categoria FROM productos WHERE codigo = ?");
    $stmt->execute([$codigo]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si es bebida alcohólica (ajusta según tu categoría)
    echo ($producto && $producto['categoria'] == 'bebidas alcoholicas') ? '1' : '0';
    exit();
}
?>