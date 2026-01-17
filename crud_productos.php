<?php
session_start();
// Incluir la conexión a la base de datos
include 'funciones/db.php';
if (!isset($_SESSION['id_usuario'])) {
// Si no ha iniciado sesión, redirigir al login
header("Location: login.php");
exit;
}

// Obtener categorías
$categoriasResult = $conn->query("SELECT id_categoria, nombre FROM categorias");
$categorias = [];

while ($row = $categoriasResult->fetch_assoc()) {
$categorias[] = $row;
}

// Buscar productos
$search = "";
if (isset($_GET["buscar"])) {
$search = $conn->real_escape_string($_GET["buscar"]);
$sql = "SELECT * FROM productos WHERE nombre LIKE '%$search%'";
} else {
$sql = "SELECT * FROM productos";
}

$result = $conn->query($sql);

// Procesamiento del formulario
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
if (isset($_POST["guardar"])) {
$nombre = $conn->real_escape_string($_POST["nombre"]);
$codigo = $conn->real_escape_string($_POST["codigo"]);
$categoria = intval($_POST["categoria"]);
$precio = floatval($_POST["precio"]);
$precio_compra = floatval($_POST["precio_compra"]);
$cantidad = floatval($_POST["cantidad"]);
$stock = intval($_POST["stock"]);
$baja_alta = $conn->real_escape_string($_POST["baja_alta"]);

// Verificar si el código del producto ya existe
$checkSql = "SELECT id_producto FROM productos WHERE codigo = '$codigo'";
$checkResult = $conn->query($checkSql);
if ($checkResult->num_rows > 0) {
$error = "El código del producto ya existe.";
echo "<script>
Swal.fire({
icon: 'error',
title: 'Error',
text: 'El código del producto ya existe.',
confirmButtonColor: '#6A0572',
});
</script>";
} else {
$sql = "INSERT INTO productos (nombre, codigo, id_categoria, precio,
precio_compra, cantidad, stock, baja_alta)

VALUES ('$nombre', '$codigo', $categoria, $precio, $precio_compra,

$cantidad, $stock, '$baja_alta')";
if ($conn->query($sql)) {
header("Location: crud_productos.php?action=added");
exit();
} else {
$error = "Error al guardar el producto.";
}
}
}

// Editar producto
if (isset($_POST["actualizar"])) {
$id_producto = intval($_GET['editar']);
$nombre = $conn->real_escape_string($_POST["nombre"]);
$codigo = $conn->real_escape_string($_POST["codigo"]);
$categoria = intval($_POST["categoria"]);
$precio = floatval($_POST["precio"]);
$precio_compra = floatval($_POST["precio_compra"]);
$cantidad = floatval($_POST["cantidad"]);
$stock = intval($_POST["stock"]);
$baja_alta = $conn->real_escape_string($_POST["baja_alta"]);

// Verificar si el código del producto ya existe (excluyendo el producto actual)
$checkSql = "SELECT id_producto FROM productos WHERE codigo = '$codigo'
AND id_producto != $id_producto";
$checkResult = $conn->query($checkSql);
if ($checkResult->num_rows > 0) {
$error = "El código del producto ya existe.";
echo "<script>
Swal.fire({
icon: 'error',
title: 'Error',
text: 'El código del producto ya existe.',
confirmButtonColor: '#6A0572',
});
</script>";
} else {

$updateSql = "UPDATE productos SET
nombre = '$nombre',
codigo = '$codigo',
id_categoria = $categoria,
precio = $precio,
precio_compra = $precio_compra,
cantidad = $cantidad,
stock = $stock,
baja_alta = '$baja_alta'
WHERE id_producto = $id_producto";
if ($conn->query($updateSql)) {
header("Location: crud_productos.php?action=updated");
exit();
} else {
$error = "Error al actualizar el producto.";
}
}
}
}

// Eliminar producto
if (isset($_GET['eliminar'])) {
$id_producto = intval($_GET['eliminar']);
$conn->query("DELETE FROM productos WHERE id_producto = $id_producto");
header("Location: crud_productos.php?action=deleted");
exit();
}

// Obtener producto para editar
if (isset($_GET['editar'])) {
$id_producto = intval($_GET['editar']);
$productoResult = $conn->query("SELECT * FROM productos WHERE id_producto
= $id_producto");
$producto = $productoResult->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestión de Productos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
rel="stylesheet">
<link rel="stylesheet"
href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-
rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=s
wap" rel="stylesheet">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-
icons@1.11.3/font/bootstrap-icons.min.css">

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
.d-flex {
display: flex;
gap: 10px;
}
.form-label {
font-weight: 500;
}
.card {
background: rgba(255, 255, 255, 0.9);
backdrop-filter: blur(12px);
border-radius: 15px;
border: 1px solid rgba(255, 255, 255, 0.2);
box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
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
<h2 class="text-center text-primary font-weight-bold">Gestión de productos</h2>

<?php if ($error): ?>
<div class="alert alert-danger"> <?= $error ?> </div>
<?php endif; ?>

<!-- Formulario para agregar o editar producto -->
<div class="card p-3 mb-4">
<form method="post">
<div class="mb-3 row">
<div class="col-md-2">
<label class="form-label">Código:</label>
<input type="text" class="form-control" name="codigo" value="<?=
isset($producto) ? htmlspecialchars($producto['codigo']) : '' ?>" required>

</div>
<div class="col-md-3">
<label class="form-label">Nombre:</label>
<input type="text" class="form-control" name="nombre" value="<?=
isset($producto) ? htmlspecialchars($producto['nombre']) : '' ?>" required>

</div>
<div class="col-md-2">
<label class="form-label">Categoría:</label>
<select class="form-control select2" name="categoria" id="categoria"

required>

<option value="">Seleccione una categoría</option>
<?php foreach ($categorias as $categoria): ?>
<option value="<?= $categoria['id_categoria'] ?>" <?=

isset($producto) && $producto['id_categoria'] == $categoria['id_categoria'] ? 'selected' :
'' ?>>

<?= htmlspecialchars($categoria['nombre']) ?>
</option>
<?php endforeach; ?>
</select>
</div>
<div class="col-md-2">
<label class="form-label">Precio Venta:</label>
<input type="number" step="0.01" class="form-control" name="precio"
value="<?= isset($producto) ? htmlspecialchars($producto['precio']) : '' ?>" required>

</div>
<div class="col-md-2">
<label class="form-label">Precio Compra:</label>
<input type="number" step="0.01" class="form-control"

name="precio_compra" value="<?= isset($producto) ?
htmlspecialchars($producto['precio_compra']) : '' ?>" required>

</div>
<div class="col-md-1">
<label class="form-label">Cantidad:</label>

<input type="number" step="0.01" class="form-control" name="cantidad"
value="<?= isset($producto) ? htmlspecialchars($producto['cantidad']) : '' ?>" required>

</div>
<div class="col-md-1">
<label class="form-label">Stock:</label>
<input type="number" class="form-control" name="stock" value="<?=

isset($producto) ? htmlspecialchars($producto['stock']) : '' ?>" required>

</div>
<div class="col-md-1">
<label class="form-label">Estado:</label>
<select class="form-control" name="baja_alta" required>
<option value="activo" <?= isset($producto) && $producto['baja_alta']

== 'activo' ? 'selected' : '' ?>>Activo</option>

<option value="inactivo" <?= isset($producto) && $producto['baja_alta']

== 'inactivo' ? 'selected' : '' ?>>Inactivo</option>

</select>
</div>
</div>
<div class="d-flex justify-content-center gap-2 mt-3">
<button type="submit" class="btn btn-success" name="<?= isset($producto)

? 'actualizar' : 'guardar' ?>">

<?= isset($producto) ? 'Actualizar' : 'Guardar' ?>
</button>
<?php if (isset($producto)): ?>
<a href="crud_productos.php" class="btn btn-secondary">Cancelar</a>
<?php endif; ?>
</div>
</form>
</div>

<!-- Tabla de productos -->
<table class="table table-bordered text-center compact table-primary"
id="tabla_datos">
<thead class="table-dark">
<tr>
<th>Código</th>
<th>Nombre</th>
<th>Categoría</th>
<th>Precio Venta</th>
<th>Precio Compra</th>
<th>Cantidad</th>
<th>Stock</th>
<th>Estado</th>
<th width="150">Acciones</th>
</tr>
</thead>
<tbody>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row["codigo"]) ?></td>
<td><?= htmlspecialchars($row["nombre"]) ?></td>
<td>
<?php
$categoriaId = $row["id_categoria"];
$categoriaNombre = "Desconocida";
foreach ($categorias as $categoria) {

if ($categoria["id_categoria"] == $categoriaId) {
$categoriaNombre = $categoria["nombre"];
break;
}
}
echo htmlspecialchars($categoriaNombre);
?>
</td>
<td><?= htmlspecialchars($row["precio"]) ?></td>
<td><?= htmlspecialchars($row["precio_compra"]) ?></td>
<td><?= htmlspecialchars($row["cantidad"]) ?></td>
<td><?= htmlspecialchars($row["stock"]) ?></td>
<td><?= htmlspecialchars($row["baja_alta"]) ?></td>
<td>
<a href="#" onclick="confirmarEdicion(<?= $row['id_producto'] ?>);

return false;" class="btn btn-warning btn-sm">Editar</a>

<a href="#" onclick="confirmarEliminacion(<?= $row['id_producto'] ?>);

return false;" class="btn btn-danger btn-sm">Eliminar</a>

</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<!-- Botón flotante mejorado -->
<a href="#" onclick="confirmarRegreso(); return false;"
class="floating-btn btn btn-primary btn-lg rounded-circle">
<i class="bi bi-house-door fs-4"></i>

</a>

<script>
$(document).ready(function() {
// Inicializa Select2 en el campo de categoría
$('#categoria').select2({
placeholder: "Buscar categoría...",
allowClear: true,
width: '100%'
});

// Inicializa DataTables en la tabla de productos
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

function confirmarRegreso() {
Swal.fire({
title: "¿Salir de productos?",
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

function confirmarEliminacion(id) {
Swal.fire({
title: "¿Estás seguro?",
text: "¡No podrás revertir esta acción!",
icon: "warning",
showCancelButton: true,
confirmButtonColor: "#6A0572",
cancelButtonColor: "#d33",
confirmButtonText: "Sí, eliminar",
cancelButtonText: "No, cancelar"
}).then((result) => {
if (result.isConfirmed) {
window.location.href = `crud_productos.php?eliminar=${id}`;
}
});

}

function confirmarEdicion(id) {
Swal.fire({
title: "¿Editar este producto?",
text: "Serás redirigido a la página de edición.",
icon: "question",
showCancelButton: true,
confirmButtonColor: "#6A0572",
cancelButtonColor: "#d33",
confirmButtonText: "Sí, editar",
cancelButtonText: "No, cancelar"
}).then((result) => {
if (result.isConfirmed) {
window.location.href = `crud_productos.php?editar=${id}`;
}
});
}

// Mostrar mensajes de SweetAlert2 según la acción realizada
const urlParams = new URLSearchParams(window.location.search);
const action = urlParams.get('action');

if (action === 'added') {
Swal.fire({
title: '¡Agregado!',
text: 'El producto ha sido agregado con éxito.',

icon: 'success',
confirmButtonColor: "#6A0572",
});
} else if (action === 'updated') {
Swal.fire({
title: '¡Actualizado!',
text: 'El producto ha sido actualizado con éxito.',
icon: 'success',
confirmButtonColor: "#6A0572",
});
} else if (action === 'deleted') {
Swal.fire({
title: '¡Eliminado!',
text: 'El producto ha sido eliminado con éxito.',
icon: 'success',
confirmButtonColor: "#6A0572",
});
}
</script>
<?php if ($producto_duplicado): ?>
<script>
Swal.fire({
icon: 'error',
title: 'Producto duplicado',
text: 'El código del producto ya existe. Por favor, usa uno diferente.',
confirmButtonColor: '#6A0572',
});

</script>
<?php endif; ?>

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