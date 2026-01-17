<?php
header('Content-Type: text/html; charset=UTF-8'); 
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=venta_smart", "root", "daniela290104");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

// Filtros del reporte
$tipo = $_GET['tipo'] ?? 'dia';
$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');

if ($desde > $hasta) {
    list($desde, $hasta) = array($hasta, $desde);
}

// ==============================================
// CONSULTAS PRINCIPALES (DEFINIDAS ANTES DEL TRY)
// ==============================================

// Formato de agrupación para VENTAS
switch ($tipo) {
    case 'dia': $groupVentas = "DATE(v.fecha)"; break;
    case 'semana': $groupVentas = "CONCAT(YEAR(v.fecha), '-Semana ', WEEK(v.fecha))"; break;
    case 'mes': $groupVentas = "DATE_FORMAT(v.fecha, '%Y-%m')"; break;
    case 'anio': $groupVentas = "YEAR(v.fecha)"; break;
    default: $groupVentas = "DATE(v.fecha)";
}

// Formato de agrupación para COMPRAS
switch ($tipo) {
    case 'dia': $groupCompras = "DATE(c.fecha)"; break;
    case 'semana': $groupCompras = "CONCAT(YEAR(c.fecha), '-Semana ', WEEK(c.fecha))"; break;
    case 'mes': $groupCompras = "DATE_FORMAT(c.fecha, '%Y-%m')"; break;
    case 'anio': $groupCompras = "YEAR(c.fecha)"; break;
    default: $groupCompras = "DATE(c.fecha)";
}

// Consulta para Ventas (definida antes de su uso)
$sqlVentas = "SELECT 
                $groupVentas as periodo, 
                SUM(v.total) as total_ventas,
                SUM(dv.cantidad * dv.precio_uni) as total_importe_ventas,
                SUM(dv.cantidad * p.precio_compra) as total_costo,
                SUM(dv.cantidad * (dv.precio_uni - p.precio_compra)) as ganancias,
                COUNT(DISTINCT v.id_ventas) as num_ventas,
                SUM(dv.cantidad) as total_productos_vendidos
            FROM ventas v
            INNER JOIN det_venta dv ON v.id_ventas = dv.id_venta
            INNER JOIN productos p ON dv.id_producto = p.id_producto
            WHERE v.fecha BETWEEN :desde AND :hasta
            AND v.fechacancelada IS NULL
            GROUP BY periodo
            ORDER BY periodo ASC";

// Consulta para Compras (definida antes de su uso)
$sqlCompras = "SELECT 
                $groupCompras as periodo,
                SUM(c.total_compra) as total_compras,
                SUM(dc.cantidad) as total_productos_comprados
             FROM compras_prov c
             INNER JOIN det_compra dc ON c.id_comprasprov = dc.id_comprasprov
             WHERE c.fecha BETWEEN :desde AND :hasta
             AND c.estado = 'activa'
             GROUP BY periodo
             ORDER BY periodo ASC";

// Consultas para productos más vendidos/comprados
$sqlTopVendidos = "SELECT 
                    p.nombre,
                    SUM(dv.cantidad) as total_vendido,
                    SUM(dv.cantidad * dv.precio_uni) as total_ingresos
                 FROM det_venta dv
                 INNER JOIN ventas v ON dv.id_venta = v.id_ventas
                 INNER JOIN productos p ON dv.id_producto = p.id_producto
                 WHERE v.fecha BETWEEN :desde AND :hasta
                 AND v.fechacancelada IS NULL
                 GROUP BY p.id_producto
                 ORDER BY total_vendido DESC
                 LIMIT 10";

$sqlTopComprados = "SELECT 
                     p.nombre,
                     SUM(dc.cantidad) as total_comprado,
                     SUM(dc.cantidad * dc.precio_uni) as total_gastado
                  FROM det_compra dc
                  INNER JOIN compras_prov c ON dc.id_comprasprov = c.id_comprasprov
                  INNER JOIN productos p ON dc.id_producto = p.id_producto
                  WHERE c.fecha BETWEEN :desde AND :hasta
                  AND c.estado = 'activa'
                  GROUP BY p.id_producto
                  ORDER BY total_comprado DESC
                  LIMIT 10";

try {
    // ==============================================
    // EJECUCIÓN DE CONSULTAS (DENTRO DEL TRY)
    // ==============================================
    
    // 1. Ejecutar consulta de ventas
    $stmtVentas = $pdo->prepare($sqlVentas);
    $stmtVentas->execute([
        ':desde' => $desde . ' 00:00:00',
        ':hasta' => $hasta . ' 23:59:59'
    ]);
    $dataVentas = $stmtVentas->fetchAll(PDO::FETCH_ASSOC);

    // 2. Ejecutar consulta de compras
    $stmtCompras = $pdo->prepare($sqlCompras);
    $stmtCompras->execute([
        ':desde' => $desde . ' 00:00:00',
        ':hasta' => $hasta . ' 23:59:59'
    ]);
    $dataCompras = $stmtCompras->fetchAll(PDO::FETCH_ASSOC);

    // 3. Combinar datos de ventas y compras
    $combinedData = [];
    foreach ($dataVentas as $venta) {
        $combinedData[$venta['periodo']] = $venta;
    }
    foreach ($dataCompras as $compra) {
        if (isset($combinedData[$compra['periodo']])) {
            $combinedData[$compra['periodo']] = array_merge($combinedData[$compra['periodo']], $compra);
        } else {
            $combinedData[$compra['periodo']] = $compra;
        }
    }
    ksort($combinedData);

    // 4. Productos más vendidos
    $stmtTopVendidos = $pdo->prepare($sqlTopVendidos);
    $stmtTopVendidos->execute([
        ':desde' => $desde . ' 00:00:00',
        ':hasta' => $hasta . ' 23:59:59'
    ]);
    $topVendidos = $stmtTopVendidos->fetchAll(PDO::FETCH_ASSOC);

    // 5. Productos más comprados
    $stmtTopComprados = $pdo->prepare($sqlTopComprados);
    $stmtTopComprados->execute([
        ':desde' => $desde . ' 00:00:00',
        ':hasta' => $hasta . ' 23:59:59'
    ]);
    $topComprados = $stmtTopComprados->fetchAll(PDO::FETCH_ASSOC);

    // ==============================================
    // CÁLCULO DE TOTALES
    // ==============================================
    $totales = [
        'ventas' => 0,
        'ganancias' => 0,
        'costo' => 0,
        'compras' => 0,
        'productos_vendidos' => 0,
        'productos_comprados' => 0
    ];
    
    foreach ($combinedData as $row) {
        $totales['ventas'] += $row['total_ventas'] ?? 0;
        $totales['ganancias'] += $row['ganancias'] ?? 0;
        $totales['costo'] += $row['total_costo'] ?? 0;
        $totales['compras'] += $row['total_compras'] ?? 0;
        $totales['productos_vendidos'] += $row['total_productos_vendidos'] ?? 0;
        $totales['productos_comprados'] += $row['total_productos_comprados'] ?? 0;
    }
    
    $margenGanancia = ($totales['ventas'] > 0) ? ($totales['ganancias'] / $totales['ventas']) * 100 : 0;

    // ==============================================
    // PREPARAR DATOS PARA GRÁFICA
    // ==============================================
    $labels = [];
    $ventasData = [];
    $gananciasData = [];
    
    foreach ($combinedData as $row) {
        $labels[] = $row['periodo'];
        $ventasData[] = $row['total_ventas'] ?? 0;
        $gananciasData[] = $row['ganancias'] ?? 0;
    }

} catch (PDOException $e) {
    die("Error al generar el reporte: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de ganancias por ventas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- jsPDF AutoTable -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>


  <style>
    body { background: linear-gradient(-45deg, #6d38a0, #ff7eb3, #D8B9D6, #23a6d5); background-size: 400% 400%; animation: gradientBG 15s ease infinite; min-height: 100vh; font-family: 'Poppins', sans-serif; }
    @keyframes gradientBG {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .container { max-width: 1200px; margin: 0 auto; }
    h2 { color: #343a40; margin-bottom: 20px; padding-bottom: 10px; }
    form { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .card-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px; }
    .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .grafico-container { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
    .table-container { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow-x: auto; }
    .floating-btn { position: fixed; top: 30px; right: 30px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); width: 50px; height: 50px; }
    @media print {
        body { background: white !important; animation: none !important; }
        .floating-btn, form, .grafico-container, .dataTables_length, .dataTables_filter, .dataTables_paginate, .bi-house-door, .btn { display: none !important; }
    }
  </style>
</head>
<body>
<div class="container mt-4">
    <a href="#" onclick="confirmarRegreso(); return false;" class="floating-btn btn btn-primary btn-lg rounded-circle">
        <i class="bi bi-house-door fs-4"></i>
    </a>

    <h2 class="text-center text-primary font-weight-bold">Reportes generales</h2>

    <form method="GET" id="filtroForm">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="tipo">Agrupar por:</label>
                <select name="tipo" id="tipo" class="form-control">
                    <option value="dia" <?= $tipo === 'dia' ? 'selected' : '' ?>>Día</option>
                    <option value="semana" <?= $tipo === 'semana' ? 'selected' : '' ?>>Semana</option>
                    <option value="mes" <?= $tipo === 'mes' ? 'selected' : '' ?>>Mes</option>
                    <option value="anio" <?= $tipo === 'anio' ? 'selected' : '' ?>>Año</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="desde">Desde:</label>
                <input type="date" class="form-control" name="desde" id="desde" value="<?= $desde ?>" max="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-3">
                <label for="hasta">Hasta:</label>
                <input type="date" class="form-control" name="hasta" id="hasta" value="<?= $hasta ?>" max="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Generar</button>
                <button type="button" class="btn btn-info" id="exportPdf">PDF</button>
            </div>
        </div>
    </form>

    <div class="card-container">
        <div class="card">
            <h3>Total Ventas</h3>
            <div class="value">$<?= number_format($totales['ventas'], 2) ?></div>
            <small>Importe total de ventas</small>
        </div>
        <div class="card">
            <h3>Ganancias</h3>
            <div class="value <?= $totales['ganancias'] >= 0 ? 'positivo' : 'negativo' ?>">
                $<?= number_format($totales['ganancias'], 2) ?>
            </div>
            <small>Beneficio neto obtenido</small>
        </div>
        <div class="card">
            <h3>Costo Total</h3>
            <div class="value">$<?= number_format($totales['costo'], 2) ?></div>
            <small>Costo de productos vendidos</small>
        </div>
        <div class="card">
            <h3>Margen</h3>
            <div class="value <?= $margenGanancia >= 0 ? 'positivo' : 'negativo' ?>">
                <?= number_format($margenGanancia, 2) ?>%
            </div>
            <small>Porcentaje de ganancia</small>
        </div>
        <div class="card">
            <h3>Total Compras</h3>
            <div class="value">$<?= number_format($totales['compras'], 2) ?></div>
            <small>Importe total de compras</small>
        </div>
        <div class="card">
            <h3>Productos</h3>
            <div class="value"><?= number_format($totales['productos_vendidos']) ?> / <?= number_format($totales['productos_comprados']) ?></div>
            <small>Vendidos / Comprados</small>
        </div>
    </div>

    <div class="grafico-container">
        <canvas id="grafico"></canvas>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="table-container">
                <h4>Productos más vendidos</h4>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-end">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topVendidos as $p): ?>
                        <tr>
                            <td><?= $p['nombre'] ?></td>
                            <td class="text-end"><?= number_format($p['total_vendido']) ?></td>
  
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="table-container">
                <h4>Productos más comprados</h4>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-end">Cantidad</th>
                            <th class="text-end">Inversión</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topComprados as $p): ?>
                        <tr>
                            <td><?= $p['nombre'] ?></td>
                            <td class="text-end"><?= number_format($p['total_comprado']) ?></td>
                            <td class="text-end">$<?= number_format($p['total_gastado'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="table-container mt-4">
        <table class="table table-hover" id="tabla_datos">
            <thead class="table-dark">
                <tr>
                    <th>Periodo</th>
                    <th class="text-end">Ventas</th>
                    <th class="text-end">Compras</th>
                    <th class="text-end">Ganancias</th>
                    <th class="text-end">Costo</th>
                    <th class="text-center">Margen</th>
                    <th class="text-center"># Ventas</th>
                    <th class="text-center">Productos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($combinedData as $row): 
                    $margen = ($row['total_ventas'] ?? 0 > 0) ? (($row['ganancias'] ?? 0) / ($row['total_ventas'] ?? 1)) * 100 : 0;
                ?>
                <tr>
                    <td><?= $row['periodo'] ?></td>
                    <td class="text-end">$<?= number_format($row['total_ventas'] ?? 0, 2) ?></td>
                    <td class="text-end">$<?= number_format($row['total_compras'] ?? 0, 2) ?></td>
                    <td class="text-end <?= ($row['ganancias'] ?? 0) >= 0 ? 'positivo' : 'negativo' ?>">
                        $<?= number_format($row['ganancias'] ?? 0, 2) ?>
                    </td>
                    <td class="text-end">$<?= number_format($row['total_costo'] ?? 0, 2) ?></td>
                    <td class="text-center">
                        <span class="badge <?= $margen >= 0 ? 'badge-success' : 'badge-danger' ?>">
                            <?= number_format($margen, 2) ?>%
                        </span>
                    </td>
                    <td class="text-center"><?= $row['num_ventas'] ?? 0 ?></td>
                    <td class="text-center"><?= number_format(($row['total_productos_vendidos'] ?? 0)) ?> / <?= number_format($row['total_productos_comprados'] ?? 0) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
  // Gráfico
  new Chart(document.getElementById('grafico').getContext('2d'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Ventas ($)',
            data: <?= json_encode($ventasData) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)'
        },{
            label: 'Ganancias ($)',
            data: <?= json_encode($gananciasData) ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.7)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: { display: true, text: 'Ventas y Ganancias por Periodo' }
        },
        scales: {
            y: { beginAtZero: true, ticks: { callback: value => '$' + value } },
            x: { ticks: { autoSkip: false, maxRotation: 45, minRotation: 45 } }
        }
    }
  });

  // DataTables
  $(document).ready(() => $('#tabla_datos').DataTable({
    language: {
        "decimal": "",
        "emptyTable": "No hay datos disponibles",
        "info": "Mostrando _START_ a _END_ de _TOTAL_",
        "infoEmpty": "Mostrando 0 a 0 de 0",
        "infoFiltered": "(filtrado de _MAX_ totales)",
        "search": "Buscar:",
        "paginate": { "first": "Primero", "last": "Último", "next": "Siguiente", "previous": "Anterior" }
    }
  }));

  // Exportar PDF
  window.jsPDF = window.jspdf.jsPDF;
  document.getElementById('exportPdf').addEventListener('click', () => {
    const pdf = new jsPDF('p', 'mm', 'a4');
    pdf.text("Reporte General", 105, 15, { align: 'center' });
    pdf.autoTable({ html: '#tabla_datos', startY: 25 });
    pdf.save('reporte-general.pdf');
  });

  function confirmarRegreso() {
    Swal.fire({
        title: "¿Salir del reporte?",
        text: "Volverás al menú principal",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#6A0572",
        cancelButtonColor: "#d33",
        confirmButtonText: "Confirmar",
        cancelButtonText: "Cancelar"
    }).then(result => {
        if (result.isConfirmed) window.location.href = "menu.php";
    });
  }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.style.position = 'fixed';
        canvas.style.top = '0';
        canvas.style.left = '0';
        canvas.style.zIndex = '1';
        canvas.style.pointerEvents = 'none';
        document.body.appendChild(canvas);

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        const particles = [];
        
        class Star {
            constructor() {
                this.reset();
                this.rotation = Math.random() * Math.PI * 2;
                this.rotationSpeed = (Math.random() - 0.5) * 0.02;
                this.points = Math.floor(Math.random() * 3) + 5; // 5-7 puntas
            }
            
            reset() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 3 + 1;
                this.speedX = (Math.random() - 0.5) * 2;
                this.speedY = (Math.random() - 0.5) * 2;
                this.alpha = Math.random() * 0.5 + 0.5;
                this.twinkleSpeed = Math.random() * 0.05 + 0.02;
            }
            
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                this.rotation += this.rotationSpeed;
                
                // Efecto de parpadeo
                this.alpha += this.twinkleSpeed;
                if(this.alpha < 0.3 || this.alpha > 1) this.twinkleSpeed *= -1;
                
                if (this.x > canvas.width + 50 || this.x < -50 || 
                    this.y > canvas.height + 50 || this.y < -50) {
                    this.reset();
                }
            }
            
            draw() {
                ctx.save();
                ctx.translate(this.x, this.y);
                ctx.rotate(this.rotation);
                ctx.fillStyle = `rgba(200, 220, 255, ${this.alpha})`;
                
                // Dibujar estrella
                const spikes = this.points;
                const outerRadius = this.size;
                const innerRadius = this.size * 0.5;
                
                ctx.beginPath();
                for(let i = 0; i < spikes; i++) {
                    const angle = (i * 2 * Math.PI) / spikes;
                    
                    // Punto exterior
                    const x1 = Math.cos(angle) * outerRadius;
                    const y1 = Math.sin(angle) * outerRadius;
                    
                    // Punto interior
                    const angle2 = angle + Math.PI / spikes;
                    const x2 = Math.cos(angle2) * innerRadius;
                    const y2 = Math.sin(angle2) * innerRadius;
                    
                    if(i === 0) ctx.moveTo(x1, y1);
                    else ctx.lineTo(x1, y1);
                    
                    ctx.lineTo(x2, y2);
                }
                ctx.closePath();
                ctx.fill();
                ctx.restore();
            }
        }

        function init() {
            for (let i = 0; i < 100; i++) { // Más estrellas
                particles.push(new Star());
            }
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Fondo estelar sutil
            ctx.fillStyle = 'rgba(0, 0, 20, 0.05)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            particles.forEach(particle => {
                particle.update();
                particle.draw();
            });
            
            requestAnimationFrame(animate);
        }
        
        init();
        animate();
    });
</script>
</body>
</html>