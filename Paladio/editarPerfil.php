<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: inicio.php");
    exit();
}

$usuario_id = $_SESSION['usuario']['id'];
$alias_actual = $_SESSION['usuario']['alias'];
$biografia_actual = $_SESSION['usuario']['descripcion'];
$imagen_actual = $_SESSION['usuario']['imagen'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Actualizar biografía
    if (!empty($_POST['biografia'])) {
        $nueva_biografia = $_POST['biografia'];
    } else {
        $nueva_biografia = $biografia_actual;  // Mantener la biografía anterior si no se envió una nueva
    }

    // Actualizar alias si se pasa
    if (!empty($_POST['alias']) && $_POST['alias'] != $alias_actual) {
        $nuevo_alias = $_POST['alias'];

        // Verificar que el alias no esté en uso
        $sql = "SELECT COUNT(*) FROM usuarios WHERE usu_alias = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $nuevo_alias);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $alias_count);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Verificar que el alias este libre
        if ($alias_count > 0) {
            $_SESSION['error_perfil'] = "El alias ya está en uso. Elige otro.";
            header("Location: editarPerfil.php");
            exit();
        } else {
            $sql = "UPDATE usuarios SET usu_alias = ? WHERE usu_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $nuevo_alias, $usuario_id);
            mysqli_stmt_execute($stmt);
        }
    } else {
        $nuevo_alias = $alias_actual;
    }

    // Subir nueva img de perfil
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen = $_FILES['imagen'];

        $extensiones_validas = ['jpg', 'jpeg', 'png'];
        $tamaño_maximo = 2 * 1024 * 1024;

        $ext = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));
        $tamaño = $imagen['size'];

        if (!in_array($ext, $extensiones_validas)) {
            $_SESSION['error_perfil'] = "La imagen debe ser de tipo JPG, JPEG, PNG.";
            header("Location: editarperfil.php");
            exit();
        } elseif ($tamaño > $tamaño_maximo) {
            $_SESSION['error_perfil'] = "La imagen no debe exceder los 5MB.";
            header("Location: editarPerfil.php");
            exit();
        } else {
            // Si existe una img distinta a 'userDefault.jpg', elimina
            if ($imagen_actual !== 'userDefault.jpg' && file_exists('../img/fotoPerfil/' . $imagen_actual)) {
                unlink('../img/fotoPerfil/' . $imagen_actual);
            }

            $nuevo_nombre_imagen = 'fotoPerfil' . uniqid() . '.' . $ext;
            $ruta_imagen = '../img/fotoPerfil/' . $nuevo_nombre_imagen;

            move_uploaded_file($imagen['tmp_name'], $ruta_imagen);

            $sql = "UPDATE usuarios SET usu_imagen = ? WHERE usu_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $nuevo_nombre_imagen, $usuario_id);
            mysqli_stmt_execute($stmt);

            $_SESSION['usuario']['imagen'] = $nuevo_nombre_imagen;
        }
    }

    // Actualizar bio
    if (!isset($error)) {
        $sql = "UPDATE usuarios SET usu_biografia = ? WHERE usu_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $nueva_biografia, $usuario_id);
        mysqli_stmt_execute($stmt);


        $_SESSION['usuario']['descripcion'] = $nueva_biografia;

        if ($nuevo_alias != $alias_actual) {
            $_SESSION['usuario']['alias'] = $nuevo_alias;
        }

        header("Location: perfil.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/styles.css"> <!-- Agrega tu archivo CSS si lo necesitas -->
    <link rel="icon" href="../img/logoIcono.png" type="image/x-icon">

</head>

<body id="bodyInicio">
    <!-- Contenedor principal -->
    <div class="container my-4 col-8 text-center mt-5" id="contenedorPrincipal">
        <div class="container-fluid text-center">
            <div class="row justify-content-center">
                <div class="col-10">
                    <!-- Contenedor personalizado para logo y título -->
                    <div class="row justify-content-center mb-4">
                        <div class="col-12">
                            <!-- Logo -->
                            <div class="d-flex justify-content-center align-items-center mb-3">
                                <img src="../img/logoTransparente.png" alt="Cargando imágen..." class="img-fluid" id="logoArchivos">
                            </div>

                            <!-- Título de la sección -->
                            <h2 class="text-warning">Editar tu perfil</h2>
                            <p class="text-white">Bienvenido, <strong><?php echo $_SESSION['usuario']['nombre'] . " " . $_SESSION['usuario']['apellido']; ?></strong>. Aquí puedes actualizar tu biografía, alias e imagen de perfil.</p>
                        </div>
                    </div>
                    <?php
                    if (isset($_SESSION['error_perfil'])) {
                        echo '<div class="alert alert-danger text-center" role="alert">'
                            . htmlspecialchars($_SESSION['error_perfil']) .
                            '</div>';
                        unset($_SESSION['error_perfil']);
                    }
                    ?>

                    <div class="text-start">
                        <!-- Mostrar la foto de perfil actual -->
                        <div class="text-center mb-3">
                            <img src="../img/fotoPerfil/<?php echo $imagen_actual; ?>" alt="Imagen de perfil" class="img-fluid" style="max-width: 200px;" id="fotoPerfil">
                        </div>

                        <form action="editarPerfil.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="biografia" class="form-label"><strong>Biografía</strong></label>
                                <textarea id="biografia" name="biografia" class="form-control custom-input" rows="3" placeholder="Escribe aquí tu nueva biografía..."><?php echo htmlspecialchars($biografia_actual); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="alias" class="form-label"><strong>Nuevo Alias</strong></label>
                                <input type="text" id="alias" name="alias" class="form-control custom-input" value="<?php echo htmlspecialchars($alias_actual); ?>" placeholder="Escribe tu nuevo alias">
                            </div>

                            <div class="mb-3">
                                <label for="imagen" class="form-label"><strong>Nueva Imagen de Perfil</strong></label>
                                <input type="file" id="imagen" name="imagen" class="form-control custom-input">
                            </div>
                    </div>
                    <button type="submit" class="btn btn-outline-warning btn-lg btnPersonalizado my-4 w-50" id="btnGuardarCambios">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de acción al final -->
    <div class="text-center">
        <button class="btn btn-outline-warning my-2 btn-lg w-25 btnPersonalizado" id="btnVolverMenu" onclick="window.location.href='index.php';">VOLVER AL MENÚ</button>
        <button class="btn btn-outline-warning my-2 btn-lg w-25 btnPersonalizado" id="btnVerPerfil" onclick="window.location.href='perfil.php';">Ver Perfil</button>
    </div>


    <?php include '../includes/videoFondo.php'; ?>
    <?php include '../includes/footer.html'; ?>
    <?php include '../includes/musica.php'; ?>
    <?php include '../includes/sonidoBotones.php'; ?>
    <script src="../includes/sonidoTeclas.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>