<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

// Obtener el nombre y apellido del usuario para mostrar en el menú
$nombreUsuario = $_SESSION['nombre'] . " " . $_SESSION['apepat'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

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

        /* Barra de navegación fija en la parte superior */
        .navbar {
            background: linear-gradient(45deg, #000428, #004e92);
            padding: 10px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 24px;
            font-weight: bold;
        }

        .navbar-nav .nav-link {
            color: white;
            transition: 0.3s;
        }

        .navbar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }

        /* Contenido Principal - Centrado */
        .content-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Toma toda la pantalla */
            text-align: center;
            padding-top: 70px; /* Ajuste para evitar solapamiento con la barra */
        }

        .titulo-principal {
            font-size: 2.5rem;
            font-weight: bold;
            text-transform: uppercase;
            background: linear-gradient(90deg, #ff8a00, #e52e71);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .marca {
            font-weight: 900;
            color: #ff8a00;
        }

        .descripcion {
            font-size: 1.2rem;
            color: #333;
            font-style: italic;
            margin-top: 10px;
        }

        .resaltado {
            font-weight: bold;
            color: #e52e71;
        }

        /* Imagen centrada */
        .background-image-container {
            width: 300px;
            height: 300px;
            background-image: url('img/tiendita.jpg');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            margin-top: 20px;
        }

        /* Estilos de usuario en barra */
        .usuario-nav {
            font-weight: bold;
            color: #fff;
            background: linear-gradient(135deg, #ff8a00, #e52e71);
            padding: 8px 15px;
            border-radius: 20px;
            transition: all 0.3s ease-in-out;
            display: inline-block;
        }

        .usuario-nav:hover {
            background: linear-gradient(135deg, #e52e71, #ff8a00);
            transform: scale(1.05);
            text-decoration: none;
            color: #fff;
        }
        body {
    font-family: 'Poppins', sans-serif;
}

    </style>
</head>
<body>

    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
        <a class="navbar-brand" href="#">
  <img src="img/logo.jpg" class="d-inline-block" style="height: 30px; margin-right: 10px;">
  VentaSmart
</a>


            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        <?php
           if( $_SESSION['rol']==1){
           ?>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto text-center">
                    <li class="nav-item">
                        <a class="nav-link" href="crud_usuarios.php"><ion-icon<i class="bi bi-people"></i></ion-icon> Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="crud_clientes.php"><i class="bi bi-person-add"></i></ion-icon> Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="caja.php"><i class="bi bi-person-add"></i></ion-icon>Caja</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-cart-plus"></i>Ventas
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pv.php">Registrar venta</a></li>
                            <li><a class="dropdown-item" href="ver_ventas.php">Consultar venta</a></li>
                        </ul>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <ion-icon name="person-add-outline"></ion-icon>Proveedores
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="crud_proveedores.php">Registrar proveedor</a></li>
                            <li><a class="dropdown-item" href="pc.php">Registrar compra</a></li>
                            <li><a class="dropdown-item" href="ver_compras.php">Consultar compras</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-clipboard-data"></i></ion-icon>Reportes
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="reportes.php">Reportes generales</a></li>
                            <li><a class="dropdown-item" href="reporte_stock.php">Reporte de stock</a></li>
                        </ul>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bag-plus"></i> Productos
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="crud_productos.php">Registrar producto</a></li>
                            <li><a class="dropdown-item" href="crud_categorias.php">Registrar categoría de producto</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger text-white" href="logout.php"><ion-icon name="close-circle-outline"></ion-icon>Cerrar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link usuario-nav" href="#">👋 Bienvenid@, <?php echo htmlspecialchars($nombreUsuario); ?></a>
                    </li>
                </ul>
            </div>
           <?
        
        }else{
            ?>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto text-center">
                  
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-cart-plus"></i>Ventas
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pv.php">Registrar venta</a></li>
                        </ul>
                    </li>
                  
                   
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger text-white" href="logout.php"><ion-icon name="close-circle-outline"></ion-icon>Cerrar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link usuario-nav" href="#">👋 Bienvenid@, <?php echo htmlspecialchars($nombreUsuario); ?></a>
                    </li>
                </ul>
            </div>
           <?
        }
        ?>

            
        </div>
    </nav>

    <!-- Contenido Principal Centrado -->
    <div class="content-container">
        <h1 class="titulo-principal"><span class="marca">Abarrotes Silvia</span></h1>
        <p class="descripcion">📊 Administra tus <span class="resaltado">productos</span>, <span class="resaltado">ventas</span> y <span class="resaltado">compras</span> de manera eficiente.</p>
        <div class="background-image-container"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
