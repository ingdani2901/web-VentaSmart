<?php
session_start();
// Incluir la conexión a la base de datos
// Conexión a la base de datos
include 'funciones/db.php';
if (!isset($_SESSION['id_usuario'])) {
// Si no ha iniciado sesión, redirigir al login
header("Location: login.php");
exit;
}

// Inicializar variables
$search = "";
$errorMensaje = "";

// Buscar proveedores
if (isset($_GET["buscar"])) {

$search = $conn->real_escape_string($_GET["buscar"]);
$sql = "SELECT * FROM proveedores WHERE
nombre LIKE '%$search%'
OR apepat LIKE '%$search%'
OR apemat LIKE '%$search%'
OR empresa LIKE '%$search%'";
} else {
$sql = "SELECT * FROM proveedores";
}

$result = $conn->query($sql);

// Formularios
if ($_SERVER["REQUEST_METHOD"] == "POST") {
if (isset($_POST["guardar"])) {
$nombre = $conn->real_escape_string($_POST["nombre"]);
$apepat = $conn->real_escape_string($_POST["apepat"]);
$apemat = $conn->real_escape_string($_POST["apemat"]);
$empresa = $conn->real_escape_string($_POST["empresa"]);
$telefono = $conn->real_escape_string($_POST["telefono"]);

// Verificar si el proveedor ya existe
$checkQuery = "SELECT id_proveedor FROM proveedores WHERE
nombre='$nombre' AND apepat='$apepat' AND apemat='$apemat' AND
empresa='$empresa'";
$checkResult = $conn->query($checkQuery);

if ($checkResult->num_rows > 0) {

$errorMensaje = "El proveedor ya existe en la base de datos.";
} else {
$sql = "INSERT INTO proveedores (nombre, apepat, apemat, empresa,
telefono) VALUES ('$nombre', '$apepat', '$apemat', '$empresa', '$telefono')";
$conn->query($sql);
header("Location: crud_proveedores.php?action=added");
exit();
}
} elseif (isset($_POST["actualizar"])) {
$id = intval($_POST["idproveedor"]);
$nombre = $conn->real_escape_string($_POST["nombre"]);
$apepat = $conn->real_escape_string($_POST["apepat"]);
$apemat = $conn->real_escape_string($_POST["apemat"]);
$empresa = $conn->real_escape_string($_POST["empresa"]);
$telefono = $conn->real_escape_string($_POST["telefono"]);

if ($id > 0) {
$sql = "UPDATE proveedores SET nombre='$nombre', apepat='$apepat',
apemat='$apemat', empresa='$empresa', telefono='$telefono' WHERE
id_proveedor=$id";
$conn->query($sql);
header("Location: crud_proveedores.php?action=updated");
exit();
}
}
}

// Eliminar proveedor

if (isset($_GET["eliminar"])) {
$id = intval($_GET["eliminar"]);
if ($id > 0) {
$conn->query("DELETE FROM proveedores WHERE id_proveedor=$id");
header("Location: crud_proveedores.php?action=deleted");
exit();
}
}

// Obtener proveedor para edición
$proveedor = ["id_proveedor" => "", "nombre" => "", "apepat" => "", "apemat" => "",
"empresa" => "", "telefono" => ""];
if (isset($_GET["editar"])) {
$id = intval($_GET["editar"]);
$resultEdit = $conn->query("SELECT * FROM proveedores WHERE
id_proveedor=$id");
if ($resultEdit->num_rows > 0) {
$proveedor = $resultEdit->fetch_assoc();
}
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CRUD de Proveedores</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
rel="stylesheet">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link rel="stylesheet"
href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
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
body {
font-family: 'Poppins', sans-serif;
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

<?php include 'menu.php/navbar-menu'; ?>

<h2 class="text-center text-primary font-weight-bold">Proveedores</h2>

<!-- Mensaje de error -->
<?php if (!empty($errorMensaje)): ?>
<script>
Swal.fire({
icon: 'error',
title: '¡Proveedor existente!',
text: <?= json_encode($errorMensaje) ?>,
confirmButtonColor: "#6A0572",

backdrop: 'rgba(0,0,0,0.4)',
timer: 5000,
timerProgressBar: true
});
</script>
<?php endif; ?>

<!-- Formulario para agregar o editar proveedor -->
<div class="card p-3 mb-4">
<form method="post">
<input type="hidden" name="idproveedor" value="<?=
htmlspecialchars($proveedor['id_proveedor']) ?>">
<div class="mb-3 row">
<div class="col-2">
<label class="form-label">Nombre:</label>
<input type="text" class="form-control" name="nombre" value="<?=

htmlspecialchars($proveedor['nombre']) ?>" required>
</div>
<div class="col-2">
<label class="form-label">Apellido Paterno:</label>
<input type="text" class="form-control" name="apepat" value="<?=

htmlspecialchars($proveedor['apepat']) ?>" required>
</div>
<div class="col-2">
<label class="form-label">Apellido Materno:</label>
<input type="text" class="form-control" name="apemat" value="<?=

htmlspecialchars($proveedor['apemat']) ?>" required>
</div>

<div class="col-3">
<label class="form-label">Empresa:</label>
<input type="text" class="form-control" name="empresa" value="<?=

htmlspecialchars($proveedor['empresa']) ?>" required>
</div>
<div class="col-3">
<label class="form-label">Teléfono:</label>
<input type="text" class="form-control" name="telefono" value="<?=

htmlspecialchars($proveedor['telefono']) ?>" required>
</div>
</div>
<div class="d-flex gap-2">
<button type="submit" class="btn btn-success" name="<?=
$proveedor['id_proveedor'] ? 'actualizar' : 'guardar' ?>">

<?= $proveedor['id_proveedor'] ? 'Actualizar' : 'Guardar' ?>
</button>
<?php if ($proveedor['id_proveedor']): ?>
<a href="crud_proveedores.php" class="btn btn-secondary">Cancelar</a>
<?php endif; ?>
</div>
</form>
</div>

<!-- Tabla de proveedores -->
<table class="table table-bordered text-center compact table-primary" id="tabla_datos">
<thead class="table-dark">
<tr>
<th>Nombre</th>

<th>Apellido Paterno</th>
<th>Apellido Materno</th>
<th>Empresa</th>
<th>Teléfono</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row["nombre"]) ?></td>
<td><?= htmlspecialchars($row["apepat"]) ?></td>
<td><?= htmlspecialchars($row["apemat"]) ?></td>
<td><?= htmlspecialchars($row["empresa"]) ?></td>
<td><?= htmlspecialchars($row["telefono"]) ?></td>
<td>
<a href="#" onclick="confirmarEdicion(<?= $row['id_proveedor'] ?>); return

false;" class="btn btn-warning btn-sm">Editar</a>

<a href="#" onclick="confirmarEliminacion(<?= $row['id_proveedor'] ?>);

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
function confirmarRegreso() {
Swal.fire({
title: "¿Salir de proveedores?",
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

// Confirmación para eliminar un proveedor
function confirmarEliminacion(id) {
Swal.fire({
title: "¿Estás seguro?",
text: "¡No podrás revertir esta acción!",
icon: "warning",
showCancelButton: true,
confirmButtonColor: "#6A0572", // Color lila
cancelButtonColor: "#d33",
confirmButtonText: "Sí, eliminar",
cancelButtonText: "No, cancelar"
}).then((result) => {
if (result.isConfirmed) {
window.location.href = `crud_proveedores.php?eliminar=${id}`;
}
});
}

// Confirmación para editar un proveedor
function confirmarEdicion(id) {
Swal.fire({
title: "¿Editar este proveedor?",
text: "Serás redirigido a la página de edición.",

icon: "question",
showCancelButton: true,
confirmButtonColor: "#6A0572", // Color lila
cancelButtonColor: "#d33",
confirmButtonText: "Sí, editar",
cancelButtonText: "No, cancelar"
}).then((result) => {
if (result.isConfirmed) {
window.location.href = `crud_proveedores.php?editar=${id}`;
}
});
}

// Mostrar mensajes de SweetAlert2 según la acción realizada
const urlParams = new URLSearchParams(window.location.search);
const action = urlParams.get('action');

if (action === 'added') {
Swal.fire({
title: '¡Agregado!',
text: 'El proveedor ha sido agregado con éxito.',
icon: 'success',
confirmButtonColor: "#6A0572",
});
} else if (action === 'updated') {
Swal.fire({
title: '¡Actualizado!',

text: 'El proveedor ha sido actualizado con éxito.',
icon: 'success',
confirmButtonColor: "#6A0572",
});
} else if (action === 'deleted') {
Swal.fire({
title: '¡Eliminado!',
text: 'El proveedor ha sido eliminado con éxito.',
icon: 'success',
confirmButtonColor: "#6A0572",
});
}
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