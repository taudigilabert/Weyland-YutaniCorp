<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: inicio.php");
    exit();
}

$rolesPermitidos = ['1', '2']; // 'capitán' y 'primer oficial'
if (!in_array($_SESSION['usuario']['rol'], $rolesPermitidos)) {
    header("Location: error.php?error=sin_permiso");
    exit();
}

// Obtener los datos del usuario actual
$usuarioActualID = $_SESSION['usuario']['id']; // ID del user logueado
$usuarioActualNombre = $_SESSION['usuario']['nombre'] . " " . $_SESSION['usuario']['apellido'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USCSS Paladio - Todos Registros</title>
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

<style>
    body {
        overflow-x: hidden;
    }

    #containerTodosInformes {
        padding-left: 15px;
        padding-right: 15px;
    }

    .row {
        margin-left: 0;
        margin-right: 0;
    }
</style>

<body id="bodyInicio">
    <!-- Contenedor principal -->
    <div class="container my-4 col-8 text-center mt-5" id="contenedorPrincipal">
        <!-- Logo -->
        <div class="row">
            <div class="col-12 d-flex justify-content-center align-items-center">
                <img src="../img/logoTransparente.png" alt="Cargando imagen..." class="img-fluid" id="logoArchivos">
            </div>
        </div>
        <!-- Título -->
        <h2 class="text-warning text-center">Administración de informes</h2>
        <h3 class="text-center">USCSS PALADIO</h3>
        <br>
        <p class="text-center">Bienvenido/a
            <strong> <?php echo htmlspecialchars($usuarioActualNombre); ?></strong>.
            Selecciona un tripulante para acceder a sus registros.
        </p>


        <!-- Tarjetas por cada tripulante -->
        <div class="container my-2">
            <div class="row justify-content-center" id="contenedorTarjetas">
                <!-- Aquí se cargarán las tarjetas de los tripulantes -->
            </div>
        </div>
    </div>

    <!-- Botones de navegación -->
    <div class="row text-center justify-content-center">
        <div class="col-6 col-md-3">
            <button class="btn btn-outline-warning btn-lg w-100 btnPersonalizado" id="btnAllWriteVolverMenu"
                onclick="window.location.href='index.php';">VOLVER AL MENÚ</button>
        </div>
        <div class="col-6 col-md-3">
            <button class="btn btn-outline-warning btn-lg w-100 btnPersonalizado" id="btnVolverMenu"
                onclick="window.location.href='gestionInformes.php';">Volver</button>
        </div>
    </div>

    <!-- INCLUDES -->
    <?php include '../includes/videoFondo.php'; ?>
    <?php include '../includes/footer.html'; ?>
    <?php include '../includes/musica.php'; ?>
    <?php include '../includes/sonidoBotones.php'; ?>

    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const API = '../API/gestionTripulacionAPI.php';

        // Listar tripulantes
        function cargarTripulacion() {
            const contenedor = document.getElementById('contenedorTarjetas');

            if (!contenedor) return;

            fetch(`${API}?accion=getTripulacion`) // Simplificado sin filtros
                .then(r => r.json())
                .then(data => {
                    contenedor.innerHTML = '';

                    data.forEach(u => {
                        const tarjeta = document.createElement('div');
                        tarjeta.className = 'col-12 col-md-6 col-lg-3 my-3';

                        tarjeta.innerHTML = `
                        <div class="card text-center" id="cardInforme">
                            <div class="card-body">
                                <img src="../img/fotoPerfil/${u.usu_imagen}" 
                                    onerror="this.src='../img/fotoPerfil/default.jpg'" 
                                    alt="Avatar ${u.usu_alias}" 
                                    class="imagen-tripulante"
                                    width="150" height="150">
                                <h6 class="card-title">${u.usu_nombre} ${u.usu_apellido}</h6>
                                <strong class="card-text">${u.rol_nombre}</strong>
                                <a href="usuarioRegistros.php?usuario=${u.usu_id}" 
                                   class="btn btn-outline-warning mt-2 btn-ver-informe btnPersonalizado w-100">
                                   Ver informes
                                </a>
                            </div>
                        </div>`;

                        contenedor.appendChild(tarjeta);
                    });
                })
                .catch(error => console.error('Error al cargar tripulantes:', error));
        }

        document.addEventListener('DOMContentLoaded', () => {
            cargarTripulacion();
        });
    </script>

</body>

</html>