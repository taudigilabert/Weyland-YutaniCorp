<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: inicio.php");
    exit();
}

if (!isset($_GET['informe'])) {
    echo "No se ha especificado un informe.";
    exit();
}

$inf_id = (int) $_GET['informe'];
$usuarioLogueadoId = $_SESSION['usuario']['id'];

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USCSS Paladio - Consultar Informe</title>

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
        <div class="container-fluid text-center">
            <!-- LOGO -->
            <div class="row">
                <div class="col-12 d-flex justify-content-center align-items-center">
                    <img src="../img/logoTransparente.png" alt="Cargando imágen..." class="img-fluid" id="logoArchivos">
                </div>
            </div>

            <!-- Título de la página -->
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="text-warning mb-0">
                        Gestión de informes
                        <i id="personalizarIcono" class="fas fa-cog fa-sm text-warning ms-2 icono"
                            style="cursor:pointer;"></i>
                    </h2>
                    <p id="textoBienvenida" class="text-white"></p>
                </div>
            </div>

            <!-- CONTROLES DE LECTURA -->
            <div id="controlesLectura" class="col-2 my-4 mx-auto">
                <div class="mb-2">
                    <div class="d-flex justify-content-start align-items-center gap-2">
                        <p class="mb-0 text-white">Tamaño</p>
                        <div class="d-flex">
                            <i id="reducirTexto" class="fas fa-minus fa-lg text-warning icono"></i>
                            <i id="aumentarTexto" class="fas fa-plus fa-lg text-warning icono"></i>
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <label for="fuenteSelector" class="form-label">
                        <p>Fuente</p>
                    </label>
                    <select id="fuenteSelector" class="form-select w-auto d-inline-block me-3">
                        <option value="'Courier New', monospace">Courier New</option>
                        <option value="'Source Sans 3', sans-serif">Source Sans 3</option>
                        <option value="'Roboto', sans-serif">Roboto</option>
                        <option value="'Merriweather', serif">Merriweather</option>
                        <option value="'Open Sans', sans-serif">Open Sans</option>
                        <option value="'Lora', serif">Lora</option>
                        <option value="'Montserrat', sans-serif">Montserrat</option>
                        <option value="'Raleway', sans-serif">Raleway</option>
                    </select>
                </div>
            </div>


            <!-- Recuadro de informe -->
            <div class="row mb-4 justify-content-center">
                <div class="col-12 col-md-10">
                    <div class="text-start">
                        <h4 id="informeConcepto" class="text-white"></h4>
                        <p><span><strong id="fechaInforme" class="text-white"></strong></span></p>
                    </div>
                    <div class="text-start">
                        <strong>CONTENIDO DEL INFORME</strong>
                        <div id="informeContenido" class="text-white"></div>
                    </div>

                    <!-- Sección de imágenes adjuntas -->
                    <div class="row mt-4">
                        <div class="col-12 text-start">
                            <strong>IMÁGENES ADJUNTAS</strong>
                            <p id="sinImagenesTexto" class="text-warning">No hay imágenes adjuntas.</p>
                            <div class="d-flex flex-wrap" id="informeImagenes"></div>
                        </div>
                    </div>

                    <button id="btnEditarInforme" class="btn btn-outline-warning btn-lg w-50 btnPersonalizado mt-4"
                        onclick="location.href='write.php?informe=<?php echo $inf_id; ?>';">
                        EDITAR INFORME
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- BOTONES -->
    <div class="container">
        <div class="row text-center justify-content-center">
            <div class="col-12 col-md-3 my-2">
                <button class="btn btn-outline-warning btn-lg w-100 btnPersonalizado"
                    onclick="location.href='index.php';" id="btnVolverMenu">VOLVER AL MENÚ</button>
            </div>
            <div class="col-12 col-md-6 my-2">
                <button class="btn btn-outline-warning btn-lg w-100 btnPersonalizado"
                    onclick="location.href='usuarioRegistros.php';" id="btnVerInformes">Ver Informes</button>
            </div>
        </div>
    </div>


    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const infId = <?php echo $inf_id; ?>;
            const usuarioLogueadoId = <?php echo $usuarioLogueadoId; ?>;
            const apiUrl = `../API/informesAPI.php?accion=getInforme&inf_id=${infId}`;
            const nombreUser = "<strong> <?php echo $_SESSION['usuario']['nombre'] . " " . $_SESSION['usuario']['apellido'] ?> </strong>";


            const informeConcepto = document.getElementById("informeConcepto");
            const fechaInforme = document.getElementById("fechaInforme");
            const informeContenido = document.getElementById("informeContenido");
            const textoBienvenida = document.getElementById("textoBienvenida");
            const contenedorImgs = document.getElementById("informeImagenes");

            fetch(apiUrl)
                .then(r => r.json())
                .then(data => {
                    console.log(data);
                    if (data.error) throw new Error(data.error);
                    if (!data.informe) throw new Error("Informe no encontrado");

                    const inf = data.informe;
                    informeConcepto.textContent = inf.inf_concepto || "Sin concepto";
                    fechaInforme.textContent = inf.inf_fecha || "Fecha no disponible";
                    informeContenido.innerHTML = inf.inf_contenido || "Sin contenido.";

                    // Compara el usu_id de la respuesta con el usuario logueado
                    if (parseInt(data.usu_id) === parseInt(usuarioLogueadoId)) {
                        textoBienvenida.innerHTML = `Bienvenido a tu informe, ` + nombreUser;
                    } else {
                        textoBienvenida.textContent = `Estás viendo un informe de otro usuario.`;
                    }

                    // Manejo de imágenes
                    // Dentro del .then(data => { ... })
                    contenedorImgs.innerHTML = "";
                    if (data.imagenes && data.imagenes.length > 0) {
                        document.getElementById("sinImagenesTexto").style.display = "none";
                        data.imagenes.forEach(img => {
                            const div = document.createElement("div");
                            div.className = "m-2";
                            div.innerHTML =
                                `
                                <img src="../img/img-subidas/informes/${img.img_ruta}" 
                                    class="imgenInforme""
                                    alt="Imagen informe">
                            `;

                            contenedorImgs.appendChild(div);
                        });
                    } else {
                        document.getElementById("sinImagenesTexto").style.display = "block";
                    }
                })
                .catch(err => {
                    informeContenido.textContent = "Error al cargar el informe: " + err.message;
                    textoBienvenida.textContent = "";
                    console.error(err);
                });


            // PERSONALIZACIÓN
            const panel = document.getElementById("controlesLectura");
            const icono = document.getElementById("personalizarIcono");
            icono.addEventListener("click", () => {
                panel.style.display = panel.style.display === "block" ? "none" : "block";
            });

            document.getElementById("fuenteSelector")
                .addEventListener("change", e => {
                    informeContenido.style.fontFamily = e.target.value;
                });

            let tam = 16;
            informeContenido.style.fontSize = tam + "px";
            document.getElementById("aumentarTexto")
                .addEventListener("click", () => informeContenido.style.fontSize = (tam += 2) + "px");
            document.getElementById("reducirTexto")
                .addEventListener("click", () => informeContenido.style.fontSize = (tam = Math.max(10, tam - 2)) + "px");
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
</body>

</html>