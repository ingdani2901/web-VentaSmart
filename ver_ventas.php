<?php
session_start();
// Incluir la conexión a la base de datos
if (!isset($_SESSION['id_usuario'])) {
    // Si no ha iniciado sesión, redirigir al login
    header("Location: login.php");
    exit;
}
if($_POST['funcion']=='Carga_Ventas'){
    include("funciones/conectar.php");
    $tipo = '';
    $cliente = '';
    if($_POST['cliente']!=''){
        $clien = explode("-",$_POST['cliente']);
        $cliente = ' AND id_cliente='. $clien[0];
    }
    $cancelado = '';
    $resultados=$consulta->query("SELECT ventas.*, clientes.nombre AS cliente, usuarios.nombre AS usuario FROM ventas LEFT JOIN clientes ON clientes.id_cliente=ventas.id_cliente LEFT JOIN usuarios ON usuarios.id_usuario=ventas.id_usuario WHERE fecha BETWEEN '".$_POST['fechai']." 00:00:00' AND '".$_POST['fechaf']." 23:59:59' $cliente ORDER BY ventas.fecha DESC");
    foreach ($resultados as $row) {
        $cancelado = ($row['fechacancelada'] != '') ? 'bg-danger' : '';
    
        $fecha=explode(" ",$row["fecha"]);
        $ano=explode("-",$fecha[0]);
        $mes=$ano[1];
        $dia=$ano[2];
        $horas = explode(":",$fecha[1]);
        $hora = $horas[0];
    ?>
    <tr class="<?=$cancelado?> text-uppercase">
        <td><?=str_pad($row["folio"], 6, "0", STR_PAD_LEFT)?></td>
        <td><?=$dia."/".$mes."/".$ano[0]." ".$hora.":".$horas[1].":".$horas[2]?></td>
        <td><?=$row['cliente']?></td>
        <td><?=$row['usuario']?></td>
        <td align="right">$ <?=number_format($row['total'],2)?></td>
        <td width="200">
            <a href="ticket_venta.php?folio=<?=$row['folio']?>"><button class='btn btn-info'>Ticket</button></a>
            <?php if($row['fechacancelada']==null){ ?>
                <button class='btn btn-danger cancelar' idventas="<?=$row['id_ventas']?>" folio="<?=str_pad($row["folio"], 6, "0", STR_PAD_LEFT)?>" title='Cancelar'>Cancelar</button>
            <?php } ?>
        </td>
    </tr>
<?php
    $cancelado = '';
    }
    exit();
}

if($_POST['funcion']=='Eliminar'){
    include('funciones/conectar.php');

    $id_venta = filter_var($_POST['id_ventas'], FILTER_VALIDATE_INT);
    if($id_venta === false || $id_venta <= 0){
        echo "4"; // ID inválido
        exit();
    }

    // Usar PDO correctamente
    $stmt = $consulta->prepare("SELECT id_ventas, folio, fechacancelada FROM ventas WHERE id_ventas = :id");
    $stmt->bindParam(':id', $id_venta, PDO::PARAM_INT);
    $stmt->execute();

    $venta = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$venta){
        echo "3"; // Venta no existe
        exit();
    }

    if($venta['fechacancelada'] === null){
        $stmt = $consulta->prepare("UPDATE ventas SET fechacancelada = NOW() WHERE id_ventas = :id");
        $stmt->bindParam(':id', $id_venta, PDO::PARAM_INT);
        $update = $stmt->execute();

        if($update){
            echo json_encode(["status" => "1", "folio" => $venta['folio']]);
        } else {
            echo "0"; // Error al actualizar
        }
    } else {
        echo "2"; // Ya cancelada
    }
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Ventas</title>
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
        title: "¿Salir de consulta de ventas?",
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
        <h2 class="text-center">Consulta de ventas</h2>
    </div>
    <div class="card-body">
        <?php
        include("funciones/conectar.php");
        if(isset($_GET['fechainicial'])){$fechainicial = $_GET['fechainicial'];}else{$fechainicial = date("Y-m-d");}
        if(isset($_GET['fechafinal'])){$fechafinal = $_GET['fechafinal'];}else{$fechafinal = date("Y-m-d");}
        if(isset($_GET['id_cliente'])){$idcliente = $_GET['id_cliente'];}else{$idcliente = '';}
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
                <label class="form-label"><b>Cliente</b></label>
                <input list="datosClientes" name="" autocomplete="off" value="<?=$idcliente?>" class="form-control" id="clientes" placeholder="Buscar clientes">
                <datalist id="datosClientes">
                <?php
                    $Auto = $consulta->query("SELECT * FROM clientes ");
                    foreach ($Auto as $producto){
                        echo "<option value ='$producto[id_cliente]-$producto[nombre]'>";
                    }
                ?>
                </datalist>
            </div>
        </div>

        <div class="table-responsive">
            <table id="tabla_ventas" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Usuario</th>
                        <th>Importe</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody id="resultados_productos">
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
    Carga_Entradas();

    $(document).on("change","#fechainicial, #fechafinal, #clientes",function(){
        var ruta = '<?=$_SERVER["REQUEST_URI"];?>';
        ruta = ruta.split("?");
        window.location = "ver_ventas.php?fechainicial="+$("#fechainicial").val()+"&fechafinal="+$("#fechafinal").val()+"&idcliente="+$("#clientes").val();
    });

    function Carga_Entradas(){
        $.ajax({
            type: "POST",
            url: "ver_ventas.php",
            data: {
                funcion: "Carga_Ventas",
                fechai: $("#fechainicial").val(),
                fechaf: $("#fechafinal").val(),
                cliente: $("#clientes").val()
            },
            dataType: "html",
            success: function(msg){
                $("#resultados_productos").html(msg);
                $('#tabla_ventas').DataTable({
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
                console.error("Error al cargar ventas:", error);
            }
        });
    }

    $(document).on("click",".cancelar",function(){
        var idventas = $(this).attr("idventas");
        var folio = $(this).attr("folio");
        var boton = $(this);
        
        console.log("Intentando cancelar venta ID:", idventas, "Folio:", folio);
        
        Swal.fire({
            title: "¿Cancelar venta "+folio+"?",
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
                    url: "ver_ventas.php",
                    data: {
                        funcion: "Eliminar",
                        id_ventas: idventas
                    },
                    dataType: "json",
                    success: function(response){
                        if(typeof response === 'object' && response.status === "1") {
                            Swal.fire({
                                title: "¡Cancelada!",
                                html: "La venta <strong>"+response.folio+"</strong> ha sido cancelada.",
                                icon: "success",
                                confirmButtonColor: "#6A0572"
                            }).then(() => {
                                // Actualizar solo la fila afectada
                                boton.closest('tr').addClass('bg-danger');
                                boton.remove();
                                $('#tabla_ventas').DataTable().draw();
                            });
                        } else if(response === "2") {
                            Swal.fire({
                                title: "¡Atención!",
                                text: "La venta "+folio+" ya estaba cancelada.",
                                icon: "warning",
                                confirmButtonColor: "#6A0572"
                            });
                        } else if(response === "3") {
                            Swal.fire({
                                title: "¡Error!",
                                html: "La venta <strong>"+folio+"</strong> no existe en la base de datos.",
                                icon: "error",
                                confirmButtonColor: "#6A0572"
                            });
                        } else if(response === "4") {
                            Swal.fire({
                                title: "¡Error!",
                                text: "El ID de venta proporcionado no es válido.",
                                icon: "error",
                                confirmButtonColor: "#6A0572"
                            });
                        } else {
                            Swal.fire({
                                title: "¡Error!",
                                text: "No se pudo cancelar la venta. Código: "+response,
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