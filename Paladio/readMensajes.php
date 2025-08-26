<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: inicio.php");
    exit();
}

$usuarioActualID = $_SESSION['usuario']['id'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USCSS Paladio - Buzón de Entrada</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Courier+New&display=swap" rel="stylesheet">
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="../img/logoIcono.png" type="image/x-icon">
    <!-- CSS -->
    <link rel="stylesheet" href="../styles/styles.css?v=1234">
</head>

<body id="bodyInicio">
    <!-- Contenedor principal -->
    <div class="container my-4 col-8 text-center mt-5" id="contenedorPrincipal">
        <div class="row">
            <!-- LOGO -->
            <div class="col-12 d-flex justify-content-center align-items-center">
                <img src="../img/logoTransparente.png" alt="Cargando imágen..." class="img-fluid" id="logoArchivos">
            </div>
            <div class="col-12 text-center">
                <h2 class="text-warning">Servicio de mensajería interno</h2>
            </div>
            <div class="col-12 text-center mb-5">
                <h3>Buzón de entrada</h3>
            </div>
            <!-- Filtro de búsqueda NO FUNCIONA-->
            <div class="row justify-content-center mt-4 mb-4">
                <div class="col-12 col-md-8">
                    <form action="buscarMensaje.php" method="get" class="buscador">
                        <input type="text" class="form-control input-busqueda" name="buscar"
                            placeholder="Buscar mensajes..."
                            value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                        <button class="btn btn-lg btn-outline-warning btnPersonalizado ms-3" type="submit"
                            id="buscarMensaje">Buscar</button>
                    </form>
                </div>
            </div>

            <div class="row d-flex align-items-center justify-content-center">
                <div class="col-8 text-center">
                    <?php
                    if (isset($_SESSION['mensaje_exito'])) {
                        echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_SESSION['mensaje_exito']) . '</div>';
                        unset($_SESSION['mensaje_exito']);
                    }
                    if (isset($_SESSION['mensaje_error'])) {
                        echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['mensaje_error']) . '</div>';
                        unset($_SESSION['mensaje_error']);
                    }
                    ?>
                    <!-- Aquí inyectaremos la lista de mensajes -->
                    <div class="list-group" id="mensajesList">
                        <p class="text-warning">Cargando mensajes...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- BOTONES -->
    <div class="text-center">
        <button class="btn btn-outline-warning my-2 btn-lg w-25 btnPersonalizado" id="btnVolverMenu"
            onclick="window.location.href='../Paladio/index.php';">Volver a menú</button>
    </div>

    <!-- Modal Borrar (reutilizado dinámicamente) -->
    <div class="modal fade" id="modalBorrar" tabindex="-1" aria-labelledby="modalBorrarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-custom">
                <div class="modal-header">
                    <h6 class="modal-title" id="modalBorrarLabel">Confirmar Borrar</h6>
                </div>
                <div class="modal-body modal-body-custom">
                    ¿Estás seguro de que quieres <strong>borrar</strong> este mensaje?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btnPersonalizado" data-bs-dismiss="modal">Cancelar</button>
                    <button id="confirmDeleteBtn" class="btn btnPersonalizado">Borrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- INCLUDES -->
    <?php include '../includes/videoFondo.php'; ?>
    <?php include '../includes/footer.html'; ?>
    <?php include '../includes/musica.php'; ?>
    <?php include '../includes/sonidoBotones.php'; ?>
    <!-- Sonido teclas -->
    <script src="../includes/sonidoTeclas.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const mensajesList = document.getElementById("mensajesList");
            let mensajeAEliminar = null;
            const API_URL = '../API/mensajeriaAPI.php';

            function loadMensajes() {
                fetch(`${API_URL}?accion=getMensajes`, {
                    credentials: "same-origin"
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                console.error("Error HTTP cargando mensajes:", response.status, text);
                                throw new Error("Error HTTP " + response.status);
                            });
                        }
                        // todo ok, parsear JSON
                        return response.json();
                    })
                    .then(data => {
                        console.log("getMensajes response:", data);
                        mensajesList.innerHTML = "";
                        if (data.mensajes && data.mensajes.length) {
                            data.mensajes.forEach(m => {
                                // Item de la lista
                                const item = document.createElement("a");
                                item.href = "#";
                                item.className = "list-group-item list-group-item-action mb-3 d-flex align-items-center item-mensaje";
                                item.setAttribute("data-bs-toggle", "modal");
                                item.setAttribute("data-bs-target", `#modalMensaje-${m.men_id}`);
                                item.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-start">
                                <h6 class="mb-0 me-2">${m.usu_nombre} ${m.usu_apellido}</h6>
                                <p class="mb-0 me-2 text-truncate" style="max-width: 100px;">
                                    <strong>${m.men_asunto}</strong>
                                </p>
                                <small class="text-truncate" style="width: 80px; color:gray">${m.men_fecha}</small>
                            </div>
                        </div>`;
                                mensajesList.appendChild(item);

                                // Modal para ver mensaje
                                const modalContainer = document.createElement("div");
                                modalContainer.innerHTML = `
                                    <div class="modal fade" id="modalMensaje-${m.men_id}" tabindex="-1" aria-labelledby="modalLabel-${m.men_id}" aria-hidden="true">
                                    <div class="modal-dialog modal-custom">
                                        <div class="modal-content modal-custom">
                                        <div class="modal-header modal-title">
                                            <h6 class="modal-title" id="modalLabel-${m.men_id}">${m.usu_nombre} ${m.usu_apellido}</h6>
                                        </div>
                                        <div class="modal-body modal-body-custom text-start">
                                            <strong>${m.men_asunto}</strong>
                                            <p>${m.men_contenido.replace(/\n/g, '<br>')}</p>
                                            <small style="color:gray">${m.men_fecha}</small>
                                            ${m.imagenes.length
                                        ? `<div class="mt-3"><h6>Imágenes adjuntas:</h6>` +
                                        m.imagenes.map(img => `<img src="../img/img-subidas/mensajes/${img}" class="img-fluid mb-2">`).join("") +
                                        `</div>`
                                        : ""
                                    }
                                        </div>
                                        <div class="modal-footer">
                                            <form action="enviarMensaje.php" method="get" class="me-auto">
                                            <input type="hidden" name="destinatario" value="${m.remitente_id}">
                                            <input type="hidden" name="responder_a" value="${m.men_id}">
                                            <button type="submit" class="btn btn-outline-warning btnPersonalizado">Responder</button>
                                            </form>
                                            <button class="btn btn-sm btnPersonalizado text-danger" data-bs-toggle="modal"
                                                data-bs-target="#modalBorrar" onclick="prepareDelete(${m.men_id})">
                                            <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                        </div>
                                    </div>
                                    </div>`;
                                mensajesList.appendChild(modalContainer.firstElementChild);
                            });
                        } else {
                            mensajesList.innerHTML = `<p class="text-warning">No tienes mensajes en tu bandeja de entrada.</p>`;
                        }
                    })
                    .catch(err => {
                        console.error("Error cargando mensajes:", err);
                        mensajesList.innerHTML = `<p class="text-danger">Error al cargar mensajes.</p>`;
                    });
            }

            window.prepareDelete = id => {
                mensajeAEliminar = id;
            };

            document.getElementById("confirmDeleteBtn").addEventListener("click", () => {
                if (!mensajeAEliminar) return;
                fetch(`${API_URL}?accion=deleteMensaje`, {
                    method: 'POST',
                    credentials: "same-origin",
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ mensaje_id: mensajeAEliminar })
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                console.error("Error HTTP borrando mensaje:", response.status, text);
                                throw new Error("Error HTTP " + response.status);
                            });
                        }
                        return response.json();
                    })
                    .then(res => {
                        console.log("deleteMensaje response:", res);
                        if (res.success) {
                            const borrarModal = bootstrap.Modal.getInstance(document.getElementById('modalBorrar'));
                            borrarModal.hide();
                            loadMensajes();
                        } else {
                            alert(res.error || "Error al borrar");
                        }
                    })
                    .catch(err => {
                        console.error("Error borrando mensaje:", err);
                        alert("Error al borrar mensaje.");
                    });
            });

            document.body.addEventListener('shown.bs.modal', e => {
                if (!e.target.id.startsWith('modalMensaje-')) return;
                const id = e.target.id.split('-')[1];

                fetch(`${API_URL}?accion=actualizarNotificacion`, {
                    method: 'POST',
                    credentials: "same-origin",
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ mensaje_id: id })
                }).catch(console.error);
            });


            // Carga inicial
            loadMensajes();
        });
    </script>

</body>

</html>