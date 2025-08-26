<script>
    // SCRIPT SONIDO EN LOS BOTONES

    // Definir sonido
    const hoverSound = new Audio('../sounds/sonido.mp3');

    // Función para reproducir el sonido desde el inicio
    function playSound() {
        hoverSound.currentTime = 0; // Reinicia el sonido
        hoverSound.play();
    }

    const botones = [
        'btnAllWriteVolverMenu',
        'btnEnviarMensaje',
        'btnVolverMenu',
        'btnVolverInicio1',
        'btnVolverInicio2',
        'btnNuevoInformeDiv',
        'btnMisInformes',
        'btnTodosInformes',
        'btnBuzonEntrada',
        'btnPersonalizar',
        'btnIniciarSesion',
        'btnRegistro',
        'btnCerrarPrograma',
        'btnModalIniciarSesion',
        'btnEditarPerfil',
        'btnEditarInforme',
        'btnCerrarSesion',
        'btnReadVolver',
        'btnResponder',
        'btnReadEditarRegistro',
        'btnGuardarInforme',
        'btnVerInformes',
        'btnCerrarSesion',
        'btnReadVolver',
        'btnModalRegistro',
        'btnInformesMed',
        'btnRegistroMovi',
        'btnVolver',
        'btnResponder',
        'buscarMensaje',
        'btnInformes',
        'btnGuardarCambios',
        'btnVerPerfil',
        'cancelarEdicion',
        'guardarEdicion',
        'btnFiltrar'
    ];

    botones.forEach(id => {
        const boton = document.getElementById(id);
        if (boton) {
            boton.addEventListener('mouseover', playSound);
        }
    });

</script>