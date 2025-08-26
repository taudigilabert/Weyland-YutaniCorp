<?php
session_start();
include('./includes/db.php.');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alias = trim($_POST['usuario']);
    $contrasena = $_POST['contrasena'];

    if (empty($alias) || empty($contrasena)) {
        header("Location: inicio.php");
        exit();
    }

    $sql = "SELECT * FROM usuarios WHERE usu_alias = ? AND usu_activo = 1";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        $_SESSION['errorInicio'] = "Error al preparar la consulta: " . mysqli_error($conn);
        header("Location: inicio.php");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $alias);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result);

        if (password_verify($contrasena, $user_data['usu_contrasena'])) {
            $_SESSION['usuario'] = [
                'id' => $user_data['usu_id'],
                'alias' => $user_data['usu_alias'],
                'rol' => $user_data['rol_id'],
                'nombre' => $user_data['usu_nombre'],
                'apellido' => $user_data['usu_apellido'],
                'descripcion' => $user_data['usu_biografia'],
                'imagen' => $user_data['usu_imagen'],
                'genero' => $user_data['usu_genero'],
                'empleado' => $user_data['usu_numero_empleado'],
                'estado' => $user_data['usu_activo'],
                'idnave' => $user_data['usu_idnave']
            ];

            // Establecer variable de sesión para indicar que el login fue exitoso
            $_SESSION['mostrar_carga'] = true;

            header("Location: index.php");
            exit();
        } else {
            header("Location: error.php?error=credenciales");
            exit();
        }
    } else {
        header("Location: error.php?error=credenciales");
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        exit();
    }
} else {
    header("Location: inicio.php");
    exit();
}
