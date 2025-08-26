<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: inicio.php");
    exit();
}

$usuarioActualID = $_SESSION['usuario']['id'];
$usuarioRolID = $_SESSION['usuario']['rol'];

$fechaActual = date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USCSS Paladio - Enviar Mensaje</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="icon" href="../img/logoIcono.png" type="image/x-icon">
    <style>
        .disabled-select {
            background-color: #6c757d !important;
            color: #adb5bd !important;
            pointer-events: none;
        }
    </style>
</head>

<body id="bodyInicio">
    <!-- Contenedor principal -->
    <div class="container my-4 col-8 text-center mt-5" id="contenedorPrincipal">
        <!-- LOGO -->
        <div class="col-12 d-flex justify-content-center align-items-center">
            <img src="../img/logoTransparente.png" alt="Cargando imágen..." class="img-fluid" id="logoArchivos">
        </div>
        <!-- Título -->
        <h2 class="text-warning">Servicio de mensajería interno</h2>
        <h3 class="text-white">Enviar mensaje</h3>
        <p class="text-white">Selecciona un destinatario</p>

        <div class="col-8">
            <!-- Mensajes de error o éxito (si se usan desde sesiones tradicionales) -->
            <?php
            if (isset($_SESSION['error'])) {
                echo "<div class='mensaje-error'>{$_SESSION['error']}</div>";
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo "<div class='mensaje-exito'>{$_SESSION['success']}</div>";
                unset($_SESSION['success']);
            }
            ?>
            <div class="text-start">
                <!-- El formulario ya no tiene action, se identificará con id para el fetch -->
                <form id="mensajeForm" method="POST" enctype="multipart/form-data">
                    <!-- Hidden para incluir el remitente automático -->
                    <input type="hidden" name="remitente" value="<?php echo $usuarioActualID; ?>">

                    <!-- Asunto del mensaje -->
                    <div class="mb-3">
                        <label for="asunto" class="form-label text-white"><strong>Asunto</strong></label>
                        <input type="text" id="asunto" name="asunto" class="form-control custom-input"
                            placeholder="Escribe el asunto del mensaje...">
                    </div>

                    <!-- Si el usuario es ADMIN, puede elegir entre un rol o usuario/s -->
                    <?php if ($usuarioRolID === 1): ?>
                        <div class="mb-3">
                            <label for="rol_destinatario" class="form-label text-white"><strong>Selecciona un rol
                                    (opcional)</strong></label>
                            <select id="rol_destinatario" name="rol_destinatario" class="form-select custom-input">
                                <option value="">-- Ningún rol --</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <!-- Selección de destinatarios -->
                    <div class="mb-3">
                        <label for="destinatario" class="form-label text-white"><strong>Selecciona
                                a los tripulantes</strong></label>
                        <select id="destinatario" name="destinatarios[]" class="form-select custom-input"
                            style="max-width: 100%; width: 100%; height: 200px;" multiple required>
                            <!-- Se llenará mediante fetch -->
                        </select>
                        <small class="text-warning">Mantén presionada la tecla Ctrl para seleccionar a varios o
                            deseleccionar único.</small>
                    </div>

                    <!-- Fecha y hora actual -->
                    <input type="hidden" name="fecha" value="<?php echo $fechaActual; ?>">

                    <!-- Contenido del mensaje -->
                    <div class="mb-2">
                        <label for="contenido" class="form-label text-white"><strong>Mensaje</strong></label>
                        <textarea id="contenido" name="contenido" class="form-control custom-input" rows="4"
                            placeholder="Escribe tu mensaje aquí..." required></textarea>
                    </div>

                    <!-- Adjuntar archivo -->
                    <div class="mb-3">
                        <label for="archivo" class="form-label text-white"><strong>Adjuntar archivo</strong></label>
                        <input type="file" id="archivo" name="archivo[]" class="form-control custom-input" multiple>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-outline-warning my-4 btn-lg w-50 btnPersonalizado"
                            id="btnEnviarMensaje">
                            Enviar mensaje
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="text-center">
        <button class="btn btn-outline-warning my-2 btn-lg w-25 btnPersonalizado"
            onclick="window.location.href='index.php';" id="btnVolverMenu">VOLVER AL MENÚ</button>
        <button class="btn btn-outline-warning btn-lg w-25 btnPersonalizado"
            onclick="window.location.href='readMensajes.php';" id="btnBuzonEntrada">Buzón de entrada</button>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const rolSelect = document.getElementById("rol_destinatario");
            const usuarioSelect = document.getElementById("destinatario");
            const mensajeForm = document.getElementById("mensajeForm");

            // Función para select de roles
            function cargarRoles() {
                fetch('../API/mensajeriaAPI.php?accion=getRoles')
                    .then(response => response.json())
                    .then(data => {
                        if (data.roles && Array.isArray(data.roles)) {
                            data.roles.forEach(role => {
                                let option = document.createElement("option");
                                option.value = role.rol_id;
                                option.textContent = role.rol_nombre;
                                rolSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => console.error('Error cargando roles:', error));
            }

            // Función para select de usuarios
            function cargarUsuarios() {
                fetch('../API/mensajeriaAPI.php?accion=getUsuarios')
                    .then(response => response.json())
                    .then(data => {
                        if (data.usuarios && Array.isArray(data.usuarios)) {
                            data.usuarios.forEach(user => {
                                let option = document.createElement("option");
                                option.value = user.usu_id;
                                option.textContent = user.usu_nombre + " " + user.usu_apellido;
                                // Si se pasó un destinatario por URL se selecciona automáticamente
                                <?php if (isset($destinatarioID)) { ?>
                                    if (user.usu_id == <?php echo $destinatarioID; ?>) {
                                        option.selected = true;
                                    }
                                <?php } ?>
                                usuarioSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => console.error('Error cargando usuarios:', error));
            }

            // Si hay ?responder_a en la URL, tomamos ese ID:
            const params = new URLSearchParams(window.location.search);
            const responderA = params.get('responder_a');
            if (responderA) {
                fetch(`../API/mensajeriaAPI.php?accion=getMensaje&mensaje_id=${responderA}`)
                    .then(res => res.ok ? res.json() : Promise.reject(res.status))
                    .then(data => {
                        // Rellenar el input de asunto
                        const inputAsunto = document.getElementById('asunto');
                        inputAsunto.value = data.mensaje.men_asunto;
                    })
                    .catch(err => console.error('No se pudo cargar el mensaje:', err));
            }

            // Llamamos a las funciones para cargar roles y usuarios
            if (rolSelect) cargarRoles();
            cargarUsuarios();

            // Función para sincronizar los select (deshabilitar uno si se selecciona en el otro)
            function toggleSelects() {
                if (rolSelect && rolSelect.value) {
                    usuarioSelect.disabled = true;
                    usuarioSelect.classList.add("disabled-select");
                    usuarioSelect.value = "";
                } else {
                    usuarioSelect.disabled = false;
                    usuarioSelect.classList.remove("disabled-select");
                }

                if (usuarioSelect.selectedOptions.length > 0) {
                    if (rolSelect) {
                        rolSelect.disabled = true;
                        rolSelect.classList.add("disabled-select");
                    }
                } else {
                    if (rolSelect) {
                        rolSelect.disabled = false;
                        rolSelect.classList.remove("disabled-select");
                    }
                }
            }

            if (rolSelect) {
                rolSelect.addEventListener("change", function () {
                    if (rolSelect.value === "") {
                        rolSelect.value = "";
                        usuarioSelect.disabled = false;
                        usuarioSelect.classList.remove("disabled-select");
                    }
                    toggleSelects();
                });
            }

            usuarioSelect.addEventListener("change", toggleSelects);

            // Enviar mensaje
            mensajeForm.addEventListener("submit", function (e) {
                e.preventDefault();

                const formData = new FormData(mensajeForm);
                fetch('../API/mensajeriaAPI.php?accion=postEnviarMensaje', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            mostrarMensajeError(data.error);
                        } else if (data.success) {
                            mostrarMensajeExito(data.success);
                            mensajeForm.reset();
                        }
                    })
                    .catch(error => {
                        console.error('Error al enviar el mensaje:', error);
                    });
            });
        });

        function mostrarMensajeExito(mensaje) {
            const alerta = document.createElement('div');
            alerta.className = 'alert alert-success mt-3';
            alerta.setAttribute('role', 'alert');
            alerta.innerHTML = `${mensaje}`;

            // Insertar después del título "Enviar mensaje"
            const titulo = document.querySelector('h3');
            titulo.parentNode.insertBefore(alerta, titulo.nextSibling);

            setTimeout(() => {
                alerta.remove();
            }, 5000);
        }

        function mostrarMensajeError(mensaje) {
            const alerta = document.createElement('div');
            alerta.className = 'alert alert-danger mt-3';
            alerta.setAttribute('role', 'alert');
            alerta.innerHTML = `
        <strong>Error!</strong> ${mensaje}
    `;

            const titulo = document.querySelector('h3');
            titulo.parentNode.insertBefore(alerta, titulo.nextSibling);

            setTimeout(() => {
                alerta.remove();
            }, 5000);
        }
    </script>

    <!-- INCLUDES -->
    <!-- Video de fondo -->
    <?php include '../includes/videoFondo.php'; ?>
    <!-- FOOTER -->
    <?php include '../includes/footer.html'; ?>
    <!-- MUSICA -->
    <?php include '../includes/musica.php'; ?>
    <!-- Sonido en botones -->
    <?php include '../includes/sonidoBotones.php'; ?>
    <!-- Sonido teclas -->
    <script src="../includes/sonidoTeclas.js"></script>
</body>

</html>