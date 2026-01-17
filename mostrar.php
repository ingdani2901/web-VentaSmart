<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
date_default_timezone_set('America/Mexico_City');
// Conexión a la base de datos
$pdo = new PDO("mysql:host=localhost;dbname=venta_smart", "usuario", "contraseña");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Filtros por fecha
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

$condiciones = '';
$params = [];

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $condiciones = "WHERE m.fecha_hora BETWEEN :inicio AND :fin";
    $params = [
        ':inicio' => $fecha_inicio . ' 00:00:00',
        ':fin' => $fecha_fin . ' 23:59:59'
    ];
}

// Consulta
$sql = "SELECT m.id_movimiento, m.tipo, m.monto, m.fecha_hora, m.descripcion, m.estado, u.usuario
        FROM caja_movimientos m
        JOIN usuarios u ON m.id_usuario = u.id_usuario
        $condiciones
        ORDER BY m.fecha_hora DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Movimientos de Caja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4 text-center">📋 Reporte de Movimientos de Caja</h2>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>">
        </div>
        <div class="col-md-4">
            <label for="fecha_fin" class="form-label">Fecha Fin</label>
            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Descripción</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($movimientos) > 0): ?>
                <?php foreach ($movimientos as $m): ?>
                    <tr>
                        <td><?= $m['id_movimiento'] ?></td>
                        <td>
                            <span class="badge <?= $m['tipo'] === 'ingreso' ? 'bg-success' : 'bg-danger' ?>">
                                <?= ucfirst($m['tipo']) ?>
                            </span>
                        </td>
                        <td>$<?= number_format($m['monto'], 2) ?></td>
                        <td><?= $m['fecha_hora'] ?></td>
                        <td><?= htmlspecialchars($m['usuario']) ?></td>
                        <td><?= htmlspecialchars($m['descripcion']) ?></td>
                        <td>
                            <span class="badge <?= $m['estado'] === 'activo' ? 'bg-primary' : 'bg-secondary' ?>">
                                <?= ucfirst($m['estado']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No hay movimientos en el rango seleccionado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
