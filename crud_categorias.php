<?php
session_start();
include 'funciones/db.php';
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

// Búsqueda de categorías
$search = "";
if (isset($_GET["buscar"])) {
    $search = $conn->real_escape_string($_GET["buscar"]);
    $sql = "SELECT * FROM categorias WHERE nombre LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM categorias";
}

$result = $conn->query($sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["guardar"])) {
        $nombre = $conn->real_escape_string($_POST["nombre"]);

        $check_sql = "SELECT COUNT(*) AS total FROM categorias WHERE nombre = '$nombre'";
        $check_result = $conn->query($check_sql);
        $row = $check_result->fetch_assoc();

        if ($row["total"] > 0) {
            // Redirigir con parámetro de error
            header("Location: crud_categorias.php?error=" . urlencode("La categoría ya existe, intenta con otro nombre."));
            exit();
        } else {
            $sql = "INSERT INTO categorias (nombre) VALUES ('$nombre')";
            if ($conn->query($sql)) {
                // Redirigir con éxito
                header("Location: crud_categorias.php?action=added");
                exit();
            } else {
                header("Location: crud_categorias.php?error=" . urlencode("Error al guardar la categoría."));
                exit();
            }
        }
    } elseif (isset($_POST["actualizar"])) {
        $id = intval($_POST["idcategoria"]);
        $nombre = $conn->real_escape_string($_POST["nombre"]);

        $check_sql = "SELECT COUNT(*) AS total FROM categorias WHERE nombre = '$nombre' AND id_categoria != $id";
        $check_result = $conn->query($check_sql);
        $row = $check_result->fetch_assoc();

        if ($row["total"] > 0) {
            header("Location: crud_categorias.php?error=" . urlencode("Ese nombre de categoría ya está en uso. Intenta con otro."));
            exit();
        } else {
            if ($id > 0 && !empty($nombre)) {
                $sql = "UPDATE categorias SET nombre='$nombre' WHERE id_categoria=$id";
                if ($conn->query($sql)) {
                    header("Location: crud_categorias.php?action=updated");
                    exit();
                } else {
                    header("Location: crud_categorias.php?error=" . urlencode("Error al actualizar la categoría."));
                    exit();
                }
            }
        }
    }
}
// Manejo de errores (sin cambios)

// Eliminar categoría (sin cambios)
if (isset($_GET["eliminar"])) {
    $id = intval($_GET["eliminar"]);
    if ($id > 0) {
        if ($conn->query("DELETE FROM categorias WHERE id_categoria=$id")) {
            header("Location: crud_categorias.php?action=deleted");
            exit();
        }
    }
}

// Obtener categoría para edición (sin cambios)
$categoria = ["id_categoria" => "", "nombre" => ""];
if (isset($_GET["editar"])) {
    $id = intval($_GET["editar"]);
    $resultEdit = $conn->query("SELECT * FROM categorias WHERE id_categoria=$id");
    if ($resultEdit->num_rows > 0) {
        $categoria = $resultEdit->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #D8B9D6;
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
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(12px);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
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

    <h2 class="text-center text-primary font-weight-bold">Categorías de productos</h2>
    
    <!-- Formulario para agregar o editar categoría -->
    <div class="card p-3 mb-4">
        <form method="post" onsubmit="return validarFormulario();" class="text-center">
            <input type="hidden" name="idcategoria" value="<?= htmlspecialchars($categoria['id_categoria']) ?>">
            <div class="mb-3 w-50 mx-auto">
                <label class="form-label">Nombre:</label>
                <input type="text" class="form-control" name="nombre" id="nombre" value="<?= htmlspecialchars($categoria['nombre']) ?>" required>
            </div>
            <div class="d-flex justify-content-center gap-2">
                <button type="submit" class="btn btn-success" name="<?= $categoria['id_categoria'] ? 'actualizar' : 'guardar' ?>">
                    <?= $categoria['id_categoria'] ? 'Actualizar' : 'Guardar' ?>
                </button>
                <?php if ($categoria['id_categoria']): ?>
                    <a href="crud_categorias.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabla de categorías -->
    <table class="table table-bordered text-center compact table-primary" id="tabla_datos">
        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row["nombre"]) ?></td>
                    <td>
                        <a href="#" onclick="confirmarEdicion(<?= $row['id_categoria'] ?>); return false;" class="btn btn-warning btn-sm">Editar</a>
                        <a href="#" onclick="confirmarEliminacion(<?= $row['id_categoria'] ?>); return false;" class="btn btn-danger btn-sm">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Imagen en la esquina de arriba, con enlace -->
<a href="#" onclick="confirmarRegreso(); return false;" 
   class="floating-btn btn btn-primary btn-lg rounded-circle">
    <i class="bi bi-house-door fs-4"></i>
</a>

    <script>
function confirmarRegreso() {
    Swal.fire({
        title: "¿Salir de categorías?",
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
        // Confirmación para eliminar una categoría
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
                    window.location.href = `crud_categorias.php?eliminar=${id}`;
                }
            });
        }

        // Confirmación para editar una categoría
        function confirmarEdicion(id) {
            Swal.fire({
                title: "¿Editar esta categoría?",
                text: "Serás redirigido a la página de edición.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#6A0572", // Color lila
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, editar",
                cancelButtonText: "No, cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `crud_categorias.php?editar=${id}`;
                }
            });
        }

        // Mostrar mensajes de SweetAlert2 según la acción realizada
        const urlParams = new URLSearchParams(window.location.search);
        const action = urlParams.get('action');

        if (action === 'added') {
            Swal.fire({
                title: '¡Agregado!',
                text: 'La categoría ha sido agregada con éxito.',
                icon: 'success',
                confirmButtonColor: "#6A0572",
            });
        } else if (action === 'updated') {
            Swal.fire({
                title: '¡Actualizado!',
                text: 'La categoría ha sido actualizada con éxito.',
                icon: 'success',
                confirmButtonColor: "#6A0572",
            });
        } else if (action === 'deleted') {
            Swal.fire({
                title: '¡Eliminado!',
                text: 'La categoría ha sido eliminada con éxito.',
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
<script>
function validarFormulario() {
    const nombre = document.getElementById("nombre").value.trim();

    // Verifica si el campo está vacío
    if (nombre === "") {
        Swal.fire({
            icon: "error",
            title: "Campo vacío",
            text: "Por favor escribe un nombre para la categoría.",
            confirmButtonColor: "#6A0572"
        });
        return false;
    }

    // Verifica si contiene solo letras y espacios
    const soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/;
    if (!soloLetras.test(nombre)) {
        Swal.fire({
            icon: "error",
            title: "Nombre inválido",
            text: "Solo se permiten letras en el nombre de la categoría.",
            confirmButtonColor: "#6A0572"
        });
        return false;
    }

    return true;
}
</script>
<?php if (!empty($_GET['error'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= urldecode($_GET['error']) ?>',
        confirmButtonColor: '#6A0572'
    });
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