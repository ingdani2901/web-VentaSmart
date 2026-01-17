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

// Buscar clientes
$search = "";
if (isset($_GET["buscar"])) {
$search = $conn->real_escape_string($_GET["buscar"]);
$sql = "SELECT * FROM clientes WHERE
nombre LIKE '%$search%'
OR apepat LIKE '%$search%'
OR apemat LIKE '%$search%'";
} else {
$sql = "SELECT * FROM clientes";
}

$result = $conn->query($sql);

// Formularios
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$errores = [];
$nombre = $conn->real_escape_string($_POST["nombre"]);
$apepat = $conn->real_escape_string($_POST["apepat"]);

$apemat = $conn->real_escape_string($_POST["apemat"]);

// Validación nueva
$campos = [
'nombre' => $nombre,
'apellido paterno' => $apepat,
'apellido materno' => $apemat
];

foreach ($campos as $campo => $valor) {
if (preg_match('/[0-9]/', $valor)) {
$errores[] = "El $campo no debe contener números";
}
if (!preg_match('/^[\p{L}\s]+$/u', $valor)) {
$errores[] = "El $campo contiene caracteres inválidos";
}
}

if (count($errores) > 0) {
$mensaje = implode(", ", $errores);
goto mostrar_mensaje;
}
// Verificar si el cliente ya existe

if (isset($_POST["guardar"])) {
// Verificar si el nombre ya existe
$sql_check = "SELECT * FROM clientes WHERE nombre = '$nombre' AND apepat
= '$apepat' AND apemat = '$apemat'";
$result_check = $conn->query($sql_check);

if ($result_check->num_rows > 0) {
$mensaje = "El cliente ya existe en la base de datos.";
// Mantener los datos del formulario después del error
$cliente = [
'nombre' => $nombre,
'apepat' => $apepat,
'apemat' => $apemat,
'id_cliente' => $_POST['id_cliente'] ?? ''
];
goto mostrar_mensaje;
} else {
$sql = "INSERT INTO clientes (nombre, apepat, apemat) VALUES ('$nombre',
'$apepat', '$apemat')";
$conn->query($sql);

header("Location: crud_clientes.php?action=added");
exit();
}
} elseif (isset($_POST["actualizar"])) {
$id = intval($_POST["id_cliente"]);

if ($id > 0) {
$sql = "UPDATE clientes SET nombre='$nombre', apepat='$apepat',
apemat='$apemat' WHERE id_cliente=$id";
$conn->query($sql);
header("Location: crud_clientes.php?action=updated");
exit();
}
}
}
mostrar_mensaje:

// Eliminar cliente
if (isset($_GET["eliminar"])) {
$id = intval($_GET["eliminar"]);
if ($id > 0) {
$conn->query("DELETE FROM clientes WHERE id_cliente=$id");
header("Location: crud_clientes.php?action=deleted");

exit();
}
}

// Obtener cliente para edición
$cliente = ["id_cliente" => "", "nombre" => "", "apepat" => "", "apemat" => ""];
if (isset($_GET["editar"])) {
$id = intval($_GET["editar"]);
$resultEdit = $conn->query("SELECT * FROM clientes WHERE id_cliente=$id");
if ($resultEdit->num_rows > 0) {
$cliente = $resultEdit->fetch_assoc();
}
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CRUD de Clientes</title>
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
background-color: #D8B9D6; /* Fondo de color lila */
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
.card {
padding: 0.2rem; /* Menos padding en la tarjeta */
}
.form-label {
margin-bottom: 0.2rem; /* Reducir el espacio debajo de las etiquetas */
}
.form-control {
padding: 0.3rem; /* Menos padding en los inputs */
}
.custom-input {
margin-bottom: 0.5rem; /* Reducir el espacio entre los inputs */
}
.mb-1 {
margin-bottom: 0.2rem !important; /* Asegurar que haya menos margen entre
los campos */
}

body {
font-family: 'Poppins', sans-serif;
}
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

<h2 class="text-center text-primary font-weight-bold">Clientes</h2>

<!-- Añadir mensajes de error -->
<?php if (isset($mensaje)): ?>

<script>
Swal.fire({
icon: 'error',
title: 'Error',
text: <?= json_encode($mensaje) ?>,
confirmButtonColor: "#6A0572",
});
</script>
<?php endif; ?>

<!-- Formulario para agregar o editar cliente -->
<div class="card p-2 mb-4">
<form method="post" class="text-center" onsubmit="return validarFormulario()">
<input type="hidden" name="id_cliente" value="<?=
htmlspecialchars($cliente['id_cliente']) ?>">

<div class="mb-1 row justify-content-center">
<div class="col-md-3">
<label class="form-label">Nombre:</label>
<input type="text" class="form-control form-control-sm" name="nombre"

value="<?= htmlspecialchars($cliente['nombre']) ?>" required>
</div>
<div class="col-md-3">

<label class="form-label">Apellido Paterno:</label>
<input type="text" class="form-control form-control-sm" name="apepat"

value="<?= htmlspecialchars($cliente['apepat']) ?>" required>
</div>
<div class="col-md-3 mt-1">
<label class="form-label">Apellido Materno:</label>
<input type="text" class="form-control form-control-sm" name="apemat"

value="<?= htmlspecialchars($cliente['apemat']) ?>" required>
</div>
</div>

<div class="d-flex justify-content-center gap-2 mt-3">
<button type="submit" class="btn btn-success" name="<?= $cliente['id_cliente']
? 'actualizar' : 'guardar' ?>">

<?= $cliente['id_cliente'] ? 'Actualizar' : 'Guardar' ?>
</button>
<?php if ($cliente['id_cliente']): ?>
<a href="crud_clientes.php" class="btn btn-secondary">Cancelar</a>
<?php endif; ?>
</div>
</form>
</div>

<!-- Tabla de clientes -->
<table class="table table-bordered text-center compact table-primary" id="tabla_datos">
<thead class="table-dark">
<tr>
<th>Nombre</th>
<th>Apellido Paterno</th>
<th>Apellido Materno</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row["nombre"]) ?></td>
<td><?= htmlspecialchars($row["apepat"]) ?></td>
<td><?= htmlspecialchars($row["apemat"]) ?></td>
<td>
<a href="#" onclick="confirmarEdicion(<?= $row['id_cliente'] ?>); return

false;" class="btn btn-warning btn-sm">Editar</a>

<a href="#" onclick="confirmarEliminacion(<?= $row['id_cliente'] ?>); return

false;" class="btn btn-danger btn-sm">Eliminar</a>

</td>
</tr>

<?php endwhile; ?>
</tbody>
</table>

<!-- Imagen en la esquina de arriba, con enlace -->
<!-- Botón flotante mejorado -->
<a href="#" onclick="confirmarRegreso(); return false;"
class="floating-btn btn btn-primary btn-lg rounded-circle">
<i class="bi bi-house-door fs-4"></i>
</a>

<script>
// Añadir validación JavaScript
function validarFormulario() {
const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
const campos = {
nombre: document.getElementsByName('nombre')[0].value.trim(),
apepat: document.getElementsByName('apepat')[0].value.trim(),
apemat: document.getElementsByName('apemat')[0].value.trim()
};

for (const [campo, valor] of Object.entries(campos)) {
if (!valor.match(regex)) {

let nombreCampo = '';
switch(campo) {
case 'nombre': nombreCampo = 'Nombre'; break;
case 'apepat': nombreCampo = 'Apellido Paterno'; break;
case 'apemat': nombreCampo = 'Apellido Materno'; break;
}
Swal.fire({
icon: 'error',
title: 'Error',
text: `${nombreCampo} debe contener solo letras válidas`,
confirmButtonColor: "#6A0572",
});
return false;
}
}
return true;
}
function confirmarRegreso() {
Swal.fire({
title: "¿Salir de clientes?",
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

// Confirmación para eliminar un cliente
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
window.location.href = `crud_clientes.php?eliminar=${id}`;
}
});
}

// Confirmación para editar un cliente
function confirmarEdicion(id) {
Swal.fire({
title: "¿Editar este cliente?",
text: "Serás redirigido a la página de edición.",
icon: "question",
showCancelButton: true,
confirmButtonColor: "#6A0572", // Color lila

cancelButtonColor: "#d33",
confirmButtonText: "Sí, editar",
cancelButtonText: "No, cancelar"
}).then((result) => {
if (result.isConfirmed) {
window.location.href = `crud_clientes.php?editar=${id}`;
}
});
}

// Mostrar mensajes de SweetAlert2 según la acción realizada
const urlParams = new URLSearchParams(window.location.search);
const action = urlParams.get('action');

if (action === 'added') {
Swal.fire({
title: '¡Agregado!',
text: 'El cliente ha sido agregado con éxito.',
icon: 'success',
confirmButtonColor: "#6A0572",
});
} else if (action === 'updated') {
Swal.fire({

title: '¡Actualizado!',
text: 'El cliente ha sido actualizado con éxito.',
icon: 'success',
confirmButtonColor: "#6A0572",
});
} else if (action === 'deleted') {
Swal.fire({
title: '¡Eliminado!',
text: 'El cliente ha sido eliminado con éxito.',
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