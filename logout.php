<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purple Logout Experience | Nexium</title>
    <!-- Recursos -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #9B72CF 0%, #6d38a0 100%);
            --neon-effect: 0 0 15px rgba(157, 114, 207, 0.5);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(-45deg, #6d38a0, #ff7eb3, #D8B9D6, #9B72CF);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .logo {
            position: absolute;
            top: 30px;
            left: 30px;
            width: 100px;
            filter: drop-shadow(var(--neon-effect));
            transition: transform 0.4s ease;
            cursor: pointer;
            z-index: 3;
        }

        .logo:hover {
            transform: rotate(-15deg) scale(1.1);
        }

        .swal2-popup {
            background: rgba(171, 96, 173, 0.95)!important;
            backdrop-filter: blur(12px)!important;
            border: 1px solid rgba(255,126,179,0.3)!important;
            border-radius: 20px!important;
            box-shadow: 0 0 30px rgba(255,126,179,0.2)!important;
            z-index: 4!important;
        }

        .swal2-title {
            color: #fff!important;
            font-size: 2.2em!important;
            letter-spacing: -1px;
            text-shadow: 0 0 10px rgba(255,126,179,0.5);
        }

        .swal2-html-container {
            color: #D8B9D6!important;
            font-size: 1.1em!important;
        }

        .swal2-confirm {
            background: var(--primary-gradient)!important;
            border: none!important;
            border-radius: 12px!important;
            padding: 15px 35px!important;
            font-weight: 600!important;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease!important;
        }

        .swal2-confirm::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                120deg,
                transparent,
                rgba(255,255,255,0.3),
                transparent
            );
            transition: 0.6s;
        }

        .swal2-confirm:hover::after {
            left: 100%;
        }

        .swal2-cancel {
            background: rgba(255,126,179,0.15)!important;
            border: 1px solid rgba(255,126,179,0.3)!important;
            color: #fff!important;
            border-radius: 12px!important;
            padding: 15px 35px!important;
            transition: all 0.3s ease!important;
        }

        .swal2-cancel:hover {
            background: rgba(255,126,179,0.25)!important;
            transform: translateY(-2px);
        }

        .cyber-glitch {
            animation: glitch 2s infinite;
        }

        @keyframes glitch {
            0% { text-shadow: 2px 0 #ff7eb3, -2px 0 #D8B9D6; }
            5% { text-shadow: 3px 1px #ff7eb3, -1px -1px #D8B9D6; }
            96% { text-shadow: 2px 0 #ff7eb3, -2px 0 #D8B9D6; }
            100% { text-shadow: 2px 0 #ff7eb3, -2px 0 #D8B9D6; }
        }

        .holographic-effect {
            background: linear-gradient(45deg, 
                #ff7eb3 20%, 
                #D8B9D6 40%, 
                #9B72CF 60%, 
                #6d38a0 80%
            );
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: holographic 8s ease infinite;
        }

        .floating {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .neon-footer {
            position: fixed;
            bottom: 25px;
            color: rgba(255,255,255,0.3);
            font-size: 0.9em;
            text-align: center;
            width: 100%;
            letter-spacing: 1px;
            z-index: 2;
        }

        .custom-cursor {
            position: fixed;
            width: 20px;
            height: 20px;
            border: 2px solid #9B72CF;
            border-radius: 50%;
            pointer-events: none;
            transition: transform 0.3s, background 0.3s;
            z-index: 9999;
        }
    </style>
</head>
<body>
    <div class="custom-cursor"></div>
    <img src="img/tiendita.jpg" alt="Logo" class="logo floating">
    <div id="particles-js"></div>

    <script>
        // Partículas moradas
        particlesJS('particles-js', {
            particles: {
                number: { value: 100 },
                color: { value: '#9B72CF' },
                shape: { type: 'circle' },
                opacity: { value: 0.6 },
                size: { value: 3 },
                move: {
                    enable: true,
                    speed: 2,
                    direction: 'none',
                    random: true,
                    straight: false,
                    out_mode: 'out',
                    bounce: false,
                }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: { enable: true, mode: 'repulse' },
                    onclick: { enable: true, mode: 'push' },
                    resize: true
                }
            },
            retina_detect: true
        });

        // Cursor personalizado
        const cursor = document.querySelector('.custom-cursor');
        document.addEventListener('mousemove', (e) => {
            cursor.style.left = e.clientX + 'px';
            cursor.style.top = e.clientY + 'px';
        });

        // Animación de SweetAlert
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '<span class="cyber-glitch">CIERRE DE SESIÓN</span>',
               html: '<p style="color: #fff; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">¿Seguro que quieres salir?</p>',
                iconHtml: '<div class="floating"><svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>',
                showCancelButton: true,
                confirmButtonText: 'Cerrar Sesión',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                background: 'transparent',
                backdrop: `
                    rgba(109, 56, 160, 0.3)
                    url("data:image/svg+xml,%3Csvg width='52' height='26' viewBox='0 0 52 26' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239B72CF' fill-opacity='0.2'%3E%3Cpath d='M10 10c0-2.21-1.79-4-4-4-3.314 0-6-2.686-6-6h2c0 2.21 1.79 4 4 4 3.314 0 6 2.686 6 6 0 2.21 1.79 4 4 4 3.314 0 6 2.686 6 6 0 2.21 1.79 4 4 4v2c-3.314 0-6-2.686-6-6 0-2.21-1.79-4-4-4-3.314 0-6-2.686-6-6zm25.464-1.95l8.486 8.486-1.414 1.414-8.486-8.486 1.414-1.414z' /%3E%3C/g%3E%3C/g%3E%3C/svg%3E")
                `,
                customClass: {
                    confirmButton: 'swal-confirm-purple',
                    cancelButton: 'swal-cancel-pink'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.body.style.opacity = '0';
                    setTimeout(() => {
                        window.location.href = 'cerrar.php';
                    }, 1000);
                } else {
                    window.location.href = 'menu.php';
                }
            });
        });
    </script>

    <div class="neon-footer">
        <span style="color: #ffffff; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">@2025 VentaSmart. Todos los derechos reservados</span>
    </div>
</body>
</html>