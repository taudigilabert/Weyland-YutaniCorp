<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USCSS Paladio</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Courier+New&display=swap" rel="stylesheet">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="img/logoIcono.png" type="image/x-icon">

    <!-- CSS -->
    <link rel="stylesheet" href="styles/styles.css">

    <style>
        #audio-control {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }

        #audio-control button {
            background-color: transparent;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #fff;
        }

        #audio-control button:hover {
            color: #ffcc00;
        }
    </style>
</head>

<body id="bodyPantallaInicial">
    <!-- Contenido principal -->
    <div class="aligncon" id="contentPantallaInicial">
        <div class="container-fluid text-center justify-content-center">
            <h1 id="tituloInicial">GESTOR DE TRIPULACIÓN</h1>
            <p>Est. 2024 | MU-TH-UR 6000 | Propiedad de WEYLAND-YUTANI CORP</p>
            <button class="btn btn-outline-warning my-2 btn-lg w-25 btnPersonalizado" id="btnIniciarPrograma"
                onclick="window.location.href='Paladio/inicio.php';">INICIAR PROGRAMA</button>
            <br><br>
            <h6><strong>Desarrollado por:</strong></h6>
            <p>Tomas Audi & Mary Ramirez</p>
        </div>
    </div>

    <!-- Salvapantallas -->
    <div id="screenSaver">
        <video id="videoScreenSaver" autoplay muted loop>
            <source src="video/screenSaverWeylandYutani.mp4" type="video/mp4">
        </video>
    </div>

    <!-- Ajustar la velocidad del video para coincidir con la música -->
    <script>
        const video = document.getElementById("videoScreenSaver");
        video.playbackRate = 0.8;
    </script>

    <!-- Botón de control de música -->
    <div id="audio-control">
        <button onclick="toggleAudio()" id="audioButton">
            <i class="fas fa-volume-up"></i> <!-- Icono de sonido -->
        </button>
    </div>

    <!-- Audio de fondo -->
    <audio id="backgroundAudio" autoplay loop>
        <source src="audio/Ambiente.mp3" type="audio/mp3">
        MU-TH-UR 6000 está experimentando serios problemas para reproducir el audio.
    </audio>

    <!-- Control de audio de fondo -->
    <script>
        const audio = document.getElementById('backgroundAudio');
        const audioButton = document.getElementById('audioButton');
        const audioIcon = audioButton.getElementsByTagName('i')[0];

        function toggleAudio() {
            if (audio.paused) {
                audio.play();
                audioIcon.classList.remove('fa-volume-mute');
                audioIcon.classList.add('fa-volume-up');
            } else {
                audio.pause();
                audioIcon.classList.remove('fa-volume-up');
                audioIcon.classList.add('fa-volume-mute');
            }
        }
    </script>


    <!--SCRIPT SONIDO EN LOS BOTONES-->
    <script>
        // Definir sonido
        const hoverSound = new Audio('sounds/sonido.mp3');

        // Función para reproducir el sonido desde el inicio
        function playSound() {
            hoverSound.currentTime = 0; // Reinicia el sonido
            hoverSound.play();
        }

        // Asignar la función de sonido a cada botón 
        const botones = [//(cambiar variable segun botones en el documento)
            'btnIniciarPrograma'
        ];

        botones.forEach(id => {
            const boton = document.getElementById(id);
            if (boton) {
                boton.addEventListener('mouseover', playSound);
            }
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>