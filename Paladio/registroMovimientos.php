<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USCSS Paladio - Sitio en Mantenimiento</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Courier+New&display=swap" rel="stylesheet"><!-- Máquina -->
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="../img/logoIcono.png" type="image/x-icon">

    <!-- CSS -->
    <link rel="stylesheet" href="../styles/styles.css">
</head>

<body id="bodyInicio">
    <!-- Contenedor principal -->
    <div class="container my-4 col-6 text-center mt-5" id="contenedorPrincipal"> <!-- Cambiar col-6 a col-8 si tiene contenido -->
        <!-- LOGO -->
        <div class="row">
            <div class="col-12 d-flex justify-content-center align-items-center">
                <img src="../img/logoTransparente.png" alt="Cargando imágen..." class="img-fluid" id="logoArchivos">
            </div>
        </div>
        <header class="error-header">
            <h2 class="text-warning"> Sitio en Mantenimiento </h2>
        </header>

        <p class="mt-3">
            Los técnicos de <strong>Weyland-Yutani</strong> están trabajando en esta función.
            <br>
            <br>
            Estamos asegurándonos de que todo el sistema de <strong>registro de movimientos</strong> esté completamente operativo y funcional.
            <br>
            <br>
            <strong>Por favor, regrese más tarde.</strong>
        </p>

    </div>
    <div class="d-flex justify-content-center">
        <button class="btn btn-outline-warning btn-lg w-25 mb-2 btnPersonalizado" id="btnVolverMenu"
            onclick="window.location.href='index.php';">VOLVER AL MENÚ</button>
    </div>

    <!-- INCLUDES -->
    <!-- FOOTER -->
    <?php include '../includes/footer.html'; ?>
    <!-- MÚSICA -->
    <?php include '../includes/musica.php'; ?>
    <!-- Sonido en botones -->
    <?php include '../includes/sonidoBotones.php'; ?>
    <!-- Video de fondo -->
    <?php include '../includes/videoFondo.php'; ?>

    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>