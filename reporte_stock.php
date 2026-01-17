<?php
session_start();
include 'funciones/db.php';
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$sql = "SELECT 
           p.*, 
           c.nombre as categoria_nombre,
           (p.cantidad - p.stock) as diferencia,
           (p.cantidad/p.stock) * 100 as porcentaje
        FROM productos p
        JOIN categorias c ON p.id_categoria = c.id_categoria
        WHERE p.cantidad <= p.stock
          AND p.baja_alta = 'activo'
          AND p.stock > 0
        ORDER BY porcentaje ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Stock Bajo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>


 <style>
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
        .stock-critico {
            background-color: #ffcccc !important;
        }
        .stock-bajo {
            background-color: #fff3cd !important;
        }
.floating-btn {
    position: fixed;
    top: 30px;
    right: 30px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

        h2 {
            color: #6A0572;
            font-weight: bold;
        }
        .badge {
            font-size: 0.9em;
        }
        .stock-critico {
    background-color: #f8d7da !important; /* rojo claro */
}
.stock-bajo {
    background-color: #fff3cd !important; /* amarillo claro */
}
        .progress-bar {
            font-size: 0.8em;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border-color: #ffeeba;
        }
        .alert-warning i {
            margin-right: 5px;
        }
        .btn-warning, .btn-danger {
    transition: all 0.3s ease;
    min-width: 80px;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}
/* Agrega esto al final de tus estilos */
@media print {
    .floating-btn,
    .dataTables_paginate,
    .dataTables_length,
    .dataTables_filter {
        display: none !important;
    }
    
    body {
        background: white !important;
        animation: none !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}

/* Estilo temporal para la generación de PDF */
.pdf-export {
    background: white !important;
    animation: none !important;
}
@media print {
    .progress-bar {
        border: 1px solid #ccc !important;
        background-image: none !important;
        color: #000 !important;
        text-align: center;
    }
    
    .badge {
        border: 1px solid #000 !important;
        background-color: transparent !important;
        color: #000 !important;
    }
    
    .stock-critico {
        background-color: #fff0f0 !important;
        -webkit-print-color-adjust: exact;
    }
    
    .stock-bajo {
        background-color: #fff9e6 !important;
        -webkit-print-color-adjust: exact;
    }
}
    </style>
</head>
<body class="container mt-4">
    
    <div class="card p-3 mb-4">
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill"></i> Estos productos necesitan reabastecimiento.
        </div>
        
        <table class="table table-bordered text-center compact table-primary" id="tabla_stock">
            <thead class="table-dark">
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Stock Actual</th>
                    <th>Stock Mínimo</th>
                    <th>Diferencia</th>
                    <th>% Disponible</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): 
                    $porcentaje = ($row['stock'] > 0) ? ($row['cantidad'] / $row['stock']) * 100 : 0;
                    $clase_fila = ($porcentaje < 20) ? 'stock-critico' : 'stock-bajo';
                ?>
                <tr class="<?= $clase_fila ?>">
                    <td><?= htmlspecialchars($row['codigo']) ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['categoria_nombre']) ?></td>
                    <td><?= htmlspecialchars($row['cantidad']) ?></td>
                    <td><?= htmlspecialchars($row['stock']) ?></td>
                    <?php
                    $diferencia = $row['cantidad'] - $row['stock'];
                    $urgencia = '';
                    
                    if ($diferencia < -50) {
                        $urgencia = '<a href="pc.php?codigo='.urlencode($row['codigo']).'" class="badge bg-danger"><i class="bi bi-exclamation-octagon-fill"></i> ¡URGENTE!</a>';
                    } elseif ($diferencia < -20) {
                        $urgencia = '<a href="pc.php?codigo='.urlencode($row['codigo']).'" class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle-fill"></i> Atención</a>';
                    } elseif ($diferencia < 0) {
                        $urgencia = '<a href="pc.php?codigo='.urlencode($row['codigo']).'" class="badge bg-info text-dark"><i class="bi bi-exclamation-circle-fill"></i> Revisar</a>';
                    }
                    ?>
                    <td class="<?= ($diferencia < -20) ? 'text-danger fw-bold' : 'text-warning' ?>">
                        <?= $diferencia ?> <?= $urgencia ?>
                    </td>


                    <td>
                        <div class="progress">
                            <div class="progress-bar <?= ($porcentaje < 30) ? 'bg-danger' : 'bg-warning' ?>" 
                                 role="progressbar" 
                                 style="width: <?= $porcentaje ?>%" 
                                 aria-valuenow="<?= $porcentaje ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                <?= round($porcentaje) ?>%
                            </div>
                        </div>
                    </td>
                <?php endwhile; ?>
            </tbody>
        </table>
        
<!-- Botón Exportar a PDF más pequeño --> 
 <button class="btn btn-primary btn-sm px-2 py-1" id="exportPdf">
    <i class="bi bi-file-earmark-pdf-fill"></i> 
    <span class="btn-text">PDF</span>
    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
</button>

<!-- Botón Volver a Productos más pequeño -->
<a href="crud_productos.php" class="btn btn-secondary btn-sm px-2 py-1">
    <i class="bi bi-arrow-left"></i> Productos
</a>

        </div>

<!-- Botón flotante mejorado -->
<a href="#" onclick="confirmarRegreso(); return false;" 
   class="floating-btn btn btn-primary btn-lg rounded-circle">
    <i class="bi bi-house-door fs-4"></i>
</a>

    <script>
        $(document).ready(function() {
            $('#tabla_stock').DataTable({
                language: {
                    "decimal": "",
                    "emptyTable": "¡Excelente! No hay productos con stock bajo.",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ productos",
                    "infoEmpty": "Mostrando 0 productos",
                    "infoFiltered": "(filtrado de _MAX_ productos totales)",
                    "lengthMenu": "Mostrar _MENU_ productos",
                    "search": "Buscar:",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                },
                order: [[6, 'asc']] // Ordenar por % disponible (de menor a mayor)
            });
        });

function confirmarRegreso() {
    Swal.fire({
        title: "¿Salir de reportes de stock?",
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
        } else {
            Swal.fire({
                title: "Acción cancelada",
                text: "Sigues en el módulo de usuarios",
                icon: "info",
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
}
    </script>
</body>
<script>
     window.jsPDF = window.jspdf.jsPDF; 
document.getElementById('exportPdf').addEventListener('click', async () => {
    const btn = document.getElementById('exportPdf');
    const spinner = btn.querySelector('.spinner-border');
    const btnText = btn.querySelector('.btn-text');
    
    try {
        btn.disabled = true;
        btnText.textContent = 'Generando PDF...';
        spinner.classList.remove('d-none');

        // Configurar DataTable para mostrar todos los registros
        const table = $('#tabla_stock').DataTable();
        table.page.len(-1).draw();
        
        // Obtener datos limpios
        const datos = [];
        $('#tabla_stock tbody tr').each(function() {
            const row = {
                codigo: $(this).find('td:eq(0)').text().trim(),
                producto: $(this).find('td:eq(1)').text().trim(),
                categoria: $(this).find('td:eq(2)').text().trim(),
                stock: $(this).find('td:eq(3)').text().trim(),
                minimo: $(this).find('td:eq(4)').text().trim(),
                diferencia: $(this).find('td:eq(5)').text().replace(/[^\d-]/g, '').trim(),
                porcentaje: $(this).find('.progress-bar').text().trim()
            };
            datos.push(row);
        });

        // Crear PDF
        const doc = new jsPDF('p', 'pt');
        
        // Encabezado
        doc.setFontSize(18);
        doc.setTextColor(40);
        doc.text('Reporte de Stock Bajo', 40, 40);
        
        // Subtítulo
        doc.setFontSize(12);
        doc.setTextColor(100);
        doc.text(`Generado: ${new Date().toLocaleDateString()} - Total de productos: ${datos.length}`, 40, 60);
        
        // Configurar tabla
        const columns = [
            { header: 'Código', dataKey: 'codigo' },
            { header: 'Producto', dataKey: 'producto' },
            { header: 'Categoría', dataKey: 'categoria' },
            { header: 'Stock Actual', dataKey: 'stock' },
            { header: 'Mínimo', dataKey: 'minimo' },
            { header: 'Diferencia', dataKey: 'diferencia' },
            { header: '% Disponible', dataKey: 'porcentaje' }
        ];
        
        // Opciones de la tabla
        const options = {
            startY: 80,
            margin: { left: 40 },
            styles: { 
                fontSize: 10,
                cellPadding: 6,
                overflow: 'linebreak'
            },
            columnStyles: {
                codigo: { cellWidth: 60 },
                producto: { cellWidth: 120 },
                categoria: { cellWidth: 80 },
                stock: { cellWidth: 50 },
                minimo: { cellWidth: 50 },
                diferencia: { cellWidth: 60 },
                porcentaje: { cellWidth: 50 }
            },
            headerStyles: {
                fillColor: [106, 5, 114], // Color morado
                textColor: 255
            },
            alternateRowStyles: {
                fillColor: [245, 245, 245]
            }
        };
        
        // Generar tabla
        doc.autoTable(columns, datos, options);
        
        // Pie de página
        const pageCount = doc.internal.getNumberOfPages();
        for(let i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            doc.setFontSize(10);
            doc.text(`Página ${i} de ${pageCount}`, doc.internal.pageSize.width - 100, doc.internal.pageSize.height - 20);
        }

        // Guardar
        doc.save('reporte_stock.pdf');

    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'Error generando el PDF: ' + error.message, 'error');
    } finally {
        btn.disabled = false;
        btnText.textContent = 'Exportar a PDF';
        spinner.classList.add('d-none');
        $('#tabla_stock').DataTable().page.len(10).draw(); // Restaurar paginación
    }
});
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
</html>