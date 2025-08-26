<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Error</title>

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
            <div class="col-12 d-flex justify-content-center align-items-center">
                <img src="../img/logoTransparente.png" alt="Cargando imagen..." class="img-fluid" id="logoArchivos">
            </div>
        </div>
        <header>
            <h2>GESTOR DE ERRORES</h2>
        </header>

        <section class="error-message">
            <?php
            $errores = [
                'sin_permiso' => [
                    'contenido' => [
                        '<br>',
                        '<br>',
                        '<h6>Acceso Denegado</h6> ',
                        '<br>',
                        '<p>Tu nivel jerárquico es insuficiente para acceder a esta sección.</p>',
                        '<br>',
                        '<strong>Si crees que se trata de un error, ponte en contacto con un superior.</strong>',
                        '<br>',
                    ],
                    'boton' => [
                        'texto' => 'REGRESAR AL MENÚ',
                        'destino' => 'index.php',
                        'id' => 'btnVolverMenu',
                    ]
                ],

                'credenciales' => [
                    'contenido' => [
                        '<br>',
                        '<br>',
                        '<h6>Error de Credenciales</h6>',
                        '<br>',
                        '<p>El sistema no ha podido verificar tus credenciales. Puede que el usuario esté inactivo.</p>',
                        '<p>Por favor, regresa e inténtalo de nuevo.</p>',
                        '<br>',
                        '<strong>Si el problema persiste, contacta con un superior.</strong>',
                        '<br>',
                    ],
                    'boton' => [
                        'texto' => 'REGRESAR AL INICIO',
                        'destino' => 'inicio.php',
                        'id' => 'btnVolverInicio1',
                    ]
                ],

                'desconocido' => [
                    'contenido' => [
                        '<br>',
                        '<br>',
                        '<h6>Error desconocido</h6>',
                        '<br>',
                        '<p>El sistema no es capaz de identificar el error.</p>',
                        '<p>Por favor, regresa e inténtalo de nuevo.</p>',
                        '<br>',
                        '<strong>Si el problema persiste, contacta con un superior.</strong>',
                        '<br>',
                    ],
                    'boton' => [
                        'texto' => 'REGRESAR AL INICIO',
                        'destino' => 'inicio.php',
                        'id' => 'btnVolverInicio2',
                    ]
                ]
            ];

            // Error desde la URL
            $error = isset($_GET['error']) ? $_GET['error'] : 'desconocido';

            // Mostrar contenido segun el error
            if (isset($errores[$error])) {
                foreach ($errores[$error]['contenido'] as $linea) {
                    echo $linea;
                }
                $boton = $errores[$error]['boton'];
                echo "<button class='btn btn-outline-warning my-2 btn-lg w-50 btnPersonalizado' id='{$boton['id']}' onclick=\"window.location.href='{$boton['destino']}';\">{$boton['texto']}</button>";
            } else {
                // Mensaje default
                echo '<p>MU-TH-UR 6000 no es capaz de identificar el error.</p>';
                echo '<button class="btn btn-outline-warning my-2 btn-lg w-50 btnPersonalizado" onclick="window.location.href=\'inicio.php\';">REGRESAR AL INICIO</button>';
            }
            ?>
        </section>
    </div>

    <!-- FOOTER -->
    <?php include '../includes/footer.html'; ?>
    <!-- MUSICA-->
    <?php include '../includes/musica.php'; ?>
    <!-- Video de fondo -->
    <?php include '../includes/videoFondo.php'; ?>
    <!-- Sonido en botones -->
    <?php include '../includes/sonidoBotones.php'; ?>

</body>

</html>