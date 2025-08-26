<?php
session_start();
include('../includes/db.php');

// Si no hay usuario logueado en la sesión, redirige a inicio.php
if (!isset($_SESSION['usuario'])) {
    header("Location: inicio.php");
    exit();
}

// Datos de usuario y roles para mostrar en el menú
$roles = [
    '1' => 'Capitán',
    '2' => 'Primer Oficial',
    '3' => 'Ingeniero Jefe',
    '4' => 'Oficial de Seguridad',
    '5' => 'Ingeniero de Mantenimiento',
    '6' => 'Piloto',
    '7' => 'Oficial Médico',
    '8' => 'Científico Principal'
];

// Verificar si el rol existe en la sesión
$rol = $_SESSION['usuario']['rol'];
$rol_mostrado = isset($roles[$rol]) ? $roles[$rol] : "Desconocido";

// Verificar si el ID existe en la sesión
$usuarioActualID = $_SESSION['usuario']['id'];

// Consulta para contar los mensajes con notificación activada
$sqlNotificaciones = "SELECT COUNT(*) AS total FROM mensaje_receptores WHERE mec_receptor = ? AND mec_notificacion = 1";
$stmtNotificaciones = mysqli_prepare($conn, $sqlNotificaciones);
mysqli_stmt_bind_param($stmtNotificaciones, "i", $usuarioActualID);
mysqli_stmt_execute($stmtNotificaciones);
$resultNotificaciones = mysqli_stmt_get_result($stmtNotificaciones);

if ($rowNotificacion = mysqli_fetch_assoc($resultNotificaciones)) {
    $mensajesNoLeidos = $rowNotificacion['total'];
} else {
    $mensajesNoLeidos = 0;
}
mysqli_stmt_close($stmtNotificaciones);

// Verificar si se debe mostrar la pantalla de carga
$mostrarCarga = isset($_SESSION['mostrar_carga']) && $_SESSION['mostrar_carga'] === true;
unset($_SESSION['mostrar_carga']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USCSS Paladio - Menú Principal</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Courier+New&display=swap" rel="stylesheet">
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="../img/logoIcono.png" type="image/x-icon">
    <!-- CSS -->
    <link rel="stylesheet" href="../styles/styles.css">
</head>

<body id="bodyInicio">
    <!-- Si se debe mostrar la pantalla de carga -->
    <?php if ($mostrarCarga): ?>
        <div id="pantallaCarga" class="pantalla-carga">
            <div class="contenedorSuperior">
                <img src="../img/logoTransparente.png" alt="Cargando imagen..." class="img-fluid" id="logoArchivos">
                <h5>Iniciando sesión...</h5>
                <h6>Cargando, por favor espera...</h6>
            </div>
            <div class="contenedorInferior">
                <video id="videoCarga" autoplay muted playsinline>
                    <source src="../video/BarraDeCarga.mp4" type="video/mp4">
                    Tu navegador no soporta el formato de video.
                </video>
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const pantallaCarga = document.getElementById("pantallaCarga");
                const video = document.getElementById("videoCarga");
                video.playbackRate = 4.5; // Ajustar velocidad
                video.addEventListener("ended", function() {
                    pantallaCarga.style.display = "none";
                });
            });
        </script>
    <?php endif; ?>

    <!-- Contenedor principal -->
    <div class="container-fluid text-center">
        <!-- INFO + LOGO -->
        <div class="row position-relative">
            <div class="position-absolute top-0 start-0 mt-5 d-flex flex-column align-items-start text-center" id="contenedorInfoUsuario">
                <div class="d-flex flex-row align-items-center justify-content-start">
                    <img src="../img/fotoPerfil/<?php echo htmlspecialchars($_SESSION['usuario']['imagen']) ?>"
                        alt="Foto de perfil" class="img-fluid" id="fotoPerfil">
                    <!-- NOMBRE + ROL -->
                    <div class="ms-3">
                        <h6 id="nombreUsuario">Cargando...</h6>
                        <p id="rolUsuario">Cargando...</p>
                        <p id="naveUsuario">Cargando...</p>
                    </div>
                </div>
                <!-- DATOS USUARIO -->
                <div class="mt-3 text-start">
                    <h6>Datos del usuario</h6>
                    <p><strong>Alias</strong>
                        <span id="alias">Cargando...</span>
                    </p>
                    <p><strong>Género</strong>
                        <span id="genero">Cargando...</span>
                    </p>
                    <p><strong>Estado</strong>
                        <span id="estado">Cargando...</span>
                    </p>
                    <p><strong>ID empleado</strong>
                        <span id="empleado">Cargando...</span>
                    </p>
                </div>

                <!-- Personalización -->
                <h6>Personalización</h6>
                <button class="btn btn-outline-warning btn-sm w-100 btnPersonalizado" id="btnEditarPerfil"
                    onclick="window.location.href='../Paladio/perfil.php';">Ver perfil</button>
                <button class="btn btn-outline-warning btn-sm w-100 btnPersonalizado" id="btnPersonalizar"
                    onclick="window.location.href='../Paladio/personalizacion.php';">Opciones HUD</button>

                <!-- Botón de cierre de sesión centrado -->
                <div class="d-flex justify-content-center text-center">
                    <a href="logout.proc.php" class="btn btnPersonalizado btn-sm"
                        style="background-color: var(--color-principal); color:black;" id="btnCerrarSesion">Cerrar
                        sesión</a>
                </div>
            </div>
            <div class="col-12 d-flex justify-content-center align-items-center" style="height: 35vh;">
                <img src="../img/logoTransparente.png" alt="Cargando imágen..." class="img-fluid" id="logoIndex" style="max-height: 100%;">
            </div>
        </div>

        <!-- Menú principal -->
        <div class="row d-flex justify-content-center">
            <div class="col-md-8 d-flex flex-column align-items-center">
                <div class="menu-container col-6" id="menuPrincipal">
                    <h1 class="text-warning">Menú Principal</h1>
                    <h3 id="naveMenu">Cargando...</h3> <!-- NOMBRE DE LA NAVE POR API -->
                    <div class="button-container mt-3">
                        <h6 class="text mt-4 mb-1">Gestor de informes</h6>
                        <button class="btn btn-outline-warning my-2 btn-lg w-100 btnPersonalizado" id="btnInformes" onclick="window.location.href='../Paladio/gestionInformes.php';">Gestión de Informes</button>
                        <h6 class="text mt-4 mb-1">Servicio de mensajería</h6>
                        <button class="btn btn-outline-warning my-2 btn-lg w-100 btnPersonalizado" id="btnEnviarMensaje" onclick="window.location.href='../Paladio/enviarMensaje.php';">Enviar mensaje</button>
                        <button class="btn btn-outline-warning my-2 btn-lg w-100 btnPersonalizado" id="btnBuzonEntrada" onclick="window.location.href='../Paladio/readMensajes.php';">
                            Buzón de entrada <div id="puntoNotificacion"></div>
                        </button>
                        <h6 class="text mt-4 mb-1">Gestor de tripulación</h6>
                        <button class="btn btn-outline-warning my-2 btn-lg w-100 btnPersonalizado" id="btnInformesMed" onclick="window.location.href='../Paladio/gestionTripulacion.php';">Gestión de tripulación</button>
                        <h6 class="text mt-4 mb-1">Registro de movimientos</h6>
                        <button class="btn btn-outline-warning my-2 btn-lg w-100 btnPersonalizado" id="btnRegistroMovi" onclick="window.location.href='../Paladio/registroMovimientos.php';">Consultar registros</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- INCLUDES -->
    <?php include '../includes/videoFondo.php'; ?>
    <?php include '../includes/footer.html'; ?>
    <?php include '../includes/musica.php'; ?>
    <?php include '../includes/sonidoBotones.php'; ?>
    <?php include '../includes/chatbot.php'; ?>

    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ============================== SCRIPTS ==============================-->
    <script>
        // Actualizar notificaciones en el buzón
        function actualizarNotificacionBuzon() {
            const mensajesNoLeidos = <?php echo $mensajesNoLeidos; ?>;
            if (mensajesNoLeidos > 0) {
                document.getElementById('puntoNotificacion').classList.add('mostrarNotificacion');
            } else {
                document.getElementById('puntoNotificacion').classList.remove('mostrarNotificacion');
            }
        }
        actualizarNotificacionBuzon();

        //GET: Datos del usuario
        fetch(`../API/perfilAPI.php?accion=getPerfil`)
            .then(response => response.json())
            .then(data => {
                console.log(data); // Verifica si los datos llegan correctamente
                if (data.mensaje) {
                    console.error('Error:', data.mensaje);
                } else {
                    // Actualiza la información del usuario en la página
                    document.getElementById('nombreUsuario').textContent = `${data.usu_nombre} ${data.usu_apellido}`;
                    document.getElementById('rolUsuario').innerHTML = `<strong>${data.rol_nombre}</strong>`;
                    document.getElementById('naveUsuario').innerHTML = `${data.nav_nombre}`;
                    document.getElementById('naveMenu').innerHTML = `${data.nav_nombre}`;
                    document.getElementById('alias').textContent = data.usu_alias;
                    document.getElementById('genero').textContent = data.usu_genero;
                    document.getElementById('estado').textContent = data.usu_activo === 1 ? 'Activo' : 'Inactivo';
                    document.getElementById('empleado').textContent = data.usu_numero_empleado;

                }
            })
            .catch(error => {
                console.error('Error al obtener los datos del usuario:', error);
            });
    </script>
</body>

</html>