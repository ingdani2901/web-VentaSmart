<?php
include("funciones/conectar.php");
session_start();
if (!isset($_SESSION['id_usuario'])) {
    // Si no ha iniciado sesión, redirigir al login
    header("Location: login.php");
    exit;
}

function FolioCompra() {
    include "funciones/conectar.php";
    $Auto = $consulta->query("SELECT MAX(id_comprasprov)+1 AS autoincrement FROM compras_prov");
    foreach ($Auto as $row);

    if($row['autoincrement']==""){
        $folio = 1;
    }else{   
        $folio = $row['autoincrement'];
    }  
    return $folio;
}

$cajero = "Usuario."; // Valor por defecto
if(isset($_SESSION['nombre'])) {
    $cajero = $_SESSION['nombre'];
} 

$porcentaje_ganancia = 30; // 30% por defecto
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compras a Proveedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
        .header .logo { width: 150px; }
        .header {
            background: #8e24aa;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .compra-container { display: flex; margin-top: 15px; }
        .detalle-compra {
            flex: 3;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
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

        .resumen-compra {
            flex: 1;
            padding: 10px;
            background: #ba68c8;
            color: white;
            border-radius: 8px;
            margin-left: 10px;
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
        .card-header { background-color: #f3e5f5; }
        .precio-venta { background-color: #e8f5e9; font-weight: bold; }
        .precio-compra { background-color: #ffebee; }
        .btn-redondear { margin-top: 28px; }
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

<!-- Botón flotante mejorado -->
<a href="#" onclick="confirmarRegreso(); return false;" 
   class="floating-btn btn btn-primary btn-lg rounded-circle">
    <i class="bi bi-house-door fs-4"></i>
</a>
    </div>
    <script>
function confirmarRegreso() {
    Swal.fire({
        title: "¿Salir de compras?",
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

</div>
<body>
    <div class="container mt-3">
        <div class="compra-container">
            <div class="detalle-compra">
                <h4>Detalle de compra</h4>
                <div class="row">
                    <div class="col-md-6">
                        <label for="proveedores">Proveedores:</label>
                        <input list="datosProveedores" name="" autocomplete="off" class="form-control form-control-sm" id="proveedores" placeholder="Buscar proveedores">
                        <datalist id="datosProveedores">
                            <?php
                            $Auto = $consulta->query("SELECT * FROM proveedores");
                            foreach ($Auto as $proveedor){
                                echo "<option value='$proveedor[id_proveedor]-$proveedor[empresa]'>";
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
                                echo "<option 
                                        value='$producto[codigo]-$producto[nombre]' 
                                        data-precio-compra='$producto[precio_compra]'
                                        data-precio-venta='$producto[precio]'>";
                            }
                            ?>
                        </datalist>
                    </div>
                    <div class="col-md-2">
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" class="form-control form-control-sm" id="cantidad" value="1" min="1">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <label for="precio_compra">Precio compra:</label>
                        <input type="number" step="0.01" class="form-control form-control-sm precio-compra" id="precio_compra">
                    </div>
                    <div class="col-md-2">
                        <label for="porcentaje_ganancia">% Ganancia:</label>
                        <input type="number" class="form-control form-control-sm" id="porcentaje_ganancia" value="<?php echo $porcentaje_ganancia; ?>" step="0.1">
                    </div>
                    <div class="col-md-3">
                        <label for="precio_venta">Precio Venta:</label>
                        <input type="number" step="0.01" class="form-control form-control-sm precio-venta" id="precio_venta">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-secondary btn-sm btn-redondear" id="redondear_precio">Redondear</button>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button class="btn btn-success btn-block" id="agregar_producto">Agregar</button>
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
                                    <th>P. Compra</th>
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
            
            <div class="resumen-compra">
                <h3>Total compra</h3>
                <div class="mb-2">
                    <label>Folio compra:</label>
                    <input type="text" class="form-control form-control-sm" id="folio" value="<?=str_pad(FolioCompra(), 4, "0", STR_PAD_LEFT);?>" readonly>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <h5>Total:</h5>
                        <h4 id="total">$0.00</h4>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="num_factura">N° Factura proveedor:</label>
                    <input type="text" class="form-control form-control-sm" id="num_factura" placeholder="Opcional">
                </div>

                <div class="acciones">
                    <button class="btn btn-success" id="Guardar_Compra">Guardar compra</button>
                    <button class="btn btn-danger" id="Cancelar_Compra">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

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
            // Mostrar precios cuando se selecciona un producto
            $(document).on("input", "#productos", function(){
                var productoSeleccionado = $("#datosProductos option[value='"+$(this).val()+"']");
                if(productoSeleccionado.length > 0) {
                    var precioCompra = parseFloat(productoSeleccionado.data('precio-compra')) || 0;
                    var precioVenta = parseFloat(productoSeleccionado.data('precio-venta')) || 0;
                    
                    $("#precio_compra").val(precioCompra.toFixed(2));
                    $("#precio_venta").val(precioVenta.toFixed(2));
                    
                    // Calcular porcentaje si el precio de compra es mayor que cero
                    if(precioCompra > 0) {
                        var porcentaje = ((precioVenta / precioCompra) - 1) * 100;
                        $("#porcentaje_ganancia").val(porcentaje.toFixed(1));
                    }
                } else {
                    $("#precio_compra").val("");
                    $("#precio_venta").val("");
                }
            });
            
            // Calcular precio de venta cuando cambia el porcentaje o el precio de compra
            $(document).on("input", "#porcentaje_ganancia, #precio_compra", function(){
                var precioCompra = parseFloat($("#precio_compra").val()) || 0;
                var porcentaje = parseFloat($("#porcentaje_ganancia").val()) || 0;
                
                if(precioCompra > 0) {
                    var precioVenta = precioCompra * (1 + (porcentaje / 100));
                    $("#precio_venta").val(precioVenta.toFixed(2));
                }
            });
            
            // Cuando se edita manualmente el precio de venta, calcular el nuevo porcentaje
            $(document).on("input", "#precio_venta", function(){
                var precioCompra = parseFloat($("#precio_compra").val()) || 0;
                var precioVenta = parseFloat($(this).val()) || 0;
                
                if(precioCompra > 0) {
                    var nuevoPorcentaje = ((precioVenta / precioCompra) - 1) * 100;
                    $("#porcentaje_ganancia").val(nuevoPorcentaje.toFixed(1));
                }
            });
            
            // Función para redondear el precio de venta
            $(document).on("click", "#redondear_precio", function(){
                var precioVenta = parseFloat($("#precio_venta").val()) || 0;
                if(precioVenta > 0) {
                    var precioRedondeado = Math.round(precioVenta);
                    $("#precio_venta").val(precioRedondeado);
                    
                    var precioCompra = parseFloat($("#precio_compra").val()) || 0;
                    if(precioCompra > 0) {
                        var nuevoPorcentaje = ((precioRedondeado / precioCompra) - 1) * 100;
                        $("#porcentaje_ganancia").val(nuevoPorcentaje.toFixed(1));
                    }
                }
            });

            // Función para agregar producto
            function Agregar_Producto(){
                // Validaciones
                if($("#cantidad").val() === "" || parseFloat($("#cantidad").val()) <= 0){
                    Swal.fire('Error', 'La cantidad debe ser mayor a 0', 'error');
                    $("#cantidad").focus();
                    return false;
                }
                if($("#productos").val() === ""){
                    Swal.fire('Error', 'Debe seleccionar un producto', 'error');
                    $("#productos").focus();
                    return false;
                }
  // Validación de PROVEEDOR (corregido)
if ($("#proveedores").val() === "") {
    Swal.fire('Error', 'Debe seleccionar un proveedor', 'error');
    $("#proveedores").focus();
    return false;
}
                if($("#precio_compra").val() === "" || parseFloat($("#precio_compra").val()) <= 0){
                    Swal.fire('Error', 'El precio de compra debe ser mayor a 0', 'error');
                    $("#precio_compra").focus();
                    return false;
                }
                if($("#precio_venta").val() === "" || parseFloat($("#precio_venta").val()) <= 0){
                    Swal.fire('Error', 'El precio de venta debe ser mayor a 0', 'error');
                    $("#precio_venta").focus();
                    return false;
                }
                
                // Obtener datos del producto
                var productoVal = $("#productos").val();
                if(productoVal.indexOf("-") === -1) {
                    Swal.fire('Error', 'Formato de producto incorrecto', 'error');
                    return false;
                }
                
                var codigo = productoVal.split("-")[0].trim();
                var nombre = productoVal.split("-")[1].trim();
                var cantidad = parseFloat($("#cantidad").val());
                var precio_compra = parseFloat($("#precio_compra").val());
                var precio_venta = parseFloat($("#precio_venta").val());
                var porcentaje = parseFloat($("#porcentaje_ganancia").val());
                var subtotal = cantidad * precio_compra;
                
                // Verificar si el producto ya está en la tabla
                var productoExistente = false;
                $("#tabla_detalle tr").each(function(){
                    var codigoExistente = $(this).find("td:eq(1)").text().trim();
                    if(codigoExistente === codigo) {
                        var inputCantidad = $(this).find(".cantidad");
                        var nuevaCantidad = parseFloat(inputCantidad.val()) + cantidad;
                        inputCantidad.val(nuevaCantidad);
                        
                        var nuevoSubtotal = nuevaCantidad * precio_compra;
                        $(this).find("td:eq(5)").text("$"+Formato_Moneda(nuevoSubtotal,2));
                        
                        productoExistente = true;
                        return false;
                    }
                });
                
                if(!productoExistente) {
                    var nuevaFila = `
                        <tr>
                            <td><input type="number" class="form-control form-control-sm cantidad" value="${cantidad}" min="1"></td>
                            <td>${codigo}</td>
                            <td>${nombre}</td>
                            <td>$${Formato_Moneda(precio_compra, 2)}</td>
                            <td data-porcentaje="${porcentaje}">$${Formato_Moneda(precio_venta, 2)}</td> 
                            <td>$${Formato_Moneda(subtotal, 2)}</td>
                            <td><button class="btn btn-danger btn-sm eliminar"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    `;
                    $("#tabla_detalle").append(nuevaFila);
                }
                
                // Limpiar campos
                $("#cantidad").val("1");
                $("#productos").val("");
                $("#precio_compra").val("");
                $("#precio_venta").val("");
                $("#productos").focus();
                
                SumarTotal();
            }

            // Evento para agregar producto con Enter
            $(document).on("keypress", "#productos, #precio_compra, #precio_venta", function(e){
                if(e.which == 13){
                    Agregar_Producto();
                }
            });

            // Evento para botón agregar
            $(document).on("click", "#agregar_producto", function(){
                Agregar_Producto();
            });

            // Función para sumar el total
            function SumarTotal(){
                var total = 0;
                $("#tabla_detalle tr").each(function(){
                    var cantidad = parseFloat($(this).find(".cantidad").val());
                    var precio = Quita_Moneda($(this).find("td:eq(3)").text());
                    total += cantidad * precio;
                });
                $("#total").text("$"+Formato_Moneda(total,2));
            }

            // Eliminar producto
            $(document).on("click", ".eliminar", function(){
                $(this).closest("tr").remove();
                SumarTotal();
            });

            // Cambiar cantidad
            $(document).on("change", ".cantidad", function(){
                var cantidad = parseFloat($(this).val()) || 1;
                if(cantidad <= 0) {
                    cantidad = 1;
                    $(this).val(1);
                }
                var precio = Quita_Moneda($(this).closest("tr").find("td:eq(3)").text());
                var subtotal = cantidad * precio;
                $(this).closest("tr").find("td:eq(5)").text("$"+Formato_Moneda(subtotal,2));
                SumarTotal();
            });

            // Cancelar compra
            $(document).on("click", "#Cancelar_Compra", function(){
                if($("#tabla_detalle tr").length === 0) {
                    Swal.fire('No hay compra activa', 'No hay productos para cancelar', 'info');
                    return;
                }

                Swal.fire({
                    title: '¿Cancelar compra completa?',
                    html: `<p>Esta acción reiniciará completamente la compra actual.</p>
                          <p>Total actual: <strong>${$("#total").text()}</strong></p>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash"></i> Sí, cancelar',
                    cancelButtonText: '<i class="fas fa-times"></i> No, conservar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        reiniciarCompraCompleta();
                    }
                });
            });

            function reiniciarCompraCompleta() {
                $("#tabla_detalle").empty();
                $("#total").text("$0.00");
                $("#productos").val("").focus();
                $("#cantidad").val("1");
                $("#precio_compra").val("");
                $("#precio_venta").val("");
                $("#num_factura").val("");
                $("#proveedores").val("");
                
                ({
                  //  position: 'center',
                  //  icon: 'success',
                  //  title: 'Compra reiniciada',
                    showConfirmButton: false,
                    timer: 1500
                });
            }

            // Guardar compra
            $(document).on("click", "#Guardar_Compra", function() {
                if ($("#tabla_detalle tr").length == 0) {
                    Swal.fire('Error', 'No hay productos en la compra', 'error');
                    return false;
                }
                if ($("#proveedores").val() == "") {
                    Swal.fire('Error', 'Debe seleccionar un proveedor', 'error');
                    return false;
                }

                var compra = {
                    proveedor: $("#proveedores").val(),
                    num_factura: $("#num_factura").val(),
                    fecha: new Date().toISOString().slice(0, 19).replace('T', ' '),
                    productos: []
                };

                $("#tabla_detalle tr").each(function() {
                    var producto = {
                        codigo: $(this).find("td:eq(1)").text(),
                        cantidad: parseFloat($(this).find(".cantidad").val()),
                        precio_compra: Quita_Moneda($(this).find("td:eq(3)").text()),
                        precio_venta: Quita_Moneda($(this).find("td:eq(4)").text()),
                        porcentaje: parseFloat($(this).find("td:eq(4)").data('porcentaje'))
                    };
                    compra.productos.push(producto);
                });

                Swal.fire({
                    title: '¿Confirmar compra?',
                    html: `<p>Proveedor: <strong>${compra.proveedor.split('-')[1]}</strong></p>
                          
                          `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, guardar compra',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
    url: 'funciones/pc.php',
    type: 'POST',
    dataType: 'json', // Especificamos que esperamos JSON
    data: { compra: JSON.stringify(compra) },
    beforeSend: function() {
        Swal.fire({
            title: 'Guardando compra...',
            html: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
    },
    success: function(response) {
        // Verificar si la respuesta es válida y completa
        if (response && response.status === "success") {
            Swal.fire({
                icon: 'success',
                title: 'Compra guardada',
                html: `<p>${response.message}</p>
                      `,
                confirmButtonText: 'Aceptar'
            }).then(() => {
                reiniciarCompraCompleta();
                // Actualizar folio directamente desde la respuesta
                $("#folio").val(response.folio);
            });
        } else {
            // Manejar respuestas inesperadas del servidor
            let errorMsg = 'Hubo un error al guardar la compra';
            if (response && response.message) {
                errorMsg += ': ' + response.message;
            } else if (response && typeof response === 'string') {
                errorMsg += ': ' + response;
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMsg,
            });
        }
    },
    error: function(xhr, status, error) {
        // Manejar errores de conexión o respuestas no JSON
        let errorMsg = 'No se pudo conectar con el servidor';
        
        try {
            // Intentar parsear respuesta de error como JSON
            const errResponse = JSON.parse(xhr.responseText);
            if (errResponse && errResponse.message) {
                errorMsg = errResponse.message;
            }
        } catch (e) {
            // Si no es JSON, mostrar el error crudo (útil para depuración)
            if (xhr.responseText) {
                errorMsg += '. Respuesta del servidor: ' + xhr.responseText;
            } else {
                errorMsg += ': ' + error;
            }
        }
        
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: errorMsg,
            confirmButtonText: 'Entendido'
        });
    }
});
                    }
                });
            });

            // Funciones de formato
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
                var s = parseFloat(n.replace(/,/g, "").replace("$", "")) || 0;
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