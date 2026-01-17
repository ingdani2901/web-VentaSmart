<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');

session_start();

// Verificar autenticación
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Verificar rol (1 = administrador)
$rol_permitido = 1;
if ($_SESSION['rol'] != $rol_permitido) {
    $_SESSION['error'] = "Acceso no autorizado";
    header("Location: login.php");
    exit();
}

// Configurar mensajes
$alertas = [];

// Conexión a BD
include 'funciones/db.php';

// Variables para filtro
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

// Validar fechas
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio)) $fecha_inicio = date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) $fecha_fin = date('Y-m-d');

// Procesar formulario de ingreso
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $alertas[] = [
            'tipo' => 'danger',
            'mensaje' => 'Token de seguridad inválido'
        ];
    } else {
        $monto = filter_input(INPUT_POST, 'monto', FILTER_VALIDATE_FLOAT);
        $descripcion = htmlspecialchars(trim($_POST['descripcion'] ?? ''));
        $id_usuario = $_SESSION['id_usuario'];

        if ($monto === false || $monto <= 1) {
            $alertas[] = [
                'tipo' => 'danger',
                'mensaje' => 'Monto inválido'
            ];
        } elseif (preg_match('/\d/', $descripcion)) {
            $alertas[] = [
                'tipo' => 'danger',
                'mensaje' => 'La descripción no puede contener números'
            ];
        } elseif (strlen($descripcion) > 255) {
            $alertas[] = [
                'tipo' => 'danger',
                'mensaje' => 'La descripción no debe exceder 255 caracteres'
            ];
        } else {
            try {
                $stmt = $conn->prepare("INSERT INTO caja_movimientos 
                    (tipo, monto, fecha_hora, id_usuario, descripcion) 
                    VALUES ('ingreso', ?, ?, ?, ?)");
                $fecha = date('Y-m-d H:i:s');
                $stmt->bind_param("dsis", $monto, $fecha, $id_usuario, $descripcion);

                if ($stmt->execute()) {
                    $conn->commit();
                    $alertas[] = [
                        'tipo' => 'success',
                        'mensaje' => 'Ingreso registrado correctamente'
                    ];
                } else {
                    throw new Exception("Error al registrar ingreso");
                }
                
            } catch (Exception $e) {
                $conn->rollback();
                $alertas[] = [
                    'tipo' => 'danger',
                    'mensaje' => 'Error: ' . $e->getMessage()
                ];
            } finally {
                $conn->autocommit(TRUE);
                $stmt->close();
            }
        }
    }
}

// Consultar datos
try {
    // Formatear fechas para consulta
    $fecha_inicio_datetime = $fecha_inicio . " 00:00:00";
    $fecha_fin_datetime = $fecha_fin . " 23:59:59";

    // 1. Obtener TOTAL DE VENTAS
    $sql_ventas = $conn->prepare("
        SELECT SUM(total) AS total_ventas 
        FROM ventas 
        WHERE fecha BETWEEN ? AND ?
        AND fechacancelada IS NULL
    ");
    $sql_ventas->bind_param("ss", $fecha_inicio_datetime, $fecha_fin_datetime);
    $sql_ventas->execute();
    $result_ventas = $sql_ventas->get_result();
    $ventas_data = $result_ventas->fetch_assoc();
    $total_ventas = (float)($ventas_data['total_ventas'] ?? 0);
    $sql_ventas->close();

    // 2. Obtener movimientos de caja
    $sql_totales = $conn->prepare("
        SELECT 
            SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) AS ingresos,
            SUM(CASE WHEN tipo = 'retiro' THEN monto ELSE 0 END) AS retiros
        FROM caja_movimientos
        WHERE fecha_hora BETWEEN ? AND ? AND estado = 'activo'
    ");
    $sql_totales->bind_param("ss", $fecha_inicio_datetime, $fecha_fin_datetime);
    $sql_totales->execute();
    $result_totales = $sql_totales->get_result();
    $totales = $result_totales->fetch_assoc();
    $sql_totales->close();

    // 3. Calcular balance (independiente de las ventas)
    $ingresos = (float)($totales['ingresos'] ?? 0);
    $retiros = (float)($totales['retiros'] ?? 0);
    $balance = $ingresos - $retiros;

    // 4. Obtener detalle de movimientos
    $sql_movimientos = $conn->prepare("
        SELECT cm.*, u.nombre AS usuario_nombre, p.nombre AS proveedor_nombre
        FROM caja_movimientos cm
        LEFT JOIN usuarios u ON cm.id_usuario = u.id_usuario
        LEFT JOIN compras_prov cp ON cm.id_comprasprov = cp.id_comprasprov
        LEFT JOIN proveedores p ON cp.id_proveedor = p.id_proveedor
        WHERE cm.fecha_hora BETWEEN ? AND ? AND cm.estado = 'activo'
        ORDER BY cm.fecha_hora DESC
    ");
    $sql_movimientos->bind_param("ss", $fecha_inicio_datetime, $fecha_fin_datetime);
    $sql_movimientos->execute();
    $resultado = $sql_movimientos->get_result();
    $sql_movimientos->close();

} catch (Exception $e) {
    $alertas[] = [
        'tipo' => 'danger',
        'mensaje' => $e->getMessage()
    ];
    $resultado = [];
    $totales = ['ingresos' => 0, 'retiros' => 0];
    $balance = 0;
    $total_ventas = 0;
}

// Generar CSRF Token
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Corte de Caja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" />
    <style>
        :root {
            --main-bg: #9966CC;
            --card-bg: #f8f9fa;
            --success-gradient: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            --danger-gradient: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            --primary-gradient: linear-gradient(135deg, #007bff 0%, #0069d9 100%);
        }

        body {
            background: linear-gradient(-45deg, #6d38a0, #ff7eb3, #D8B9D6, #23a6d5);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .floating-btn {
            position: fixed;
            top: 30px;
            right: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            width: 50px;
            height: 50px;
            z-index: 1000;
        }
    </style>
</head>
<body class="container py-4">

    <a href="#" onclick="confirmarRegreso(); return false;" class="floating-btn btn btn-primary btn-lg rounded-circle">
        <i class="bi bi-house-door fs-4"></i>
    </a>

    <div class="glass-card">
        <div class="header-section d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold mb-2">
                    <i class="bi bi-cash-stack me-2"></i>Corte de Caja
                </h1>
                <p class="lead text-muted mb-1"><?= date('d/m/Y') ?></p>
                <small class="text-muted">Usuario: <?= htmlspecialchars($_SESSION['nombre']) ?></small>
            </div>
            <img src="https://cdn-icons-png.flaticon.com/512/2331/2331966.png" alt="Caja" class="header-image" style="width: 120px;">
        </div>

        <?php if (!empty($alertas)): ?>
            <script>
                <?php foreach ($alertas as $alerta): ?>
                    Swal.fire({
                        icon: '<?= $alerta['tipo'] ?>',
                        title: '<?= addslashes($alerta['mensaje']) ?>',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                <?php endforeach; ?>
            </script>
        <?php endif; ?>

        <div class="mb-4">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="fecha_inicio" class="col-form-label">Fecha Inicio:</label>
                </div>
                <div class="col-auto">
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" 
                           value="<?= htmlspecialchars($fecha_inicio) ?>" required />
                </div>
                <div class="col-auto">
                    <label for="fecha_fin" class="col-form-label">Fecha Fin:</label>
                </div>
                <div class="col-auto">
                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" 
                           value="<?= htmlspecialchars($fecha_fin) ?>" required />
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </form>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card text-white h-100 summary-card" style="background: var(--success-gradient);">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-arrow-down-circle fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title mb-1">Ingresos</h5>
                                <h2 class="card-text">$<?= number_format($totales['ingresos'] ?? 0, 2) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card text-white h-100 summary-card" style="background: var(--danger-gradient);">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-arrow-up-circle fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title mb-1">Retiros</h5>
                                <h2 class="card-text">$<?= number_format($totales['retiros'] ?? 0, 2) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card text-white h-100 summary-card" style="background: var(--primary-gradient);">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-graph-up fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title mb-1">Balance</h5>
                                <h2 class="card-text">$<?= number_format($balance, 2) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($_SESSION['rol'] == 1): ?>
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-cash-stack me-2"></i>Verificación de Efectivo</h5>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Total de ventas a verificar: <strong>$<?= number_format($total_ventas, 2) ?></strong>
                </div>
                <button type="button" class="btn btn-info w-100" onclick="checkCash()">
                    <i class="bi bi-cash-coin me-2"></i>Realizar Corte
                </button>
            </div>

            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-plus-circle me-2"></i>Registrar Movimiento</h5>
                <form id="formIngreso" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="monto" name="monto" 
                                       step="0.01" min="0.01" required placeholder="Monto">
                                <label for="monto">Monto</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="descripcion" name="descripcion" 
                                       maxlength="255" required placeholder="Descripción"
                                       oninput="this.value = this.value.replace(/[0-9]/g, '')">
                                <label for="descripcion">Descripción</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success px-4 py-2">
                                <i class="bi bi-save me-2"></i>Registrar Ingreso
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <div class="glass-card p-4">
            <h5 class="fw-bold mb-4">
                <i class="bi bi-list-ul me-2"></i>
                Movimientos del <?= date('d/m/Y', strtotime($fecha_inicio)) ?> 
                al <?= date('d/m/Y', strtotime($fecha_fin)) ?>
            </h5>
            <table class="table table-sm table-bordered text-center table-primary" id="tabla_datos" style="width:100%;">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha/Hora</th>
                        <th>Tipo</th>
                        <th>Monto</th>
                        <th>Usuario</th>
                        <th>Descripción</th>
                        <th>Proveedor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php while($mov = $resultado->fetch_assoc()): ?>
                            <tr class="<?= $mov['tipo'] == 'ingreso' ? 'table-success' : 'table-danger' ?>">
                                <td><?= date('d/m/Y H:i', strtotime($mov['fecha_hora'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $mov['tipo'] == 'ingreso' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($mov['tipo']) ?>
                                    </span>
                                </td>
                                <td class="fw-bold">$<?= number_format($mov['monto'], 2) ?></td>
                                <td><?= htmlspecialchars($mov['usuario_nombre']) ?></td>
                                <td><?= htmlspecialchars($mov['descripcion']) ?></td>
                                <td><?= htmlspecialchars($mov['proveedor_nombre'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No hay movimientos para mostrar</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const expectedCash = <?= json_encode($total_ventas) ?>;

        function confirmarRegreso() {
            Swal.fire({
                title: "¿Salir de caja?",
                text: "Volverás al menú principal",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#6A0572",
                cancelButtonColor: "#d33",
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar",
                backdrop: 'rgba(0,0,0,0.4)'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "menu.php";
                }
            });
        }

        function checkCash() {
            Swal.fire({
                title: 'Verificación de Corte',
                html: `<p>Total de ventas esperado: <strong>$${expectedCash.toFixed(2)}</strong></p>
                      <p>Ingrese el efectivo físico contado:</p>`,
                input: 'number',
                inputAttributes: {
                    step: '0.01',
                    min: '0.01',
                    placeholder: 'Ej. 1500.50'
                },
                inputValidator: (value) => {
                    if (!value || isNaN(value)) {
                        return 'Ingrese un monto válido!';
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Verificar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const montoIngresado = parseFloat(result.value);
                    const diferencia = montoIngresado - expectedCash;
                    
                    if (Math.abs(diferencia) < 0.01) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Corte Correcto!',
                            html: `<p>El efectivo coincide con las ventas</p>
                                  <div class="alert alert-success mt-3">
                                      <i class="bi bi-check2-circle me-2"></i>
                                      Total verificado: $${montoIngresado.toFixed(2)}
                                  </div>`,
                            confirmButtonColor: '#6d38a0'
                        });
                    } else {
                        const mensaje = diferencia > 0 
                            ? `Sobrante: $${Math.abs(diferencia).toFixed(2)}` 
                            : `Faltante: $${Math.abs(diferencia).toFixed(2)}`;
                        
                        Swal.fire({
                            icon: 'error',
                            title: '¡Discrepancia!',
                            html: `<div class="alert alert-danger">
                                      <i class="bi bi-exclamation-triangle me-2"></i>
                                      ${mensaje}
                                  </div>
                                  <table class="table table-sm">
                                      <tr><th>Total Ventas</th><td>$${expectedCash.toFixed(2)}</td></tr>
                                      <tr><th>Efectivo Contado</th><td>$${montoIngresado.toFixed(2)}</td></tr>
                                      <tr><th>Diferencia</th><td class="fw-bold">$${diferencia.toFixed(2)}</td></tr>
                                  </table>`,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                }
            });
        }

        $(document).ready(function() {
            $('#tabla_datos').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.3/i18n/es_es.json'
                },
                order: [[0, 'desc']]
            });

            $('#formIngreso').on('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Confirmar registro?',
                    text: '¿Estás seguro de registrar este ingreso?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>