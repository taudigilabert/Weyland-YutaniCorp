<?php
session_start();
include('../includes/db.php');

// Si no hay sesión, redirige a inicio
if (!isset($_SESSION['usuario'])) {
    header("Location: inicio.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USCSS Paladio - Perfil</title>

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
    <!-- Contenedor principal -->
    <div class="container my-4 col-8 text-center mt-5" id="contenedorPrincipal">

        <!-- LOGO -->
        <div class="row">
            <div class="col-12 my-2 d-flex justify-content-center align-items-center">
                <img src="../img/logoTransparente.png" alt="Cargando imágen..." class="img-fluid" id="logoArchivos">
            </div>
        </div>

        <!-- Titulos -->
        <div class="text-center mb-4">
            <h2 class="text-warning">Perfil de Usuario</h2>
            <div>
                <p>Tripulación de</p>
                <h3 id="nombreNaveHeader">USCSS Paladio</h3>
            </div>
            <br>
            <p>Bienvenido/a de nuevo, <strong id="nombreCompletoBienvenida">Cargando...</strong>, consulta y edita tus datos.</p>
        </div>

        <!-- Contenido principal -->
        <div class="row justify-content-center align-items-start mt-4 col-12">

            <!-- Columna Izquierda: Imagen -->
            <div class="col-md-4 d-flex flex-column align-items-center">
                <img src="../img/fotoPerfil/<?php echo htmlspecialchars($_SESSION['usuario']['imagen']) ?>" alt="Foto de perfil" id="fotoPerfil"
                    style="width: 300px; height: auto; margin-bottom: 10px;">
                <h4 class="text-warning mb-0" id="nombreCompleto">Cargando...</h4>
                <h6 id="rolNombre" class="mb-4">Cargando...</h6>
            </div>

            <!-- Columna Derecha: Datos del usuario -->
            <div class="col-md-8">

                <div class="row">
                    <div class="col-md-6 mb-3 text-start">
                        <p><strong>Alias</strong><br><span id="alias">Cargando...</span></p>
                        <p><strong>Género</strong><br><span id="genero">Cargando...</span></p>
                        <p><strong>Nave</strong><br><span id="nombreNave">Cargando...</span></p>
                        <p><strong>Empleado</strong><br><span id="empleado">Cargando...</span></p>

                    </div>
                    <div class="col-md-6 mb-3 text-start">
                        <p><strong>Tipo de Nave</strong><br><span id="tipoNave">Cargando...</span></p>
                        <p><strong>Descripción de la Nave</strong><br><span id="descripcionNave">Cargando...</span></p>
                        <p><strong>Descripción del Rol</strong><br><span id="descripcionRol">Cargando...</span></p>
                        <p><strong>Fecha de alistamiento</strong><br><span id="fechaCreacion">Cargando...</span></p>
                    </div>
                </div>
            </div>

            <!-- Fila completa: Biografía -->
            <div class="col-10 mt-4 text-start">
                <h4 class="text-warning">Biografía</h4>
                <p class="text-light" id="UserBiografia" style="text-align: justify; text-indent: 30px;">
                    Cargando biografía...
                </p>
            </div>

            <!-- Botón editar -->
            <div class="col-12 text-center mt-3">
                <button class="btn btn-outline-warning btn-lg w-25 my-4 btnPersonalizado" id="btnEditarPerfil"
                    data-bs-toggle="modal" data-bs-target="#editarPerfilModal">EDITAR PERFIL</button>
            </div>
        </div>

    </div>

    <!-- Botón volver -->
    <div class="text-center">
        <button class="btn btn-outline-warning btn-lg my-2 w-25 btnPersonalizado"
            id="btnVolverMenu"
            onclick="window.location.href='index.php';">VOLVER AL MENÚ</button>
    </div>


    <!-- Modal para editar perfil -->
    <div class="modal fade" id="editarPerfilModal" tabindex="-1" aria-labelledby="editarPerfilModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-custom">

                <!-- MODAL HEADER -->
                <div class="modal-header d-flex flex-column align-items-center">
                    <!-- LOGO centrado -->
                    <div class="col-8 d-flex justify-content-center align-items-center">
                        <img src="../img/logoTransparente.png" alt="Cargando imágen..." class="img-fluid" id="logoModals">
                    </div>

                    <!-- Título -->
                    <div class="text-center mt-1">
                        <h4 id="editarPerfilModalLabel">EDITOR DE DATOS VARIABLES</h4>
                        <!--<p>Actualiza la información personal y profesional como empleado y tripulante.</p>-->
                    </div>
                </div>


                <div class="modal-body modal-body-scrollable">
                    <!-- Formulario de edición -->
                    <form id="formEditarPerfil" class="row">
                        <!-- Parte izquierda (Imagen de perfil) -->
                        <div class="col-12 col-md-6 d-flex justify-content-center align-items-center mb-3">

                            <div id="imagenPerfilContainer">
                                <!-- ID EMPLEADO alineado a la derecha -->
                                <div class="text-start">
                                    <p class="text-start"><strong>Empleado</strong> <br>
                                        <span id="editIdEmpleado">Cargando id...</span>
                                    </p>
                                </div>
                                <!-- Imagen actual (si existe) -->
                                <!--  -->
                                <img src="../img/fotoPerfil/<?php echo htmlspecialchars($_SESSION['usuario']['imagen']) ?>"
                                    alt="Foto de perfil" class="img-fluid" id="fotoPerfil" style="width: 245px; height: auto;">
                                <div>
                                    <!-- Opción para cambiar la imagen -->
                                    <input type="file" class="form-control custom-input mt-3" id="editImagen" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <!-- Parte derecha (Nombre, Alias, Género) -->
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label for="editNombre" class="form-label"><strong>Primer nombre</strong></label>
                                <input type="text" class="form-control custom-input" id="editNombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="editApellido" class="form-label"><strong>Primer apellido</strong></label>
                                <input type="text" class="form-control custom-input" id="editApellido" required>
                            </div>
                            <div class="mb-3">
                                <label for="editAlias" class="form-label"><strong>Alias</strong></label>
                                <input type="text" class="form-control custom-input" id="editAlias" required>
                            </div>
                            <div class="mb-3">
                                <label for="editGenero" class="form-label"><strong>Género</strong></label>
                                <select class="form-select custom-input" id="editGenero">
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>

                        <!-- Parte inferior (Biografía) -->
                        <div class="col-12 mb-3">
                            <label for="editBiografia" class="form-label"><strong>Biografía</strong></label>
                            <textarea class="form-control custom-input" id="editBiografia" rows="12"></textarea>
                        </div>
                    </form>
                    <p class="text-sm" style="font-size: 1rem;">
                        <strong>ADVERTENCIA!</strong><br>
                        Introducir datos personales fraudulentos puede conllevar sanciones severas, incluyendo la degradación de rango y otras consecuencias legales.
                    </p>
                </div>
                <div class="modal-footer align-items-center">
                    <button type="button" class="btn btn-outline-warning btnPersonalizado w-25" data-bs-dismiss="modal" id="cancelarEdicion">Cancelar</button>
                    <button type="button" class="btn btn-outline-warning btnPersonalizado w-50" id="guardarEdicion">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>




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

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ============================== SCRIPTS ==============================-->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Cargar datos de usuario al cargar la pagina
            let perfilUsuario = null;
            let idUsuario = null;

            //================ GET: Datos del usuario (ventana principal) ================
            fetch(`../API/perfilAPI.php?accion=getPerfil`)
                .then(response => response.json())
                .then(data => {
                    console.log("Respuesta de la API:", data);
                    perfilUsuario = data;

                    idUsuario = data.usu_id || 'Desconocido';

                    // Comprobamos que los elementos existen antes de asignarles valores
                    if (document.getElementById("nombreCompleto")) {
                        document.getElementById("nombreCompleto").textContent = data.usu_nombreCompleto || 'Desconocido';
                    }
                    if (document.getElementById("nombreCompletoBienvenida")) {
                        document.getElementById("nombreCompletoBienvenida").textContent = data.usu_nombreCompleto || 'Desconocido';
                    }
                    if (document.getElementById("nombreUsuario")) {
                        document.getElementById("nombreUsuario").textContent = data.usu_nombre || 'Desconocido';
                    }
                    if (document.getElementById("apellidoUsuario")) {
                        document.getElementById("apellidoUsuario").textContent = data.usu_apellido || 'Desconocido';
                    }
                    if (document.getElementById("nombreNave")) {
                        document.getElementById("nombreNave").textContent = data.nav_nombre || 'Desconocido';
                    }
                    if (document.getElementById("nombreNaveHeader")) {
                        document.getElementById("nombreNaveHeader").textContent = data.nav_nombre || 'Desconocido';
                    }
                    if (document.getElementById("tipoNave")) {
                        document.getElementById("tipoNave").textContent = data.nav_tipo || 'Desconocido';
                    }
                    if (document.getElementById("descripcionNave")) {
                        document.getElementById("descripcionNave").textContent = data.nav_descripcion || 'Sin descripción';
                    }
                    if (document.getElementById("rolNombre")) {
                        document.getElementById("rolNombre").textContent = data.rol_nombre || 'Desconocido';
                    }
                    if (document.getElementById("descripcionRol")) {
                        document.getElementById("descripcionRol").textContent = data.rol_descripcion || 'Sin descripción';
                    }
                    if (document.getElementById("alias")) {
                        document.getElementById("alias").textContent = data.usu_alias || 'Desconocido';
                    }
                    if (document.getElementById("genero")) {
                        document.getElementById("genero").textContent = data.usu_genero || 'Desconocido';
                    }
                    if (document.getElementById("empleado")) {
                        document.getElementById("empleado").textContent = data.usu_numero_empleado || 'Desconocido';
                    }
                    if (document.getElementById("UserBiografia")) {
                        document.getElementById("UserBiografia").textContent = data.usu_biografia || 'Sin biografía';
                    }
                    if (document.getElementById("fechaCreacion")) {
                        document.getElementById("fechaCreacion").textContent = data.usu_fecha_creacion || 'Sin fecha';
                    }

                    // Mostrar botón de editar
                    document.getElementById("btnEditarPerfil").classList.remove("d-none");

                })
                .catch(error => {
                    console.error('Error al obtener el perfil:', error);
                });



            //================ PUT: ACTUALIZAR DATOS DEL USUARIO ================ (esto no acaba de ir fino, no siempre se actualiza y no actuializa la imagen)
            document.getElementById('btnEditarPerfil').addEventListener('click', function() {
                if (!perfilUsuario) {
                    console.warn('Perfil no cargado aún.');
                    return;
                }

                // RELLENAR MODAL CON DATOS ACTUALES
                document.getElementById("editIdEmpleado").textContent = perfilUsuario.usu_numero_empleado || '';
                document.getElementById("editNombre").value = perfilUsuario.usu_nombre || '';
                document.getElementById("editApellido").value = perfilUsuario.usu_apellido || '';
                document.getElementById("editAlias").value = perfilUsuario.usu_alias || '';
                document.getElementById("editGenero").value = perfilUsuario.usu_genero || '';
                document.getElementById("editBiografia").value = perfilUsuario.usu_biografia || '';
            });

            document.getElementById('guardarEdicion').addEventListener('click', function() {
                const id = idUsuario;
                const nombre = document.getElementById('editNombre').value.trim();
                const apellido = document.getElementById('editApellido').value.trim();
                const alias = document.getElementById('editAlias').value.trim();

                // Validación de campos obligatorios
                if (!nombre || !apellido || !alias) {
                    alert('Por favor, completa los campos obligatorios: nombre, apellido y alias.');
                    return;
                }

                actualizarPerfil(id, nombre, apellido, alias);
            });

            function actualizarPerfil(id, nombre, apellido, alias) {
                const genero = document.getElementById('editGenero').value;
                const biografia = document.getElementById('editBiografia').value.trim();
                const imagen = document.getElementById('editImagen').files[0];

                const formData = new FormData();
                formData.append('accion', 'actualizarPerfil');
                formData.append('usu_id', id);
                formData.append('nombre', nombre);
                formData.append('apellido', apellido);
                formData.append('alias', alias);
                formData.append('genero', genero);
                formData.append('biografia', biografia);
                if (imagen) {
                    const urlImagenTemporal = URL.createObjectURL(imagen);
                    document.getElementById("fotoPerfil").src = urlImagenTemporal;
                }

                fetch('../API/perfilAPI.php?accion=actualizarPerfil', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => {
                        // Verifica que la respuesta sea válida
                        if (!res.ok) {
                            throw new Error('Error en la respuesta del servidor');
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.mensaje === "Perfil actualizado con éxito.") {
                            alert("Perfil actualizado exitosamente");

                            // Refrescar con datos actualizados
                            window.location.reload();

                            // Cerrar el modal y actualizar la vista
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editarPerfilModal'));
                            modal.hide();

                            // Actualizar los datos visibles en la página
                            document.getElementById("nombreCompleto").textContent = nombre + ' ' + apellido;
                            document.getElementById("nombreCompletoBienvenida").textContent = nombre + ' ' + apellido;
                            document.getElementById("nombreUsuario").textContent = nombre;
                            document.getElementById("apellidoUsuario").textContent = apellido;
                            document.getElementById("alias").textContent = alias;
                            document.getElementById("genero").textContent = genero;
                            document.getElementById("biografiaUsuario").textContent = biografia;
                            // Actualizar imagen de perfil en pantalla (si se seleccionó una nueva)
                            if (imagen) {
                                const urlImagenTemporal = URL.createObjectURL(imagen);
                                document.getElementById("fotoPerfil").src = urlImagenTemporal;
                            }
                        } else {
                            alert("Hubo un error al actualizar el perfil: " + data.mensaje);
                        }
                    })
                    .catch(err => {
                        console.error("Error al actualizar el perfil:", err);
                    });
            }

        });
    </script>
</body>

</html>