<?php
session_start();
// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
  header("Location: inicio.php");
  exit();
}

// Determinar qué usuario se va a mostrar: si es administrador, se puede pasar un "usuario" por GET; de lo contrario, se usa el logueado.
$rolesPermitidos = ['1', '2'];
$usuarioSeleccionadoID = (isset($_GET['usuario']) && in_array($_SESSION['usuario']['rol'], $rolesPermitidos))
  ? (int) $_GET['usuario']
  : $_SESSION['usuario']['id'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>USCSS Paladio - Informes</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Fuentes -->
  <link href="https://fonts.googleapis.com/css2?family=Courier+New&display=swap" rel="stylesheet">
  <!-- Iconos -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="icon" href="../img/logoIcono.png" type="image/x-icon">
  <!-- CSS propio -->
  <link rel="stylesheet" href="../styles/styles.css">
</head>

<body id="bodyInicio">
  <!-- Contenedor principal -->
  <div class="container my-4 col-8 text-center mt-5" id="contenedorPrincipal">
    <!-- LOGO -->
    <div class="row">
      <div class="col-12 d-flex justify-content-center align-items-center">
        <img src="../img/logoTransparente.png" alt="Cargando imágen..." class="img-fluid" id="logoArchivos">
      </div>
    </div>
    <!-- Título -->
    <div class="col-10">
      <h2 class="text-center text-warning">Gestor de informes</h2>
      <p>Aquí puedes consultar todos los informes almacenados en la base de datos y gestionarlos.
        <br>
        <strong>Clickea sobre el titulo del informe</strong> para ver su contenido.
      </p>
    </div>

    <!-- Mensajes de éxito/error -->
    <div id="mensajes">
      <?php
      if (isset($_SESSION['mensaje_exito'])) {
        echo '<div class="alert alert-success mensaje-exito" role="alert">' . htmlspecialchars($_SESSION['mensaje_exito']) . '</div>';
        unset($_SESSION['mensaje_exito']);
      }
      if (isset($_SESSION['mensaje_error'])) {
        echo '<div class="alert alert-danger mensaje-error" role="alert">' . htmlspecialchars($_SESSION['mensaje_error']) . '</div>';
        unset($_SESSION['mensaje_error']);
      }
      ?>
    </div>

    <!-- Título de usuario -->
    <h6 id="tituloUsuario"></h6>
    <!-- Lista de informes -->
    <div class="col-8 mt-2" id="contenedorInformes">
      <p class="text-center" id="sinInformesMensaje">Cargando informes...</p>
      <ul class="list-group d-none listadoInformes" id="listaInformes"></ul>
    </div>
    <!-- Botón para escribir un nuevo informe si es el usuario propio -->
    <div id="btnNuevoInformeDiv" class="mt-2"></div>
  </div>

  <!-- Modal Archivar -->
  <div class="modal fade" id="modalArchivar" tabindex="-1" aria-labelledby="modalArchivarLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content modal-custom">
        <!-- Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title" id="modalArchivarLabel">Confirmar Archivar</h6>
        </div>
        <!-- Modal Body -->
        <div class="modal-body modal-body-custom">
          ¿Estás seguro de que quieres archivar o reabrir este informe?
        </div>
        <!-- Modal Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btnPersonalizado" data-bs-dismiss="modal">Cancelar</button>
          <a id="btnConfirmArchivar" href="#" class="btn btnPersonalizado">Ok</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Borrar -->
  <div class="modal fade" id="modalBorrar" tabindex="-1" aria-labelledby="modalBorrarLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content modal-custom">
        <!-- Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title" id="modalBorrarLabel">Confirmar Borrar</h6>
        </div>
        <!-- Modal Body -->
        <div class="modal-body modal-body-custom">
          ¿Estás seguro de que quieres borrar este informe?
        </div>
        <!-- Modal Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btnPersonalizado" data-bs-dismiss="modal">Cancelar</button>
          <a id="btnConfirmBorrar" href="#" class="btn btnPersonalizado">Borrar</a>
        </div>
      </div>
    </div>
  </div>

  <!-- BOTONES de navegación -->
  <div class="text-center">
    <button class="btn btn-outline-warning my-2 btn-lg w-25 btnPersonalizado" id="btnVolverMenu"
      onclick="window.location.href='index.php';">VOLVER AL MENÚ</button>
    <button class="btn btn-outline-warning btn-lg w-25 btnPersonalizado" id="btnVolver"
      onclick="window.location.href='gestionInformes.php';">Volver</button>
  </div>

  <!-- INCLUDES (video, footer, música, sonido) -->
  <?php include '../includes/videoFondo.php'; ?>
  <?php include '../includes/footer.html'; ?>
  <?php include '../includes/musica.php'; ?>
  <?php include '../includes/sonidoBotones.php'; ?>

  <!-- Scripts de Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Script para consumir la API -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Obtenemos el ID del usuario seleccionado (inyectado desde PHP)
      const usuarioSeleccionadoID = <?php echo $usuarioSeleccionadoID; ?>;
      // URL del endpoint getInformes; asegúrate de que la ruta sea correcta
      const apiUrl = `../API/informesAPI.php?accion=getInformes&usuario=${usuarioSeleccionadoID}`;

      // Elementos del DOM para actualizar
      const tituloUsuario = document.getElementById('tituloUsuario');
      const listaInformes = document.getElementById('listaInformes');
      const sinInformesMensaje = document.getElementById('sinInformesMensaje');
      const btnNuevoInformeDiv = document.getElementById('btnNuevoInformeDiv');

      // Llamada a la API
      fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            sinInformesMensaje.textContent = data.error;
            return;
          }
          // Título con nombre de usuario
          tituloUsuario.textContent = `Informes de ${data.usuario_nombre}`;

          // Si existen informes
          if (data.informes && data.informes.length > 0) {
            listaInformes.classList.remove('d-none');
            listaInformes.innerHTML = '';
            data.informes.forEach(informe => {

              // Para cada informe se crea un item
              const li = document.createElement('li');
              li.className = 'list-group-item items-inf'; // clases básicas

              // Enlace clickeable para ver el informe
              const a = document.createElement('a');
              a.href = `read.php?informe=${encodeURIComponent(informe.inf_id)}`;
              a.className = 'informe-link'; // solo una clase, el estilo lo manejas por CSS

              a.innerHTML =
                `
                <div class="info-texto text-start">
                  <h6>${informe.inf_concepto}</h6>
                  <strong>${informe.inf_fecha}</strong>
                </div>
                <span class="estado-informe">${informe.inf_estado}</span>
              `;

              li.appendChild(a);


              // Contenedor para los iconos de Archivar y Borrar
              const divIcons = document.createElement('div');
              divIcons.className = 'd-flex gap-2';

              // Botón Archivar
              const btnArchivar = document.createElement('a');
              btnArchivar.href = '#';
              btnArchivar.className = 'icono';
              btnArchivar.title = 'Archivar';
              btnArchivar.setAttribute('data-bs-toggle', 'modal');
              btnArchivar.setAttribute('data-bs-target', '#modalArchivar');
              btnArchivar.innerHTML = '<i class="fa fa-archive"></i>';
              // Botón Archivar
              btnArchivar.addEventListener('click', (e) => {
                e.preventDefault();
                currentInfId = informe.inf_id;
              });
              divIcons.appendChild(btnArchivar);

              // Botón Borrar
              const btnBorrar = document.createElement('a');
              btnBorrar.href = '#';
              btnBorrar.className = 'icono';
              btnBorrar.title = 'Borrar';
              btnBorrar.setAttribute('data-bs-toggle', 'modal');
              btnBorrar.setAttribute('data-bs-target', '#modalBorrar');
              btnBorrar.innerHTML = '<i class="fa fa-trash"></i>';
              // Botón Borrar
              btnBorrar.addEventListener('click', (e) => {
                e.preventDefault();
                currentInfId = informe.inf_id;
              });
              divIcons.appendChild(btnBorrar);

              li.appendChild(divIcons);

              listaInformes.appendChild(li);
            });
            sinInformesMensaje.style.display = 'none';
          } else {
            sinInformesMensaje.textContent = 'No hay informes para este usuario.';
          }

          // Si el usuario está viendo sus propios informes, habilitamos el botón para crear nuevos
          if (data.esPropio) {
            btnNuevoInformeDiv.innerHTML = `<a href="write.php" class="btn btn-outline-warning mb-3 mt-3 btn-lg w-90 btnPersonalizado" id="btnNuevoInforme">GENERAR UN NUEVO INFORME</a>`;
          }
        })
        .catch(error => {
          console.error('Error al obtener los informes:', error);
          sinInformesMensaje.textContent = 'Error al cargar los informes.';
        });

      // Confirmar Archivar
      document.getElementById('btnConfirmArchivar').addEventListener('click', async (e) => {
        e.preventDefault();
        if (!currentInfId) return;

        const formData = new FormData();
        formData.append('inf_id', currentInfId);

        try {
          const response = await fetch(`../API/informesAPI.php?accion=archivarInforme`, {
            method: 'POST',
            body: formData,
            credentials: 'include'
          });
          const data = await response.json();

          if (data.success) {
            location.reload();
          } else {
            alert('Error: ' + (data.error || 'No se pudo archivar'));
          }
        } catch (error) {
          console.error('Error:', error);
          alert('Error de conexión');
        }

        bootstrap.Modal.getInstance(document.getElementById('modalArchivar')).hide();
      });

      // Confirmar Eliminar
      document.getElementById('btnConfirmBorrar').addEventListener('click', async (e) => {
        e.preventDefault();
        if (!currentInfId) return;

        const formData = new FormData();
        formData.append('inf_id', currentInfId);

        try {
          const response = await fetch(`../API/informesAPI.php?accion=eliminarInforme`, {
            method: 'POST',
            body: formData,
            credentials: 'include'
          });
          const data = await response.json();

          if (data.success) {
            location.reload();
          } else {
            alert('Error: ' + (data.error || 'No se pudo eliminar'));
          }
        } catch (error) {
          console.error('Error:', error);
          alert('Error de conexión');
        }

        bootstrap.Modal.getInstance(document.getElementById('modalBorrar')).hide();
      });
    });
  </script>

  <script>
    // Eliminar mensajes de éxito/error después de 3 segundos
    setTimeout(() => {
      const mensajeExito = document.querySelector('.mensaje-exito');
      const mensajeError = document.querySelector('.mensaje-error');
      if (mensajeExito) {
        mensajeExito.remove();
      }
      if (mensajeError) {
        mensajeError.remove();
      }
    }, 3000);
  </script>
</body>

</html>