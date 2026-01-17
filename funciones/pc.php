<?php
// Establecer encabezados al principio para asegurar respuesta JSON
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);
date_default_timezone_set('America/Mexico_City');
function sendResponse($status, $message, $data = [], $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

try {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/../funciones/conectar.php';
    
    if (!isset($consulta)) {
        throw new Exception("Error de conexión con la base de datos", 500);
    }

    if (!isset($_SESSION['id_usuario'])) {
        throw new Exception("Acceso no autorizado: sesión no iniciada", 401);
    }
    $id_usuario = $_SESSION['id_usuario'];

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido", 405);
    }

    if (!isset($_POST['compra'])) {
        throw new Exception("Datos de compra no recibidos", 400);
    }

    $compra = json_decode($_POST['compra'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error al decodificar JSON: " . json_last_error_msg(), 400);
    }

    if (!isset($compra['proveedor'], $compra['productos']) || !is_array($compra['productos'])) {
        throw new Exception("Estructura de datos incorrecta", 400);
    }

    $proveedorParts = explode("-", $compra['proveedor']);
    if (count($proveedorParts) < 2) {
        throw new Exception("Formato de proveedor incorrecto", 400);
    }
    $id_proveedor = (int)$proveedorParts[0];

    $folioResult = $consulta->query("SELECT COALESCE(MAX(id_comprasprov), 0) + 1 AS folio FROM compras_prov");
    if (!$folioResult) {
        throw new Exception("Error al obtener folio: " . implode(" - ", $consulta->errorInfo()), 500);
    }
    $folioData = $folioResult->fetch(PDO::FETCH_ASSOC);
    $folio = str_pad($folioData['folio'], 4, "0", STR_PAD_LEFT);

    $consulta->beginTransaction();
    $fecha = date("Y-m-d H:i");
    try {
        $stmtCompra = $consulta->prepare("INSERT INTO compras_prov 
                            (id_proveedor, fecha, total_compra, folio, id_usuario, estado) 
                            VALUES (?,? , 0, ?, ?, 'activa')");
        
        if (!$stmtCompra) {
            throw new Exception("Error al preparar consulta: " . implode(" - ", $consulta->errorInfo()), 500);
        }

        $stmtCompra->execute([$id_proveedor,$fecha, $folio, $id_usuario]);
        $id_compra = $consulta->lastInsertId();
        $total_compra = 0;

        // MODIFICACIÓN PRINCIPAL: Se agregó precio_compra al UPDATE
        $stmtProducto = $consulta->prepare("SELECT id_producto FROM productos WHERE codigo = ?");
        $stmtDetalle = $consulta->prepare("INSERT INTO det_compra 
                            (id_comprasprov, id_producto, precio_uni, precio_venta, porcentaje_ganancia, cantidad, importe) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmtUpdate = $consulta->prepare("UPDATE productos SET precio = ?, precio_compra = ? WHERE codigo = ?");

        foreach ($compra['productos'] as $producto) {
            if (empty($producto['codigo']) || !isset($producto['precio_compra'], $producto['precio_venta'], $producto['cantidad'])) {
                throw new Exception("Datos de producto incompletos", 400);
            }

            $stmtProducto->execute([$producto['codigo']]);
            $result = $stmtProducto->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                throw new Exception("Producto no encontrado: " . $producto['codigo'], 404);
            }

            $id_producto = $result['id_producto'];

            $importe = $producto['cantidad'] * $producto['precio_compra'];
            $total_compra += $importe;
            $porcentaje = (($producto['precio_venta'] / $producto['precio_compra']) - 1) * 100;

            $stmtDetalle->execute([
                $id_compra,
                $id_producto,
                $producto['precio_compra'],
                $producto['precio_venta'],
                $porcentaje,
                $producto['cantidad'],
                $importe
            ]);

            // MODIFICACIÓN PRINCIPAL: Se agregó precio_compra al execute
            $stmtUpdate->execute([
                $producto['precio_venta'],
                $producto['precio_compra'],
                $producto['codigo']
            ]);
        }

        $stmtTotal = $consulta->prepare("UPDATE compras_prov SET total_compra = ? WHERE id_comprasprov = ?");
        $stmtTotal->execute([$total_compra, $id_compra]);

        $consulta->commit();

        sendResponse('success', 'Compra registrada correctamente', [
            'folio' => $folio,
            'total' => $total_compra,
            'id_compra' => $id_compra
        ]);

    } catch (Exception $e) {
        if ($consulta->inTransaction()) {
            $consulta->rollBack();
        }
        throw $e;
    }

} catch (Exception $e) {
    error_log("[" . date('Y-m-d H:i:s') . "] Error en pc.php: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    
    sendResponse(
        'error', 
        $e->getMessage(), 
        ['trace' => $e->getTraceAsString()],
        $e->getCode() ?: 500
    );
}
?>