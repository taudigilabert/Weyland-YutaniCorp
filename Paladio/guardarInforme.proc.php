<?php
session_start();
include('../includes/db.php');
//TODO controlar size y extension imagenes


if (!isset($_SESSION['usuario'])) {
    header("Location: inicio.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inf_id = isset($_POST['inf_id']) ? (int)$_POST['inf_id'] : null;
    $inf_concepto = trim($_POST['inf_concepto']);
    $inf_fecha = $_POST['inf_fecha'];
    $inf_contenido = trim($_POST['inf_contenido']);
    $usu_id = $_SESSION['usuario']['id'];

    // Insertar o actualizar el informe
    if ($inf_id) {
        $sql = "UPDATE informes_usuario 
                SET inf_concepto = ?, inf_fecha = ?, inf_contenido = ?
                WHERE inf_id = ? AND usu_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssii", $inf_concepto, $inf_fecha, $inf_contenido, $inf_id, $usu_id);
        $result = mysqli_stmt_execute($stmt);
    } else {
        $inf_estado = "Abierto";
        $sql = "INSERT INTO informes_usuario (usu_id, inf_concepto, inf_fecha, inf_contenido, inf_estado) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issss", $usu_id, $inf_concepto, $inf_fecha, $inf_contenido, $inf_estado);
        $result = mysqli_stmt_execute($stmt);
        $inf_id = mysqli_insert_id($conn);
    }

    if ($result) {
        // Procesar img subidas
        if (isset($_FILES['inf_imagenes']) && count($_FILES['inf_imagenes']['name']) > 0) {
            $carpetaImagenes = "../img/img-subidas/informes/";
            if (!file_exists($carpetaImagenes)) {
                mkdir($carpetaImagenes, 0777, true);
            }

            foreach ($_FILES['inf_imagenes']['tmp_name'] as $key => $tmp_name) {

                //TODO cambiar por uniqID
                $nombreImagen = basename($_FILES['inf_imagenes']['name'][$key]);
                $rutaImagen = $carpetaImagenes . $nombreImagen;

                if (move_uploaded_file($tmp_name, $rutaImagen)) {
                    // Guardar la ruta de la imagen en la bd
                    $sqlImg = "INSERT INTO informe_imagenes (inf_id, img_ruta) VALUES (?, ?)";
                    $stmtImg = mysqli_prepare($conn, $sqlImg);
                    mysqli_stmt_bind_param($stmtImg, "is", $inf_id, $nombreImagen);
                    mysqli_stmt_execute($stmtImg);
                    mysqli_stmt_close($stmtImg);
                }
            }
        }

        // Obtener el usu_id del dueño del informe si es una actualizacion
        if ($inf_id) {
            $sql = "SELECT usu_id FROM informes_usuario WHERE inf_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $inf_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $usu_id);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }

        $_SESSION['mensaje_exito'] = "Informe guardado correctamente";
        header("Location: usuarioRegistros.php?usuario=" . $usu_id);
        exit();

    } else {
        $_SESSION['mensaje_error'] = "Error al guardar el informe: " . mysqli_error($conn);
        header("Location: usuarioRegistros.php?usuario=" . $usu_id);
        exit();  
    }
}
?>