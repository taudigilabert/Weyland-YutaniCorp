<?php
session_start();
include('../includes/db.php');
if (!isset($_SESSION['usuario'])) {
    header("Location: inicio.php");
    exit();
}
$esAdmin = in_array($_SESSION['usuario']['rol'], [1, 2]);

// Variables inicializadas para nuevo informe
$inf_id = '';
$inf_concepto = '';
$inf_fecha = date('Y-m-d');
$inf_contenido = '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>USCSS Paladio - Crear / Editar Informe</title>
    <!-- Bootstrap, fuentes e iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Courier+New&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="icon" href="../img/logoIcono.png">
</head>

<body id="bodyInicio">
    <div class="container my-4 col-8 text-center mt-5" id="contenedorPrincipal">
        <div class="container-fluid text-center">
            <!-- LOGO y TÍTULO -->
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-center">
                    <img src="../img/logoTransparente.png" class="img-fluid" id="logoArchivos">
                </div>
                <div class="col-12">
                    <h2 class="text-warning" id="tituloInforme">Crear Informe</h2>
                    <p>
                        Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['usuario']['nombre'] . ' ' . $_SESSION['usuario']['apellido']); ?></strong>
                        al generador de informes de <strong>Weyland-Yutani</strong>.
                        <br>
                        Redacta tus nuevos informes o edita los ya existentes aquí.
                    </p>
                </div>
            </div>
            <!-- FORMULARIO -->
            <div class="row justify-content-center">
                <div class="col-10 text-start">
                    <!-- Formulario -->
                    <div class="row mb-4 justify-content-center">
                        <div class="col-10 text-start">
                            <form id="informeForm" enctype="multipart/form-data">
                                <!-- hidden para el ID del informe -->
                                <input type="hidden" name="inf_id" id="inf_id"
                                    value="<?php echo htmlspecialchars($inf_id); ?>">

                                <!-- Concepto -->
                                <div class="mb-3">
                                    <label for="infConcepto"
                                        class="form-label text-white"><strong>Concepto</strong></label>
                                    <input type="text" class="form-control custom-input" id="infConcepto"
                                        name="inf_concepto" placeholder="Introduce el concepto del informe..."
                                        value="<?php echo htmlspecialchars($inf_concepto); ?>" required>
                                </div>

                                <!-- Fecha -->
                                <div class="mb-3">
                                    <label for="infFecha" class="form-label text-white"><strong>Fecha</strong></label>
                                    <input type="date" class="form-control custom-input" id="infFecha" name="inf_fecha"
                                        value="<?php echo htmlspecialchars($inf_fecha); ?>" required>
                                </div>

                                <!-- Contenido -->
                                <div class="mb-3">
                                    <label for="infContenido"
                                        class="form-label text-white"><strong>Contenido</strong></label>
                                    <!-- Icono de Información -->
                                    <i id="infoIcon" class="fas fa-info-circle icono"></i>
                                    <!-- Texto información desplegable -->
                                    <div id="infoText" style="display: none;">
                                        <p>Los ingenieros de <strong>Weyland-Yutani</strong> están desarrollando un <strong>editor de texto mejorado</strong> para optimizar tu experiencia. </p>

                                    </div>
                                    <!-- TextArea -->
                                    <textarea class="form-control custom-textarea" id="infContenido"
                                        name="inf_contenido" rows="15"
                                        placeholder="Escribe aquí el contenido del informe..."
                                        required><?php echo htmlspecialchars($inf_contenido); ?></textarea>
                                </div>

                                <!-- Campo para subir imágenes -->
                                <div class="mb-3">
                                    <label for="infImagenes"
                                        class="form-label text-white"><strong>Imágenes</strong></label>
                                    <input type="file" class="form-control custom-input" id="infImagenes"
                                        name="inf_imagenes[]" multiple>
                                    <small class="text-warning">Puedes subir varias imágenes (formatos aceptados: JPG,
                                        PNG, GIF).</small>
                                </div>

                                <!-- Contenedor para imágenes existentes -->
                                <div id="imagenesExistentes" class="mb-3 text-start"></div>

                                <!-- Botón guardar -->
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-outline-warning btn-lg w-50 btnPersonalizado"
                                        id="btnGuardarInforme">
                                        Guardar informe
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTONES INFERIORES -->
    <div class="text-center mb-4">
        <button class="btn btn-outline-warning btn-lg w-25 btnPersonalizado"
            onclick="location.href='gestionInformes.php';" id="btnVolver">Volver</button>
        <button class="btn btn-outline-warning btn-lg w-25 btnPersonalizado"
            onclick="location.href='usuarioRegistros.php';" id="btnVerInformes">Ver Informes</button>
    </div>

    <!-- MODAL ELIMINAR IMAGEN -->
    <div class="modal fade" id="modalImg" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content modal-custom">
                <div class="modal-header">
                    <h6 class="modal-title">Eliminar imagen</h6>
                </div>
                <div class="modal-body">¿Seguro que deseas <strong>eliminar</strong> esta imagen?</div>
                <div class="modal-footer">
                    <button class="btn btnPersonalizado" data-bs-dismiss="modal">Cancelar</button>
                    <button id="confirmDeleteImg" class="btn btnPersonalizado">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API = '../API/informesAPI.php';
        const modalImg = new bootstrap.Modal(document.getElementById('modalImg'));
        let imagenAEliminar = null;
        const informeId = new URLSearchParams(window.location.search).get('informe');

        // Elementos
        const form = document.getElementById('informeForm');
        const btnGuardar = document.getElementById('btnGuardar');
        const contImgs = document.getElementById('imagenesExistentes');

        // Cargar datos para editar
        if (informeId) loadInforme(informeId);

        function loadInforme(id) {
            fetch(`${API}?accion=getInforme&inf_id=${id}`)
                .then(r => r.json())
                .then(data => {
                    if (data.error) throw data.error;
                    document.getElementById('tituloInforme').textContent = 'Editar Informe';
                    document.getElementById('inf_id').value = data.informe.inf_id;
                    document.getElementById('infConcepto').value = data.informe.inf_concepto;
                    document.getElementById('infFecha').value = data.informe.inf_fecha;
                    document.getElementById('infContenido').value = data.informe.inf_contenido;

                    // Mostrar imágenes existentes
                    contImgs.innerHTML = '<h6 class="text-white">Imágenes actuales:</h6><div class="d-flex flex-wrap"></div>';
                    const wrap = contImgs.querySelector('div');
                    data.imagenes.forEach(img => {
                        const box = document.createElement('div');
                        box.className = 'm-2 text-center';

                        box.innerHTML =
                            `
                            <div class="img-container" style="position: relative;">
                                <img src="../img/img-subidas/informes/${img.img_ruta}" 
                                    class="imgenInforme" alt="Imagen del informe">
                                <button type="button" class="btn btn-outline-warning btn-sm mt-2 btnPersonalizado" 
                                    style="position: absolute; top: 10px; right: 10px;">Eliminar</button>
                            </div>
                            `;
                        box.querySelector('button').onclick = () => {
                            imagenAEliminar = {
                                img_id: img.img_id,
                                inf_id: id
                            };
                            modalImg.show();
                        };
                        wrap.appendChild(box);
                    });
                })
                .catch(err => {
                    alert('Error cargando informe: ' + err);
                    console.error(err);
                });
        }


        // Confirmar borrado
        document.getElementById('confirmDeleteImg').onclick = () => {
            fetch(`${API}?accion=deleteImagen&img_id=${imagenAEliminar.img_id}&inf_id=${imagenAEliminar.inf_id}`)
                .then(r => r.json())
                .then(res => {
                    if (res.error) throw res.error;
                    modalImg.hide();
                    loadInforme(informeId);
                })
                .catch(err => {
                    alert('No se pudo eliminar: ' + err);
                });
        };

        // Enviar formulario con fetch
        form.addEventListener('submit', e => {
            e.preventDefault();
            const fd = new FormData(form);
            fetch(`${API}?accion=postGuardarInforme`, {
                    method: 'POST',
                    body: fd
                })
                .then(r => r.json())
                .then(res => {
                    if (res.error) throw res.error;
                    // Redirigir a la lista de informes del usuario
                    window.location.href = `usuarioRegistros.php?usuario=${res.usu_id}`;
                })
                .catch(err => {
                    alert('Error: ' + err);
                });
        });

        // Auto‑resize textarea
        document.addEventListener('input', e => {
            if (e.target.classList.contains('custom-textarea')) {
                e.target.style.height = 'auto';
                e.target.style.height = e.target.scrollHeight + 'px';
            }
        });
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


    <script>
        // Obtener los elementos
        const infoIcon = document.getElementById('infoIcon');
        const infoText = document.getElementById('infoText');

        // Función para alternar la visibilidad del texto
        infoIcon.addEventListener('click', function() {
            if (infoText.style.display === "none") {
                infoText.style.display = "block";
            } else {
                infoText.style.display = "none";
            }
        });
    </script>

    <!-- EDITOR DE TEXTO ERIQUECIDO PARA LOS INFORMES (En proceso...) -->
    <!-- TinyMCE (Maquetación de texto)-->
    <!-- <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.2/tinymce.min.js"></script>-->

    <!-- SONIDO TECLAS NO VA -->
    <!-- <script>
        tinymce.init({
            selector: '#infContenido',
            menubar: false,
            plugins: 'lists link',
            toolbar: 'bold italic underline | bullist numlist | link',
            branding: false,
            content_css: false,
            skin: "oxide-dark",
            height: 600,
            font_size: '14px',
            content_style: `
                body {
                    background-color: #000000;
                    color: #f39c12;
                    font-family: 'Courier New', Courier, monospace;
                    font-size: 16px;
                    line-height: 1.5;
                }

                ::selection {
                    background: #f39c12;
                    color: #000000;
                }

                a {
                    color: #00ffaa;
                    text-decoration: underline;
                }

                strong {
                    color: #ccff00;
                }

                em {
                    color: #66ff66;
                }

                ul, ol {
                    margin-left: 20px;
                }

                h1, h2, h3 {
                    color: #ffff00;
                    font-weight: bold;
                    border-bottom: 1px dashed #00ff00;
                    margin-top: 20px;
                }
            `
        });
    </script>
    -->

</body>

</html>