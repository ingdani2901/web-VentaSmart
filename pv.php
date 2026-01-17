<?php
include("funciones/conectar.php");
if (!isset($_SESSION['id_usuario'])) {
    // Si no ha iniciado sesión, redirigir al login
    header("Location: login.php");
    exit;
}


function FolioVenta(){
    include "funciones/conectar.php";
    $Auto = $consulta->query("SELECT MAX(id_ventas)+1 AS autoincrement  FROM ventas");
    foreach ($Auto as $row);

    if($row['autoincrement']==""){
        $folio = 1;
    }else{   
        $folio = $row['autoincrement'];
    }  
    return $folio;
}



// Configura esto según lo que encuentres en el print_r($_SESSION)
$cajero = "Usuario."; // Valor por defecto

if(isset($_SESSION['nombre'])) {
    $cajero = $_SESSION['nombre'];
} 

// Puedes agregar más condiciones según necesites

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuario: <?=$cajero?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/jquery-3.7.1.min"></script>
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
        .header {
            background: #8e24aa;
            color: white;
            padding: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header .logo {
            width: 150px;
        }
        .venta-container {
            display: flex;
            margin-top: 15px;
        }
        .detalle-venta {
            flex: 3;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        .resumen-venta {
            flex: 1;
            padding: 10px;
            background: #ba68c8;
            color: white;
            border-radius: 8px;
            margin-left: 10px;
        }
        .resumen-venta h3 {
            font-size: 24px;
        }
        .acciones button {
            width: 100%;
            margin-bottom: 5px;
        }
        /* Estilos para los nuevos botones */
#Venta_Espera {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #212529;
}

#Recuperar_Venta {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

/* Espaciado entre botones */
.acciones button {
    margin-bottom: 5px;
}
        .card-header {
            background: #9c27b0;
            color: white;
        }
        .header {
    justify-content: space-between; /* Mantiene elementos separados a los extremos */
    padding: 15px; /* Aumentar padding para mejor espaciado */
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
<body>
<div class="header">
        <!-- Sección movida: Información de fecha, hora y usuario ahora a la izquierda -->
        <div style="text-align: left;">
            <h5>Fecha: <span id="fecha"></span></h5>
            <h5>Hora: <span id="hora"></span></h5>
            <h5>Cajero: <strong><?php echo htmlspecialchars($cajero); ?></strong></h5>
        </div>

        <!-- Sección movida: Casita ahora a la derecha -->
<!-- Botón flotante mejorado -->
<a href="#" onclick="confirmarRegreso(); return false;" 
   class="floating-btn btn btn-primary btn-lg rounded-circle">
    <i class="bi bi-house-door fs-4"></i>
</a>
    </div>
        <script>
function confirmarRegreso() {
    Swal.fire({
        title: "¿Salir de ventas?",
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
        
    <div class="container mt-3">
        <div class="venta-container">
            <div class="detalle-venta">
                <h4>Punto de venta</h4>
                <div class="row">
                <div class="col-md-6">
    <label for="clientes">Clientes:</label>
    <input list="datosClientes" name="" autocomplete="off" class="form-control form-control-sm" id="clientes" placeholder="Buscar clientes" value="0-Venta de mostrador">
    <datalist id="datosClientes">
        <option value="0-Venta de mostrador">
        <?php
            $Auto = $consulta->query("SELECT * FROM clientes ");
            foreach ($Auto as $producto){
                echo "<option value='$producto[id_cliente]-$producto[nombre]'>";
            }
        ?>
    </datalist>
</div>
                    <div class="col-md-4">
                        <label for="productos">Productos:</label>
                        <input list="datosProductos" name="" autocomplete="off" class="form-control form-control-sm" id="productos" placeholder="Buscar Productos">
                        <datalist id="datosProductos">
                            <?php
                            $Auto = $consulta->query("SELECT * FROM productos WHERE productos.baja_alta LIKE 'activo'");
                            foreach ($Auto as $producto){
                                echo "<option value ='$producto[codigo]-$producto[nombre]'>";
                            }
                            ?>
                        </datalist>
                    </div>
                    <div class="col-md-2">
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" class="form-control form-control-sm" id="cantidad" value="1" >
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <button class="btn btn-success" id="agregar_producto">Agregar Producto</button>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Cantidad</th>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>P. Venta</th>
                                    <th>Subtotal</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla_detalle">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="resumen-venta">
    <h3>Total a pagar</h3>
    <!-- Folio de venta -->
    <div class="mb-2">
        <label>Folio Venta:</label>
        <input type="text" class="form-control form-control-sm" id="folio" value="<?=str_pad(FolioVenta(), 4, "0", STR_PAD_LEFT);?>" readonly>
    </div>
    
    <!-- Total, Efectivo y Cambio -->
    <div class="mb-3">
        <div class="d-flex justify-content-between mb-2">
            <h5>Total:</h5>
            <h4 id="total">$0.00</h4>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <h5>Efectivo:</h5>
            <input type="text" id="efectivo" class="form-control form-control-sm w-50" value="0">
        </div>
        <div class="d-flex justify-content-between mb-3">
            <h5>Cambio:</h5>
            <h4 id="cambio">$0.00</h4>
        </div>
    </div>

    <!-- Botones de acciones -->
    <div class="acciones">
        <button class="btn btn-success" id="Guardar_Venta">Guardar Venta</button>
        <button class="btn btn-warning" id="Venta_Espera">
        <i class="fas fa-pause"></i> Venta en Espera
    </button>
    <button class="btn btn-info" id="Recuperar_Venta" style="display:none;">
        <i class="fas fa-play"></i> Recuperar Venta
    </button>
  
        <button class="btn btn-danger" id="Cancelar_Venta">Cancelar Venta</button>
    </div>
</div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Actualizar fecha y hora
        function actualizarFechaHora() {
            const fecha = new Date();
            document.getElementById("fecha").innerText = fecha.toLocaleDateString();
            document.getElementById("hora").innerText = fecha.toLocaleTimeString();
        }
        setInterval(actualizarFechaHora, 1000);
        actualizarFechaHora();

        $(document).ready(function(){
            var bandera = true;
            $(window).on('beforeunload', function (e) {
            
            });

$(document).on("change", "#efectivo", async function(){
    var total = Quita_Moneda($("#total").text());
    var efectivo = Quita_Moneda($(this).val());
    
    if(efectivo < total){
        await Swal.fire({
            icon: 'error',
            title: 'Error de pago',
            text: 'El efectivo no puede ser menor al total',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#d33',
            backdrop: 'rgba(0,0,0,0.4)'
        });
        
        $(this).val(Formato_Moneda(total, 2));
        $("#cambio").text("$"+Formato_Moneda(0,2));
    }else{
        $("#cambio").text("$"+Formato_Moneda(efectivo-total,2));
    }
});
            $(document).on("keypress", "#productos", function(e){
                if(e.which==13){
                    Agregar_Producto();
                }
            });
async function Agregar_Producto(event) {
    // Prevenir comportamiento por defecto si es un evento
    if (event) event.preventDefault();

    // Validación de cantidad
    if ($("#cantidad").val() === "") {
        await Swal.fire({
            icon: 'warning',
            title: 'Campo obligatorio',
            text: 'El campo Cantidad es obligatorio',
            confirmButtonText: 'Aceptar'
        });
        $("#cantidad").focus();
        return false;
    }
if (parseFloat($("#cantidad").val()) <= 0) {
    await Swal.fire({
        icon: 'error',
        title: 'Cantidad inválida',
        text: 'El campo Cantidad debe ser mayor a cero',
        confirmButtonText: 'Aceptar'
    });
    $("#cantidad").focus();
    return false;
}

    // Validación de productos
    if ($("#productos").val() === "") {
        await Swal.fire({
            icon: 'warning',
            title: 'Campo obligatorio',
            text: 'El campo Producto es obligatorio',
            confirmButtonText: 'Aceptar'
        });
        $("#productos").focus();
        return false;
    }
                var bandera = false;
                var codigo = $("#productos").val().split("-")[0];
                var cantidad = $("#cantidad").val();
                $("#tabla_detalle tr").each(function(){
                    if($(this).find("td:eq(1)").text()==codigo){
                        cantidad = parseInt($(this).find("td:eq(0)").find("input").val())+1;
                        $(this).find("td:eq(0)").find("input").val(cantidad);
                        $(this).find("td:eq(4)").text("$"+Formato_Moneda(cantidad*Quita_Moneda($(this).find("td:eq(3)").text()),2));
                        SumarTotal();
                        bandera = true;
                        return false;
                    }
                });
                if(bandera==false){
                    $.ajax({
                        url: 'funciones/pv.php',
                        type: 'POST',
                        data: {funcion: 'Agregar', codigo: codigo, cantidad: cantidad},
                        success: function(response){
                            $("#tabla_detalle").append(response);
                            $("#cantidad").val("1");
                            $("#productos").val("");
                            SumarTotal();
                        }
                    });
                }else{
                    $("#cantidad").val("1");
                    $("#productos").val("");
                }
            }
            // Variable global para almacenar ventas en espera
var ventasEnEspera = [];

$(document).on("click", "#Venta_Espera", function() {
    if($("#tabla_detalle tr").length === 0) {
        Swal.fire('Venta vacía', 'No hay productos para poner en espera', 'info');
        return;
    }

    // Crear objeto con los datos de la venta actual
    var ventaActual = {
        cliente: $("#clientes").val(),
        productos: [],
        total: $("#total").text(),
        efectivo: $("#efectivo").val(),
        cambio: $("#cambio").text()
    };

    // Recoger todos los productos de la tabla
    $("#tabla_detalle tr").each(function() {
        ventaActual.productos.push({
            cantidad: $(this).find(".cantidad").val(),
            codigo: $(this).find("td:eq(1)").text(),
            nombre: $(this).find("td:eq(2)").text(),
            precio: $(this).find("td:eq(3)").text(),
            subtotal: $(this).find("td:eq(4)").text()
        });
    });

    // Guardar en el array de ventas en espera
    ventasEnEspera.push(ventaActual);
    
    // Mostrar confirmación
    Swal.fire({
        title: 'Venta pausada',
        html: `Venta guardada en espera #${ventasEnEspera.length}<br>
               Total: <strong>${ventaActual.total}</strong>`,
        icon: 'success',
        timer: 2000
    });

    // Reiniciar la venta actual
    reiniciarVentaCompleta();
    
    // Mostrar botón de recuperar si hay ventas en espera
    if(ventasEnEspera.length > 0) {
        $("#Recuperar_Venta").show();
    }
});

$(document).on("click", "#Recuperar_Venta", function() {
    if(ventasEnEspera.length === 0) {
        Swal.fire('No hay ventas', 'No hay ventas en espera', 'info');
        return;
    }

    // Mostrar selector de ventas en espera
    let options = '';
    ventasEnEspera.forEach((venta, index) => {
        options += `<option value="${index}">Venta ${index+1} - ${venta.cliente.split('-')[1]} - ${venta.total}</option>`;
    });

    Swal.fire({
        title: 'Seleccione venta a recuperar',
        html: `<select id="selectVentaEspera" class="form-control">${options}</select>`,
        showCancelButton: true,
        confirmButtonText: 'Recuperar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const selectedIndex = $('#selectVentaEspera').val();
            return { index: selectedIndex };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const venta = ventasEnEspera[result.value.index];
            
            // Restaurar los valores
            $("#clientes").val(venta.cliente);
            
            // Limpiar tabla antes de agregar productos
            $("#tabla_detalle").empty();
            
            // Agregar productos a la tabla
            venta.productos.forEach(producto => {
                $("#tabla_detalle").append(`
                    <tr>
                        <td><input type="number" class="form-control form-control-sm cantidad" value="${producto.cantidad}"></td>
                        <td>${producto.codigo}</td>
                        <td>${producto.nombre}</td>
                        <td>${producto.precio}</td>
                        <td>${producto.subtotal}</td>
                        <td><button class="btn btn-danger btn-sm eliminar"><i class="fas fa-trash"></i></button></td>
                    </tr>
                `);
            });
            
            // Restaurar totales
            $("#total").text(venta.total);
            $("#efectivo").val(venta.efectivo);
            $("#cambio").text(venta.cambio);
            
            // Eliminar la venta del array de espera
            ventasEnEspera.splice(result.value.index, 1);
            
            // Ocultar botón si no hay más ventas
            if(ventasEnEspera.length === 0) { 
                $("#Recuperar_Venta").hide();
            }
            
            Swal.fire('Venta recuperada', 'Puede continuar con la venta', 'success');
        }
    });
});
            $(document).on("click", "#Cancelar_Venta", function(){
    // Verificar si hay productos antes de mostrar la alerta
    if($("#tabla_detalle tr").length === 0 && $("#total").text() === "$0.00") {
        Swal.fire(
            'No hay venta activa',
            'No hay productos para cancelar',
            'info'
        );
        return;
    }

    Swal.fire({
        title: '¿Cancelar venta completa?',
        html: `<p>Esta acción reiniciará completamente la venta actual.</p>
              <p>Total actual: <strong>${$("#total").text()}</strong></p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash"></i> Sí, cancelar',
        cancelButtonText: '<i class="fas fa-times"></i> No, conservar',
        reverseButtons: true,
        backdrop: `
            rgba(0,0,0,0.7)
            url("https://i.gifer.com/7efs.gif")
            left top
            no-repeat
        `
    }).then((result) => {
        if (result.isConfirmed) {
            reiniciarVentaCompleta();
        }
    });
});

// Función para reinicio completo (igual que antes)
function reiniciarVentaCompleta() {
    // 1. Limpiar la tabla de productos con animación
    $("#tabla_detalle tr").fadeOut(300, function() {
        $(this).remove();
    });
    
    // 2. Resetear todos los valores numéricos
    $("#total").text("$0.00");
    $("#efectivo").val("0");
    $("#cambio").text("$0.00");
    
    // 3. Resetear campos de entrada
    $("#productos").val("").focus();
    $("#cantidad").val("1");
    
    // 4. Resetear cliente a "Venta de mostrador"
    $("#clientes").val("0-Venta de mostrador");
    
    // 5. Notificación de éxito con el icono de reinicio
    Swal.fire({
        position: 'center',
        icon: 'success',
        title: 'Venta reiniciada',
        html: '<div class="text-center"><i class="fas fa-redo fa-3x mb-3" style="color:#28a745;"></i><br>Todo se ha restablecido</div>',
        showConfirmButton: false,
        timer: 1500,
        backdrop: `
            rgba(0,0,0,0.7)
            url("https://i.gifer.com/7efs.gif")
            left top
            no-repeat
        `
    });
}

            $(document).on("click", "#Guardar_Venta", function(){
                if($("#tabla_detalle tr").length==0){
                          Swal.fire({
            icon: 'warning',
            title: '¡Atención!',
            text: 'No hay productos en la venta',
            confirmButtonText: 'Aceptar'
        });
                    return false;
                }
                var productos = [];
                $("#tabla_detalle tr").each(function(){
                    var producto = {
                        cantidad: $(this).find("td:eq(0)").find("input").val(),
                        codigo: $(this).find("td:eq(1)").text(),
                        id_producto: $(this).find("td:eq(1)").attr("idproductos"),
                        precio: Quita_Moneda($(this).find("td:eq(3)").text())
                    };
                    productos.push(producto);
                });
                $.ajax({
                    url: 'funciones/pv.php',
                    type: 'POST',
                    data: {
                        funcion: 'Guardar_Venta',
                        idclientes: $("#clientes").val().split("-")[0],
                        total: Quita_Moneda($("#total").text()),
                        efectivo: Quita_Moneda($("#efectivo").val()),
                        cambio: Quita_Moneda($("#cambio").text()),
                        Detalle: productos},
                        success: function(response){
console.log("Respuesta del servidor:", response);
if (response > 1) {
    Swal.fire({
        icon: 'success',
        title: '¡Venta guardada!',
        text: 'La venta se guardó correctamente con folio ' + response,
        confirmButtonText: 'Aceptar'
    }).then(() => {
        bandera = false;
        var bob = window.open('', '_blank');
        bob.location = "ticket_venta.php?folio=" + response;
        location.reload();
    });
} else {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error al guardar la venta',
        confirmButtonText: 'Reintentar'
    });
}
                        }

                });
            });

            $(document).on("click", "#agregar_producto", function(){
                Agregar_Producto();
            });

            function SumarTotal(){
                var total = 0;
                $("#tabla_detalle tr").each(function(){
                    total += Quita_Moneda($(this).find("td:eq(4)").text());
                });
                $("#total").text("$"+Formato_Moneda(total,2));
            }

$(document).on("click", ".eliminar", function(e) {
    e.preventDefault(); // Previene acciones no deseadas

    const fila = $(this).closest("tr"); // Guarda la fila a eliminar

    Swal.fire({
        title: '¿Estás seguro?',
        text: "El producto será eliminado de la lista.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fila.remove();       // Elimina la fila
            SumarTotal();        // Actualiza el total
        }
    });
});

            $(document).on("change", ".cantidad", function(){
                var cantidad = $(this).val();
                if(cantidad=="" || cantidad<=0){
                    cantidad = 1;
                    $(this).val(1);
                }
                var precio = Quita_Moneda($(this).parent().parent().find("td:eq(3)").text());
                $(this).parent().parent().find("td:eq(4)").text("$"+Formato_Moneda(cantidad*precio,2));
                SumarTotal();
            });

            function Formato_Moneda(n, c, d, t) {
                var c = isNaN(c = Math.abs(c)) ? 2 : c,
                    d = d == undefined ? "." : d,
                    t = t == undefined ? "," : t,
                    s = n < 0 ? "-" : "",
                    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                    j = (j = i.length) > 3 ? j % 3 : 0;
                return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
            }

            function Quita_Moneda(n) {
                n = String(n);
                var s = parseFloat(n.replace(",", "").replace("$", ""));
                if (isNaN(s)) s = 0;
                return s;
            }
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