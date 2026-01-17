<?php
session_start();
// Incluir la conexión a la base de datos
if (!isset($_SESSION['id_usuario'])) {
    // Si no ha iniciado sesión, redirigir al login
    header("Location: login.php");
    exit;
}
if($_POST['funcion']=='Carga_Compras'){
    include("funciones/conectar.php");
    $proveedor = '';
    if($_POST['proveedor']!=''){
        $prov = explode("-",$_POST['proveedor']);
        $proveedor = ' AND cp.id_proveedor='. $prov[0];
    }
    
    $resultados=$consulta->query("SELECT cp.*, p.nombre AS proveedor, u.nombre AS usuario 
                                FROM compras_prov cp 
                                LEFT JOIN proveedores p ON p.id_proveedor=cp.id_proveedor 
                                LEFT JOIN usuarios u ON u.id_usuario=cp.id_usuario 
                                WHERE cp.fecha BETWEEN '".$_POST['fechai']." 00:00:00' AND '".$_POST['fechaf']." 23:59:59' 
                                $proveedor 
                                ORDER BY cp.fecha DESC");
    
    foreach ($resultados as $row) {
        $cancelado = ($row['estado'] == 'cancelada') ? 'bg-danger' : '';
    
        $fecha=explode(" ",$row["fecha"]);
        $ano=explode("-",$fecha[0]);
        $mes=$ano[1];
        $dia=$ano[2];
        $horas = explode(":",$fecha[1]);
        $hora = $horas[0];
    ?>
    <tr class="<?=$cancelado?> text-uppercase">
        <td><?=str_pad($row["id_comprasprov"], 6, "0", STR_PAD_LEFT)?></td>
        <td><?=$dia."/".$mes."/".$ano[0]." ".$hora.":".$horas[1].":".$horas[2]?></td>
        <td><?=$row['proveedor']?></td>
        <td><?=$row['usuario']?></td>
        <td align="right">$ <?=number_format($row['total_compra'],2)?></td>
        <td width="200">
        <a href="ticket_compra.php?folio=<?=str_pad($row['id_comprasprov'], 6, '0', STR_PAD_LEFT)?>">
    <button class='btn btn-info'>Ticket</button>
</a>
<?php if($row['estado'] == 'activa'){ ?>
                <button class='btn btn-danger cancelar' idcompra="<?=$row['id_comprasprov']?>" folio="<?=str_pad($row["id_comprasprov"], 6, "0", STR_PAD_LEFT)?>" title='Cancelar'>Cancelar</button>
            <?php } ?>
        </td>
    </tr>
<?php
    }
    exit();
}

if($_POST['funcion']=='Eliminar'){
    include('funciones/conectar.php');

    $id_compra = filter_var($_POST['idcompra'], FILTER_VALIDATE_INT);
    if($id_compra === false || $id_compra <= 0){
        echo json_encode(["status" => "4"]); // ID inválido
        exit();
    }

    $stmt = $consulta->prepare("SELECT id_comprasprov, estado FROM compras_prov WHERE id_comprasprov = :id");
    $stmt->bindParam(':id', $id_compra, PDO::PARAM_INT);
    $stmt->execute();

    $compra = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$compra){
        echo json_encode(["status" => "3"]); // Compra no existe
        exit();
    }

    if($compra['estado'] == 'activa'){
        $stmt = $consulta->prepare("UPDATE compras_prov SET estado = 'cancelada' WHERE id_comprasprov = :id");
        $stmt->bindParam(':id', $id_compra, PDO::PARAM_INT);
        $update = $stmt->execute();

        if($update){
            echo json_encode(["status" => "1", "folio" => str_pad($compra['id_comprasprov'], 6, "0", STR_PAD_LEFT)]);
        } else {
            echo json_encode(["status" => "0"]); // Error al actualizar
        }
    } else {
        echo json_encode(["status" => "2"]); // Ya cancelada
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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

        .card-header {
            background-color: #6A0572;
            color: white;
        }
        .table th {
            background-color: #6A0572;
            color: white;
        }
        .bg-danger {
            background-color: #f8d7da !important;
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
</style>
</head>
<body class="container mt-4">

<!-- Botón flotante mejorado -->
<a href="#" onclick="confirmarRegreso(); return false;" 
   class="floating-btn btn btn-primary btn-lg rounded-circle">
    <i class="bi bi-house-door fs-4"></i>
</a>
<!-- Función personalizada de regreso -->
<script>
function confirmarRegreso() {
    Swal.fire({
        title: "¿Salir de consulta de compras?",
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

<div class="card">
    <div class="card-header">
        <h2 class="text-center">Consulta de compras</h2>
    </div>
    <div class="card-body">
        <?php
        include("funciones/conectar.php");
        if(isset($_GET['fechainicial'])){$fechainicial = $_GET['fechainicial'];}else{$fechainicial = date("Y-m-d");}
        if(isset($_GET['fechafinal'])){$fechafinal = $_GET['fechafinal'];}else{$fechafinal = date("Y-m-d");}
        if(isset($_GET['id_proveedor'])){$idproveedor = $_GET['id_proveedor'];}else{$idproveedor = '';}
        ?>
        
        <div class="row mb-4">
            <div class="col-md-3">
                <label class="form-label"><b>Fecha Inicial</b></label>
                <input type="date" class="form-control" id="fechainicial" value="<?=$fechainicial?>">
            </div>
            <div class="col-md-3">
                <label class="form-label"><b>Fecha Final</b></label>
                <input type="date" class="form-control" id="fechafinal" value="<?=$fechafinal?>">
            </div>
            <div class="col-md-6">
                <label class="form-label"><b>Proveedor</b></label>
                <input list="datosProveedores" name="" autocomplete="off" value="<?=$idproveedor?>" class="form-control" id="proveedores" placeholder="Buscar proveedores">
                <datalist id="datosProveedores">
                <?php
                    $Auto = $consulta->query("SELECT * FROM proveedores ");
                    foreach ($Auto as $proveedor){
                        echo "<option value ='$proveedor[id_proveedor]-$proveedor[nombre]'>";
                    }
                ?>
                </datalist>
            </div>
        </div>

        <div class="table-responsive">
            <table id="tabla_compras" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Usuario</th>
                        <th>Importe</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody id="resultados_compras">
                    <tr>
                        <td colspan="6">Sin Resultados</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(e) {
    Carga_Compras();

    $(document).on("change","#fechainicial, #fechafinal, #proveedores",function(){
        var ruta = '<?=$_SERVER["REQUEST_URI"];?>';
        ruta = ruta.split("?");
        window.location = "ver_compras.php?fechainicial="+$("#fechainicial").val()+"&fechafinal="+$("#fechafinal").val()+"&id_proveedor="+$("#proveedores").val();
    });

    function Carga_Compras(){
        $.ajax({
            type: "POST",
            url: "ver_compras.php",
            data: {
                funcion: "Carga_Compras",
                fechai: $("#fechainicial").val(),
                fechaf: $("#fechafinal").val(),
                proveedor: $("#proveedores").val()
            },
            dataType: "html",
            success: function(msg){
                $("#resultados_compras").html(msg);
                $('#tabla_compras').DataTable({
                    order: [[0, 'desc']],
                    language: {
                        processing: "Procesando...",
                        search: "Buscar:",
                        lengthMenu: "Mostrar _MENU_ registros",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        infoEmpty: "Mostrando 0 a 0 de 0 registros",
                        infoFiltered: "(filtrado de _MAX_ registros totales)",
                        zeroRecords: "No se encontraron resultados",
                        emptyTable: "No hay datos disponibles en la tabla",
                        paginate: {
                            first: "Primero",
                            previous: "Anterior",
                            next: "Siguiente",
                            last: "Último"
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar compras:", error);
            }
        });
    }

    $(document).on("click",".cancelar",function(){
        var idcompra = $(this).attr("idcompra");
        var folio = $(this).attr("folio");
        var boton = $(this);
        
        Swal.fire({
            title: "¿Cancelar compra "+folio+"?",
            text: "¡Esta acción no se puede deshacer!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#6A0572",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, cancelar",
            cancelButtonText: "No, mantener"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "ver_compras.php",
                    data: {
                        funcion: "Eliminar",
                        idcompra: idcompra
                    },
                    dataType: "json",
                    success: function(response){
                        if(typeof response === 'object' && response.status === "1") {
                            Swal.fire({
                                title: "¡Cancelada!",
                                html: "La compra <strong>"+response.folio+"</strong> ha sido cancelada.",
                                icon: "success",
                                confirmButtonColor: "#6A0572"
                            }).then(() => {
                                boton.closest('tr').addClass('bg-danger');
                                boton.remove();
                                $('#tabla_compras').DataTable().draw();
                            });
                        } else if(response.status === "2") {
                            Swal.fire({
                                title: "¡Atención!",
                                text: "La compra "+folio+" ya estaba cancelada.",
                                icon: "warning",
                                confirmButtonColor: "#6A0572"
                            });
                        } else if(response.status === "3") {
                            Swal.fire({
                                title: "¡Error!",
                                html: "La compra <strong>"+folio+"</strong> no existe en la base de datos.",
                                icon: "error",
                                confirmButtonColor: "#6A0572"
                            });
                        } else if(response.status === "4") {
                            Swal.fire({
                                title: "¡Error!",
                                text: "El ID de compra proporcionado no es válido.",
                                icon: "error",
                                confirmButtonColor: "#6A0572"
                            });
                        } else {
                            Swal.fire({
                                title: "¡Error!",
                                text: "No se pudo cancelar la compra. Código: "+response.status,
                                icon: "error",
                                confirmButtonColor: "#6A0572"
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: "¡Error!",
                            text: "Error en la conexión: "+error,
                            icon: "error",
                            confirmButtonColor: "#6A0572"
                        });
                    }
                });
            }
        });
    });
 });
</script>
<script>
    $(document).ready(function() {
        $('#tabla_datos').DataTable({
            language: {
                "decimal": "",
                "emptyTable": "No hay datos disponibles en la tabla",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
                "infoFiltered": "(filtrado de _MAX_ entradas totales)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron registros coincidentes",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "aria": {
                    "sortAscending": ": activar para ordenar la columna ascendente",
                    "sortDescending": ": activar para ordenar la columna descendente"
                }
            }
        });
    });
</script>
</body>
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