<?php
session_start();
// Incluir la conexión a la base de datos
include 'funciones/db.php';
if (!isset($_SESSION['id_usuario'])) {
    // Si no ha iniciado sesión, redirigir al login
    header("Location: login.php");
    exit;
}

// Verificar conexión a la base de datos
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Definir el número de resultados por página
$results_per_page = 10;

// Obtener el número de página actual
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Buscar usuarios
$search = "";
if (isset($_GET["buscar"])) {
    $search = $conn->real_escape_string($_GET["buscar"]);
    $sql = "SELECT * FROM usuarios WHERE 
            nombre LIKE '%$search%' 
            OR apepat LIKE '%$search%' 
            OR apemat LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM usuarios";
}

$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Contar el total de usuarios para calcular la cantidad de páginas
$count_sql = "SELECT COUNT(*) FROM usuarios";
$count_result = $conn->query($count_sql);
$row = $count_result->fetch_row();
$total_users = $row[0];
$total_pages = ceil($total_users / $results_per_page);

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errores = [];
    $nombre = $conn->real_escape_string($_POST["nombre"]);
    $apepat = $conn->real_escape_string($_POST["apepat"]);
    $apemat = $conn->real_escape_string($_POST["apemat"]);

    // Validación de campos
    $campos_validar = [
        'nombre' => $nombre,
        'apellido paterno' => $apepat,
        'apellido materno' => $apemat
    ];

    foreach ($campos_validar as $campo => $valor) {
        if (preg_match('/[0-9]/', $valor)) {
            $errores[] = "El campo $campo no debe contener números";
        }
        if (!preg_match('/^[\p{L}\s]+$/u', $valor)) {
            $errores[] = "El campo $campo contiene caracteres inválidos";
        }
    }

if (!empty($errores)) {
    $mensaje = implode('<br>', $errores);
    // Reiniciar valores del formulario
    $usuario = [
        'nombre' => '',
        'apepat' => '',
        'apemat' => '',
        'rol' => '',
        'id_usuario' => ''
    ];
    goto mostrar_mensaje;
}

    if (isset($_POST["guardar"])) {
        $rol = intval($_POST["rol"]);
        $contrasena = $_POST["contrasena"];

        // Verificar si el usuario ya existe
        $checkUserSql = "SELECT * FROM usuarios WHERE nombre = '$nombre' AND apepat = '$apepat' AND apemat = '$apemat'";
        $checkUserResult = $conn->query($checkUserSql);

if ($checkUserResult->num_rows > 0) {
    $mensaje = "El usuario con esos datos ya existe.";
    // Reiniciar valores del formulario
    $usuario = [
        'nombre' => '',
        'apepat' => '',
        'apemat' => '',
        'rol' => '',
        'id_usuario' => ''
    ];
    goto mostrar_mensaje;
}else {
            //$contrasenaHashed = password_hash($contrasena, PASSWORD_BCRYPT);
           // $sql = "INSERT INTO usuarios (nombre, apepat, apemat, rol, contra) VALUES ('$nombre', '$apepat', '$apemat', $rol, '$contrasenaHashed')";
            //$conn->query($sql);
            //header("Location: crud_usuarios.php?action=added");
           // exit();
          
// Suponiendo que ya tienes una conexión $conn activa
         $contrasenaHashed = password_hash($contrasena, PASSWORD_BCRYPT);

         $sql = "CALL insertarusuario(?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssis", $nombre, $apepat, $apemat, $rol, $contrasenaHashed);

        $stmt->execute();
     //   $stmt->close();

header("Location: crud_usuarios.php?action=added");
exit();


        }
    } elseif (isset($_POST["actualizar"])) {
        $id = intval($_POST["id_usuario"]);
        $rol = intval($_POST["rol"]);
        $contrasena = $_POST["contrasena"];

        // Verificar si se proporcionó una nueva contraseña
        if (!empty($contrasena)) {
            $contrasenaHashed = password_hash($contrasena, PASSWORD_BCRYPT);
            $sql = "UPDATE usuarios SET nombre='$nombre', apepat='$apepat', apemat='$apemat', rol=$rol, contra='$contrasenaHashed' WHERE id_usuario=$id";
        } else {
            $sql = "UPDATE usuarios SET nombre='$nombre', apepat='$apepat', apemat='$apemat', rol=$rol WHERE id_usuario=$id";
        }

        if ($conn->query($sql) === TRUE) {
            header("Location: crud_usuarios.php?action=updated");
            exit();
        } else {
            $mensaje = "Error al actualizar el usuario: " . $conn->error;
        }
    }
}

mostrar_mensaje:

if (isset($_GET["eliminar"])) {
    $id = intval($_GET["eliminar"]);
    if ($id > 0) {
        $conn->query("DELETE FROM usuarios WHERE id_usuario=$id");
        header("Location: crud_usuarios.php?action=deleted");
        exit();
    }
}

$usuario = ["id_usuario" => "", "nombre" => "", "apepat" => "", "apemat" => "", "rol" => "", "contraseña" => ""];
if (isset($_GET["editar"])) {
    $id = intval($_GET["editar"]);
    $resultEdit = $conn->query("SELECT * FROM usuarios WHERE id_usuario=$id");
    if ($resultEdit->num_rows > 0) {
        $usuario = $resultEdit->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color:  #9966CC
;
        }
        .show-password {
            cursor: pointer;
            color: #6A0572;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        .table-primary {
    background-color: #c1fdec !important;
}
      body {
    font-family: 'Poppins', sans-serif;
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
    <h2 class="text-center text-primary font-weight-bold">Usuarios</h2>

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

    <div class="card p-3 mb-4">
         <form method="post" onsubmit="return validarFormulario()">
        <form method="post">
            <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['id_usuario']) ?>">
            <div class="mb-3 row">
                <div class="col-2">
                    <label class="form-label">Nombre:</label>
                    <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                </div>
                <div class="col-2">
                    <label class="form-label">Apellido Paterno:</label>
                    <input type="text" class="form-control" name="apepat" value="<?= htmlspecialchars($usuario['apepat']) ?>" required>
                </div>
                <div class="col-2">
                    <label class="form-label">Apellido Materno:</label>
                    <input type="text" class="form-control" name="apemat" value="<?= htmlspecialchars($usuario['apemat']) ?>" required>
                </div>
                <div class="col-3">
                    <label class="form-label">Rol:</label>
                    <select class="form-control" name="rol" required>
                        <option value="1" <?= $usuario['rol'] == 1 ? 'selected' : '' ?>>Administrador</option>
                        <option value="2" <?= $usuario['rol'] == 2 ? 'selected' : '' ?>>Empleado</option>
                    </select>
                </div>
                <div class="col-3">
                    <label class="form-label">Contraseña:</label>
                    <input type="password" class="form-control" name="contrasena" id="contrasena" value="<?= htmlspecialchars($usuario['contraseña']) ?>">
                    <span class="show-password" onclick="mostrarContrasena()">Mostrar contraseña.</span>
                    <small>Dejar en blanco para mantener la contraseña actual.</small>
                </div>
            </div>

            <button type="submit" class="btn btn-success" name="<?= $usuario['id_usuario'] ? 'actualizar' : 'guardar' ?>">
                <?= $usuario['id_usuario'] ? 'Actualizar' : 'Guardar' ?>
            </button>
            <?php if ($usuario['id_usuario']): ?>
                <a href="crud_usuarios.php" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
        </form>
    </div>

    <table class="table table-bordered text-center compact table-primary" id="tabla_datos">

        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row["nombre"]) ?></td>
                    <td><?= htmlspecialchars($row["apepat"]) ?></td>
                    <td><?= htmlspecialchars($row["apemat"]) ?></td>
                    <td><?= $row["rol"] == 1 ? 'Administrador' : 'Empleado' ?></td>
                    <td>
                        <a href="#" onclick="confirmarEdicion(<?= $row['id_usuario'] ?>); return false;" class="btn btn-warning btn-sm">Editar</a>
                        <a href="#" onclick="confirmarEliminacion(<?= $row['id_usuario'] ?>); return false;" class="btn btn-danger btn-sm">Eliminar</a>
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
        // Agregar función de validación
// Modificar la función validarFormulario()
function validarFormulario() {
    const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
    const form = document.forms[0];
    
    // Obtener valores limpios
    const campos = {
        nombre: form.nombre.value.trim(),
        apepat: form.apepat.value.trim(),
        apemat: form.apemat.value.trim()
    };

    // Validar campos vacíos
    for (const [campo, valor] of Object.entries(campos)) {
        if (!valor) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `El campo ${campo} no puede estar vacío`,
                confirmButtonColor: "#6A0572",
            });
            form.reset(); // Limpiar formulario
            return false;
        }
    }

    // Validar caracteres
    for (const [campo, valor] of Object.entries(campos)) {
        if (!valor.match(regex)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `${campo.charAt(0).toUpperCase() + campo.slice(1)} contiene caracteres inválidos`,
                confirmButtonColor: "#6A0572",
            });
            form.reset(); // Limpiar formulario
            return false;
        }
    }

    // Validar contraseña en creación
    <?php if (!isset($_GET['editar'])): ?>
    if (!form.contrasena.value) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'La contraseña es obligatoria',
            confirmButtonColor: "#6A0572",
        });
        form.reset(); // Limpiar formulario
        return false;
    }
    <?php endif; ?>

    return true;
}
function confirmarRegreso() {
    Swal.fire({
        title: "¿Salir de usuarios?",
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
                    window.location.href = `crud_usuarios.php?eliminar=${id}`;
                }
            });
        }

        function confirmarEdicion(id) {
            Swal.fire({
                title: "¿Editar este usuario?",
                text: "Serás redirigido a la página de edición.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#6A0572",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, editar",
                cancelButtonText: "No, cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `crud_usuarios.php?editar=${id}`;
                }
            });
        }

        const urlParams = new URLSearchParams(window.location.search);
        const action = urlParams.get('action');

        if (action === 'added') {
            Swal.fire({
                title: '¡Agregado!',
                text: 'El usuario ha sido agregado con éxito.',
                icon: 'success',
                confirmButtonColor: "#6A0572",
            });
        } else if (action === 'updated') {
            Swal.fire({
                title: '¡Actualizado!',
                text: 'El usuario ha sido actualizado con éxito.',
                icon: 'success',
                confirmButtonColor: "#6A0572",
            });
        } else if (action === 'deleted') {
            Swal.fire({
                title: '¡Eliminado!',
                text: 'El usuario ha sido eliminado con éxito.',
                icon: 'success',
                confirmButtonColor: "#6A0572",
            });
        }

        function mostrarContrasena() {
            const contrasenaInput = document.getElementById("contrasena");
            const showPasswordText = document.querySelector(".show-password");

            if (contrasenaInput.type === "password") {
                contrasenaInput.type = "text";
                showPasswordText.textContent = "Ocultar contraseña";
            } else {
                contrasenaInput.type = "password";
                showPasswordText.textContent = "Mostrar contraseña";
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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