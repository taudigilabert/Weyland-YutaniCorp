<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// HABILITAR CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

session_start();

// CONEXIÓN A LA BASE DE DATOS
include_once '../includes/db.php';

// Acción a realizar
$accion = $_POST['accion'] ?? $_GET['accion'] ?? $_PUT['accion'] ?? $_DELETE['accion'] ?? null;

// Menú con acciones
switch ($accion) {
    case 'getPerfil':
        getPerfil($conn);
        break;
    case 'getUsuarios':
        getAllUsuarios($conn);
        break;
    case 'getUsuario':
        getUsuario($conn);
        break;
    case 'getRoles':
        getRoles($conn);
        break;
    case 'getNaves':
        getNaves($conn);
        break;
    case 'actualizarPerfil':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') putActualizarPerfil($conn);
        else echo json_encode(["mensaje" => "Método no permitido."]);
        break;
    default:
        echo json_encode(["mensaje" => "El sistema no reconoce la acción solicitada"]);
        break;
}

// =================== FUNCIONES ===================
function getPerfil($conn)
{
    $id = $_SESSION['usuario']['id'] ?? null;
    if (!$id) {
        echo json_encode(["mensaje" => "ID de usuario inválido."]);
        return;
    }

    $stmt = mysqli_prepare($conn, "
        SELECT u.usu_id, u.usu_nombre, u.usu_apellido, u.usu_alias, u.usu_genero, u.usu_biografia, u.usu_imagen, 
               u.usu_numero_empleado, u.usu_fecha_creacion, u.usu_contrasena, u.usu_activo, 
               r.rol_id, r.rol_nombre, r.rol_descripcion, 
               n.nav_nombre, n.nav_tipo, n.nav_descripcion
        FROM usuarios u
        LEFT JOIN roles r ON u.rol_id = r.rol_id
        LEFT JOIN nave n ON u.usu_idnave = n.nav_id
        WHERE u.usu_id = ?
    ");

    if (!$stmt) {
        echo json_encode(["mensaje" => "Error al preparar la consulta."]);
        return;
    }

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);

    if ($usuario = mysqli_fetch_assoc($resultado)) {
        $usuario['usu_nombreCompleto'] = $usuario['usu_nombre'] . ' ' . $usuario['usu_apellido'];
        echo json_encode($usuario);
    } else {
        echo json_encode(["mensaje" => "Usuario no encontrado."]);
    }

    mysqli_stmt_close($stmt);
}


// GET: Listar todos los usuarios
function getAllUsuarios($conn)
{
    $sql = "SELECT * FROM usuarios";
    $resultado = mysqli_query($conn, $sql);
    $usuarios = [];

    while ($row = mysqli_fetch_assoc($resultado)) {
        $usuarios[] = $row;
    }

    echo json_encode($usuarios);
}

// GET: Obtener usuario por ID
function getUsuario($conn)
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    if (!$id) {
        echo json_encode(["mensaje" => "ID inválido."]);
        return;
    }

    $stmt = mysqli_prepare($conn, "SELECT * FROM usuarios WHERE usu_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $usuario = mysqli_fetch_assoc($resultado);
    echo json_encode($usuario ?: []);
}

// GET: Listar todos los roles
function getRoles($conn)
{
    $sql = "SELECT * FROM roles";
    $resultado = mysqli_query($conn, $sql);
    $roles = [];

    while ($row = mysqli_fetch_assoc($resultado)) {
        $roles[] = $row;
    }

    echo json_encode($roles);
}

// GET: Listar todas las naves
function getNaves($conn)
{
    $sql = "SELECT * FROM nave";
    $resultado = mysqli_query($conn, $sql);
    $naves = [];

    while ($row = mysqli_fetch_assoc($resultado)) {
        $naves[] = $row;
    }

    echo json_encode($naves);
}

// PUT: Actualizar perfil
function putActualizarPerfil($conn)
{
    $id = $_POST['usu_id'] ?? null;

    if (!$id) {
        echo json_encode(["mensaje" => "ID de usuario faltante."]);
        return;
    }

    $nombre = $_POST['nombre'] ?? '';
    $alias = $_POST['alias'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $rol_id = $_POST['rol_id'] ?? 0;
    $genero = $_POST['genero'] ?? '';
    $biografia = $_POST['biografia'] ?? '';
    $imagen = $_FILES['imagen'] ?? null;

    if (empty($nombre) || empty($apellido) || empty($alias)) {
        echo json_encode(["mensaje" => "Nombre, apellido y alias son obligatorios."]);
        return;
    }

    // Subida de imagen
    if ($imagen && $imagen['error'] === 0) {
        $extensiones_validas = ['jpg', 'jpeg', 'png'];
        $tamaño_maximo = 2 * 1024 * 1024;
        $ext = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));
        $tamaño = $imagen['size'];

        if (!in_array($ext, $extensiones_validas)) {
            echo json_encode(["mensaje" => "La imagen debe ser de tipo JPG, JPEG o PNG."]);
            return;
        } elseif ($tamaño > $tamaño_maximo) {
            echo json_encode(["mensaje" => "La imagen no debe exceder los 2MB."]);
            return;
        } else {
            // Obtener imagen anterior
            $sql = "SELECT usu_imagen FROM usuarios WHERE usu_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $imagen_anterior);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            // Eliminar imagen anterior
            if ($imagen_anterior && file_exists('../img/fotoPerfil/' . $imagen_anterior)) {
                unlink('../img/fotoPerfil/' . $imagen_anterior);
            }

            // Guardar nueva imagen
            $nuevo_nombre_imagen = 'fotoPerfil_' . uniqid() . '.' . $ext;
            $ruta_imagen = '../img/fotoPerfil/' . $nuevo_nombre_imagen;

            if (move_uploaded_file($imagen['tmp_name'], $ruta_imagen)) {
                $sql = "UPDATE usuarios SET usu_imagen = ? WHERE usu_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "si", $nuevo_nombre_imagen, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                echo json_encode(["mensaje" => "Error al subir la imagen."]);
                return;
            }
        }
    }

    // Actualizar solo los campos editables
    $sql = "UPDATE usuarios SET 
                usu_nombre = ?,
                usu_alias = ?, 
                usu_apellido = ?, 
                usu_genero = ?, 
                usu_biografia = ?
            WHERE usu_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $nombre, $alias, $apellido, $genero, $biografia, $id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo json_encode(["mensaje" => "Perfil actualizado con éxito."]);
    } else {
        echo json_encode(["mensaje" => "No se realizaron cambios."]);
    }

    mysqli_stmt_close($stmt);
    exit;
}
