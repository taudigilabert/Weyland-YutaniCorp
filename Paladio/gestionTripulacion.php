<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>USCSS Paladio - Gestión de Tripulación</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fuentes e iconos -->
    <link href="https://fonts.googleapis.com/css2?family=Courier+New&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- CSS propio -->
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="icon" href="../img/logoIcono.png">
</head>

<body id="bodyInicio">
    <div class="container my-4 col-8 text-center mt-5" id="contenedorPrincipal">
        <!-- LOGO -->
        <div class="row">
            <div class="col-12 d-flex justify-content-center align-items-center">
                <img src="../img/logoTransparente.png" alt="Cargando imágen..." class="img-fluid" id="logoArchivos">
            </div>
        </div>
        <h2 class="text-warning mb-4">Gestión de Tripulación</h2>
        <p class="text-center mb-4">Bienvenido al <strong>gestor de la tripulación</strong> . Seleciona un campo del tripulante para editar sus datos.</p>


        <!-- FILTROS -->
        <div class="row mb-3 g-3">
            <div class="col">
                <select id="filtroRol" class="form-select"></select>
            </div>
            <div class="col">
                <select id="filtroActivo" class="form-select"></select>
            </div>
            <div class="col">
                <select id="orden" class="form-select">
                    <option value="ASC">A-Z</option>
                    <option value="DESC">Z-A</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-warning w-100 btnPersonalizado" onclick="cargarTripulacion()" id="btnFiltrar">Filtrar</button>
            </div>
        </div>

        <!-- TABLA -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Alias</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Rol</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="tablaTripulacion"></tbody>
            </table>
        </div>

        <!-- AVISO DE ADVERTENCIA -->
        <div class="col-10 text-small mb-4">
            <strong>Weyland-Yutani:</strong> La manipulación fraudulenta de los datos de la tripulación constituye una violación grave del protocolo de seguridad. Toda acción será auditada y su responsable severamente sancionado.
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-custom">
                <div class="modal-header">
                    <h6>Confirmar eliminación</h6>
                </div>
                <div class="modal-body">
                    ¿Seguro que deseas <strong>eliminar (inactivar)</strong> a este tripulante?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-warning btnPersonalizado" data-bs-dismiss="modal">Cancelar</button>
                    <button id="confirmDeleteBtn" type="button" class="btn btn-outline-warning btnPersonalizado">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center my-3">
        <button class="btn btn-outline-warning btn-lg w-25 btnPersonalizado" onclick="location.href='index.php'" id="btnVolverMenu">
            VOLVER AL MENÚ
        </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API = '../API/gestionTripulacionAPI.php';
        let idAEliminar = null;
        const modal = new bootstrap.Modal(document.getElementById('modalEliminar'));

        // 1) Carga del filtro de roles
        function loadRoles() {
            fetch(`${API}?accion=getRoles`)
                .then(r => r.json())
                .then(roles => {
                    const sel = document.getElementById('filtroRol');
                    sel.innerHTML = '<option value="">Todos</option>';
                    roles.forEach(r => {
                        sel.innerHTML += `<option value="${r.rol_id}">${r.rol_nombre}</option>`;
                    });
                });
        }

        // 2) Carga del filtro de estados Y disparo de carga inicial
        function loadEstados() {
            fetch(`${API}?accion=getEstados`)
                .then(r => r.json())
                .then(est => {
                    const sel = document.getElementById('filtroActivo');
                    sel.innerHTML = '';
                    est.forEach(e => {
                        sel.innerHTML += `<option value="${e.activo}">${e.nombre}</option>`;
                    });
                    // Fijamos por defecto "Activos"
                    sel.value = '1';
                    cargarTripulacion();
                });
        }

        // 3) Listar tripulantes con los filtros ya establecidos
        function cargarTripulacion() {
            const rol = document.getElementById('filtroRol').value;
            const activo = document.getElementById('filtroActivo').value;
            const orden = document.getElementById('orden').value;

            fetch(`${API}?accion=getTripulacion&rol=${rol}&activo=${activo}&orden=${orden}`)
                .then(r => r.json())
                .then(data => {
                    const tb = document.getElementById('tablaTripulacion');
                    tb.innerHTML = '';
                    data.forEach(u => {
                        tb.innerHTML += `
            <tr data-id="${u.usu_id}">
              <td class="align-middle">
                <img src="../img/fotoPerfil/${u.usu_imagen}"
                     onerror="this.src='../img/fotoPerfil/default.jpg'"
                     alt="Avatar ${u.usu_alias}"
                     width="40" height="40">
              </td>
              <td>
                <input name="usu_alias" class="form-control form-control-sm"
                       value="${u.usu_alias}"
                       onchange="editarTripulante(this)">
              </td>
              <td>
                <input name="usu_nombre" class="form-control form-control-sm"
                       value="${u.usu_nombre}"
                       onchange="editarTripulante(this)">
              </td>
              <td>
                <input name="usu_apellido" class="form-control form-control-sm"
                       value="${u.usu_apellido}"
                       onchange="editarTripulante(this)">
              </td>
              <td>${u.rol_nombre}</td>
              <td class="text-center">
                <i class="fas fa-trash icono-eliminar icono" onclick="confirmarEliminacion(${u.usu_id})"></i>
              </td>
            </tr>`;
                    });
                });
        }

        // Editar alias/nombre/apellido
        function editarTripulante(el) {
            const tr = el.closest('tr');
            const fd = new FormData();
            fd.append('usu_id', tr.dataset.id);
            fd.append('usu_alias', tr.querySelector('input[name="usu_alias"]').value);
            fd.append('usu_nombre', tr.querySelector('input[name="usu_nombre"]').value);
            fd.append('usu_apellido', tr.querySelector('input[name="usu_apellido"]').value);

            fetch(`${API}?accion=putTripulante`, {
                    method: 'POST',
                    body: fd
                })
                .then(r => r.json())
                .then(resp => {
                    if (resp.error) console.error(resp.error);
                });
        }

        // Abrir modal de eliminación
        function confirmarEliminacion(id) {
            idAEliminar = id;
            modal.show();
        }

        // Confirmar y ejecutar eliminación (inactivación)
        document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
            const fd = new FormData();
            fd.append('usu_id', idAEliminar);
            fd.append('usu_activo', 0);
            fetch(`${API}?accion=putActivo`, {
                    method: 'POST',
                    body: fd
                })
                .then(r => r.json())
                .then(resp => {
                    if (!resp.error) {
                        modal.hide();
                        cargarTripulacion();
                    }
                });
        });

        // 4) Inicializamos solo loadRoles +loadEstados
        document.addEventListener('DOMContentLoaded', () => {
            loadRoles();
            loadEstados();
        });
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