<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Abarrotes Silvia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background: linear-gradient(to top, #D8B9D6, #6d38a0);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: brown;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            text-align: center;
            max-width: 350px;
        }

        .login-container h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            outline: none;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            background: rgba(0, 0, 0, 0.3);
            color: white;
        }

        .login-container input[type="submit"] {
            background: #ff7eb3;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .forgot {
            color: #ccc;
            text-decoration: none;
            display: block;
            margin: 10px 0;
        }

        .social-icons a {
            color: purple;
            font-size: 20px;
            margin: 0 10px;
            text-decoration: none;
        }

        .input-container {
            position: relative;
            width: 100%;
        }

        .input-container i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: purple;
        }

        .input-container input {
            width: 100%;
            padding: 10px 10px 10px 35px;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            outline: none;
        }

        .mi-color-personalizado {
            color: rgb(0, 0, 0);
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1.5em;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 3em;
            color: #fff;
            background: linear-gradient(to right, #ff7eb3, #6d38a0);
            -webkit-background-clip: text;
            background-clip: text;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.4);
            margin-bottom: 20px;
        }

        h1:hover {
            color: #ff6347;
            cursor: pointer;
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <?php
    include 'funciones/db.php';
    include 'funciones/conectar.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (!empty($username) && !empty($password)) {
            $stmt = $conn->prepare("SELECT id_usuario, nombre, apepat, apemat, contra, rol FROM usuarios WHERE nombre = ?");
            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['contra'])) {
                    $_SESSION['id_usuario'] = $row['id_usuario'];
                    $_SESSION['nombre'] = $row['nombre'];
                    $_SESSION['apepat'] = $row['apepat'];
                    $_SESSION['apemat'] = $row['apemat'];
                    $_SESSION['rol'] = $row['rol'];
                    echo '
<style>
body {
    background: linear-gradient(-45deg, #6d38a0, #ff7eb3, #D8B9D6, #23a6d5);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    min-height: 100vh;
    margin: 0;
}
@keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    title: "¡Acceso concedido!",
    text: "Bienvenido al menú principal.",
    icon: "success",
    showConfirmButton: false,
    timer: 2500,
    timerProgressBar: true,
    background: "rgba(255, 255, 255, 0.95)",
    color: "#4a148c",
    backdrop: `
        rgba(0,0,0,0.4)
        center left
        no-repeat
    `,
    didOpen: () => {
        const content = Swal.getHtmlContainer();
        if (content) content.style.fontSize = "1.1rem";
    }
}).then(function() {
    window.location.href = "menu.php";
});
</script>
';
                    exit;


                } else {
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Contraseña incorrecta',
                            text: 'Por favor, verifica tus datos e intenta nuevamente.'
                        });
                    </script>";
                }
            } else {
                echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Usuario no encontrado',
                            text: 'El usuario que ingresaste no está registrado.'
                        });
                    </script>";
            }
            $stmt->close();
        } else {
            echo "<script>
                        Swal.fire({
                            icon: 'warning',
                            title: 'Campos incompletos',
                            text: 'Por favor, completa todos los campos requeridos.'
                        });
                    </script>";
        }
        $conn->close();

    }
    ?>
    <div class="login-container">
        <h1>Abarrotes Silvia</h1>
        <p class="mi-color-personalizado">Por favor ingresa tu usuario y contraseña</p>
        <form method="POST">
            <div class="input-container">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-container">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <input type="submit" value="Login">
        </form>
        <div class="social-icons">
            <p>Contacta mis redes @Venta Smart</p>
            <a href="https://www.facebook.com/share/1Y5zQGfDiY/?mibextid=wwXIfr"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com/tu_usuario/" target="_blank"><i class="fab fa-instagram"></i></a>

        </div>
    </div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
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
                if (this.alpha < 0.3 || this.alpha > 1) this.twinkleSpeed *= -1;

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
                for (let i = 0; i < spikes; i++) {
                    const angle = (i * 2 * Math.PI) / spikes;

                    // Punto exterior
                    const x1 = Math.cos(angle) * outerRadius;
                    const y1 = Math.sin(angle) * outerRadius;

                    // Punto interior
                    const angle2 = angle + Math.PI / spikes;
                    const x2 = Math.cos(angle2) * innerRadius;
                    const y2 = Math.sin(angle2) * innerRadius;

                    if (i === 0) ctx.moveTo(x1, y1);
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