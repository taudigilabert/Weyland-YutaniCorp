<?php
session_start();

include('../includes/db.php');


$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
$success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
unset($_SESSION['error'], $_SESSION['success']); // Elimina mensajes luego de mostrarlo

//Si se ha iniciado sesion, te envia directamente a index.php (menú)
if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Consulta preparada para obtener todos los roles
$query = "SELECT rol_id, rol_nombre FROM roles";

if ($stmt = mysqli_prepare($conn, $query)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $rol_id, $rol_nombre);

    // Array para almacenar los roles
    $roles = [];

    // Obtener resultados y almacenarlos en el array
    while (mysqli_stmt_fetch($stmt)) {
        $roles[] = ['rol_id' => $rol_id, 'rol_nombre' => $rol_nombre];
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Error al preparar la consulta: " . mysqli_error($conn);
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PD: USCSS Paladio</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Courier+New&display=swap" rel="stylesheet"><!-- Maquina -->
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="../img/logoIcono.png" type="image/x-icon">

    <!-- CSS -->
    <link rel="stylesheet" href="../styles/styles.css">

</head>

<body id="bodyInicio">

    <!-- Contenedor principal -->
    <div class="container my-4 text-center mt-5 col-6" id="contenedorPrincipal">
        <!-- Titulo -->
        <br>
        <p>Bienvenido a la nave:</p>
        <h1 class="text-warning" id="USCSSPaladio">USCSS Paladio</h1>
        <img src="../img/logoTransparente.png" alt="Cargando imágen..." id="logoInicio" class="img-fluid">

        <!-- Botones para abrir los modales -->
        <button class="btn btn-outline-warning btn-lg btnPersonalizado my-3" id="btnIniciarSesion"
            data-bs-toggle="modal" data-bs-target="#modalIniciarSesion">Iniciar sesión</button>
        <button class="btn btn-outline-warning btn-lg btnPersonalizado my-1" id="btnRegistro" data-bs-toggle="modal"
            data-bs-target="#modalRegistro">Registrar nuevo tripulante</button>
        <button class="btn btn-outline-warning btn-lg btnPersonalizado my-3" id="btnCerrarPrograma"
            onclick="window.location.href='../index.php';">CERRAR PROGRAMA</button>
    </div>


    <!-- Modal Iniciar sesión -->
    <div class="modal fade" id="modalIniciarSesion" tabindex="-1" aria-labelledby="modalIniciarSesionLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-custom">
                <!-- Modal Header -->
                <div class="modal-header modal-header-custom">
                    <h4 class="modal-title" id="modalIniciarSesionLabel">Iniciar sesión</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <!-- Descripción y formulario -->
                <div class="modal-body modal-body-custom">
                    <!-- LOGO -->
                    <div class="col-12 d-flex justify-content-center align-items-center">
                        <img src="../img/logoTransparente.png" alt="Cargando imágen..." class="img-fluid"
                            id="logoArchivos">
                    </div>
                    <p class="text mb-4">Ingresa tus credenciales para <strong>acceder</strong> al programario.</p>

                    <form method="POST" id="formIniciarSesion">
                        <div class="mb-3">
                            <label for="usuario" class="form-label">
                                <strong>Usuario</strong>
                            </label>
                            <input type="text" class="form-control custom-input" id="usuario" name="usuario"
                                placeholder="Ingresa usuario." required>
                        </div>

                        <div class="mb-3">
                            <label for="contrasena" class="form-label">
                                <strong>Contraseña</strong>
                            </label>
                            <input type="password" class="form-control custom-input" id="contrasena" name="contrasena"
                                placeholder="Ingresa contraseña." required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-warning btn-lg btnPersonalizado"
                                id="btnModalIniciarSesion">Iniciar sesión</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Registro -->
    <div class="modal fade" id="modalRegistro" tabindex="-1" aria-labelledby="modalRegistroLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-custom">
                <div class="modal-header">
                    <h6 class="modal-title" id="modalRegistroLabel">Registrar nuevo tripulante</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <!-- Contenido y formulario -->
                <div class="modal-body-custom">
                    <!-- 📣 ALERTA PARA ERRORES DE API -->
                    <div id="alertRegistro" class="alert alertaErrorCredenciales d-none"></div>

                    <?php if ($error): ?>
                        <div class="alerta alertaErrorCredenciales">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alerta alertaCorrectoCredenciales">
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <!-- LOGO -->
                    <div class="col-12 d-flex justify-content-center align-items-center">
                        <img src="../img/logoTransparente.png" alt="Cargando imágen..." class="img-fluid"
                            id="logoArchivos">
                    </div>
                    <p class="text mb-4">Ingresa los datos para <strong>registrar</strong> un nuevo tripulante.</p>
                    <form method="POST" action="registro.proc.php" id="formRegistro" enctype="multipart/form-data">
                        <!-- Campos de registro -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                <strong>Nombre</strong>
                            </label>
                            <input type="text" class="form-control custom-input" id="nombre" name="nombre"
                                placeholder="Ingresa el nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido" class="form-label">
                                <strong>Apellido</strong>
                            </label>
                            <input type="text" class="form-control custom-input" id="apellido" name="apellido"
                                placeholder="Ingresa el apellido" required>
                        </div>
                        <div class="mb-3">
                            <label for="alias" class="form-label">
                                <strong>Alias</strong>
                            </label>
                            <input type="text" class="form-control custom-input" id="alias" name="alias"
                                placeholder="Ingresa el alias" required>
                        </div>
                        <div class="mb-3">
                            <label for="rol" class="form-label">
                                <strong>Rol</strong>
                            </label>
                            <select class="form-select custom-input" id="rol" name="rol" required>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?= htmlspecialchars($rol['rol_id']) ?>">
                                        <?= htmlspecialchars($rol['rol_nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="genero" class="form-label">
                                <strong>Género</strong>
                            </label>
                            <select class="form-select custom-input" id="genero" name="genero" required>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bio" class="form-label">
                                <strong>Biografía</strong>
                            </label>
                            <textarea class="form-control custom-input" id="bio" rows="3" name="biografia"
                                placeholder="Escribe una breve biografía"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="fotoPerfil" class="form-label">
                                <strong>Subir Foto de Perfil</strong>
                            </label>
                            <input type="file" class="form-control custom-input" id="fotoPerfil" name="fotoPerfil"
                                accept="image/*">
                            <small class="text">Si no subes una imagen, se asignará una imagen por defecto.</small>
                        </div>
                        <!-- Contras -->
                        <div class="mb-3">
                            <label for="contrasenaRegistro" class="form-label">
                                <strong>Contraseña</strong>
                            </label>
                            <input type="password" class="form-control custom-input" id="contrasenaRegistro"
                                name="contrasena" placeholder="Ingresa tu contraseña" required>
                        </div>
                        <div class="mb-3">
                            <label for="verificarContrasenaRegistro" class="form-label">
                                <strong>Verificar Contraseña</strong>
                            </label>
                            <input type="password" class="form-control custom-input" id="verificarContrasenaRegistro"
                                name="contrasenaRepetida" placeholder="Verifica tu contraseña" required>
                            <small id="mensajeError" class="text-danger" style="display: none;">Las contraseñas no
                                coinciden</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Cambiado a type="button" -->
                            <button type="button" class="btn btn-outline-warning btn-lg btnPersonalizado"
                                id="btnModalRegistro" disabled>Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Scripts necesarios para Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        // Abre el modal si hay un mensaje en la sesión que mostrar
        <?php if ($error || $success): ?>
            var modal = new bootstrap.Modal(document.getElementById('modalIniciarSesion'));
            modal.show();
        <?php endif; ?>



        document.getElementById('btnModalIniciarSesion').addEventListener('click', function () {
            // Obtener los valores del formulario
            const alias = document.getElementById('usuario').value;
            const password = document.getElementById('contrasena').value;
            console.log('Alias:', alias, 'Password:', password); // Para depurar

            // Verificar que los campos no estén vacíos
            if (!alias || !password) {
                alert('Por favor, ingresa tu usuario y contraseña.');
                return;
            }

            // Enviar las credenciales al servidor
            fetch('../API/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `usu_alias=${encodeURIComponent(alias)}&usu_contrasena=${encodeURIComponent(password)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        localStorage.setItem('token', data.token);
                        window.location.href = 'index.php';
                    } else if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        alert(data.message);
                    }
                })


                .catch(error => {
                    console.error('Error en la solicitud:', error);
                    alert('Hubo un error al intentar iniciar sesión.');
                });
        });
    </script>

    <script>
        // Validación en vivo de contraseñas
        const pass1 = document.getElementById('contrasenaRegistro');
        const pass2 = document.getElementById('verificarContrasenaRegistro');
        const msgErr = document.getElementById('mensajeError');
        const btnReg = document.getElementById('btnModalRegistro');

        function validarContrasenasEnVivo() {
            if (pass2.value && pass1.value !== pass2.value) {
                msgErr.style.display = 'block';
                btnReg.disabled = true;
            } else {
                msgErr.style.display = 'none';
                btnReg.disabled = (pass2.value === '');
            }
        }

        pass1.addEventListener('input', validarContrasenasEnVivo);
        pass2.addEventListener('input', validarContrasenasEnVivo);

        // Envío del registro vía AJAX a la API
        document.getElementById('btnModalRegistro').addEventListener('click', function () {
            const alerta = document.getElementById('alertRegistro');
            alerta.classList.add('d-none');
            alerta.textContent = '';

            const form = document.getElementById('formRegistro');
            const formData = new FormData(form);

            fetch('../API/registroAPI.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Guarda el token y redirige
                        localStorage.setItem('token', data.token);
                        window.location.href = 'index.php';
                    } else {
                        // Muestra el mensaje de error dentro del modal
                        alerta.textContent = data.message || 'Error en el registro';
                        alerta.classList.remove('d-none');
                    }
                })
                .catch(err => {
                    console.error('Error en la petición de registro:', err);
                    alerta.textContent = 'No se pudo completar el registro.';
                    alerta.classList.remove('d-none');
                });
        });
    </script>




    <script>
        // Validación en tiempo real solo cuando se empieza a escribir en el campo de repetir contraseña
        const contrasena = document.getElementById('contrasenaRegistro');
        const contrasenaRepetida = document.getElementById('verificarContrasenaRegistro');
        const mensajeError = document.getElementById('mensajeError');
        const botonRegistrar = document.getElementById('btnModalRegistro');

        function validarContrasenasEnVivo() {
            // Solo verificar cuando el campo de confirmar contraseña tiene valor
            if (contrasenaRepetida.value && contrasena.value !== contrasenaRepetida.value) {
                mensajeError.style.display = 'block';
                botonRegistrar.disabled = true;
            } else {
                mensajeError.style.display = 'none';
                botonRegistrar.disabled = contrasenaRepetida.value === ''; // Deshabilitar el btn
            }
        }

        contrasenaRepetida.addEventListener('input', validarContrasenasEnVivo);
    </script>


    <!--INCLUDES-->
    <!-- FOOTER -->
    <?php include '../includes/footer.html'; ?>
    <!-- MUSICA-->
    <?php include '../includes/musica.php'; ?>
    <!-- Video de fondo -->
    <?php include '../includes/videoFondo.php'; ?>
    <!-- Sonido en botones -->
    <?php include '../includes/sonidoBotones.php'; ?>
    <!-- Sonido teclas -->
    <script src="../includes/sonidoTeclas.js"></script>

</body>

</html>