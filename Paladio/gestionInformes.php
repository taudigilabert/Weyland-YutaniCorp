<?php

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USCSS Paladio - Gestor de Informes</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Courier+New&display=swap" rel="stylesheet">
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="../img/logoIcono.png" type="image/x-icon">

    <!-- CSS -->
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
        body {
            overflow-x: hidden;
            /* Evita scroll horizontal */
        }
    </style>
</head>

<body id="bodyInicio">

    <!-- Contenedor principal -->
    <div class="container my-4 col-8 text-center mt-5" id="contenedorPrincipal">
        <!-- LOGO -->
        <div class="row">
            <div class="col-12 d-flex justify-content-center align-items-center">
                <img src="../img/logoTransparente.png" alt="Cargando imagen..." class="img-fluid" id="logoArchivos">
            </div>
        </div>
        <!-- Título -->
        <h2 class="text-warning text-center mt-4">GESTOR DE INFORMES</h2>
        <p class="text-center mt-2">Selecciona una acción relacionada con los informes</p>

        <!-- Botones del gestor -->
        <div class="row justify-content-center my-4">
            <div class="col-12 my-2">
                <button class="btn btn-outline-warning btn-lg w-100 btnPersonalizado" id="btnNuevoInforme"
                    onclick="window.location.href='../Paladio/write.php';">Nuevo informe</button>
            </div>
            <div class="col-12 my-2">
                <button class="btn btn-outline-warning btn-lg w-100 btnPersonalizado" id="btnMisInformes"
                    onclick="window.location.href='../Paladio/usuarioRegistros.php';">Mis informes</button>
            </div>
            <div class="col-12 my-2">
                <button class="btn btn-outline-warning btn-lg w-100 btnPersonalizado" id="btnTodosInformes"
                    onclick="window.location.href='../Paladio/allWrite.php';">Consultar todos los informes</button>
            </div>
        </div>
    </div>

    <!-- BOTONES -->
    <div class="row justify-content-center text-center">
        <div class="col-10 col-md-6 my-2">
            <button class="btn btn-outline-warning btn-lg w-50 btnPersonalizado" id="btnVolverMenu"
                onclick="window.location.href='index.php';">VOLVER AL MENÚ</button>
        </div>
    </div>

    <!-- INCLUDES -->
    <!-- Video de fondo -->
    <?php include '../includes/videoFondo.php'; ?>
    <!-- FOOTER -->
    <?php include '../includes/footer.html'; ?>
    <!-- MÚSICA -->
    <?php include '../includes/musica.php'; ?>
    <!-- Sonido en botones -->
    <?php include '../includes/sonidoBotones.php'; ?>

    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>