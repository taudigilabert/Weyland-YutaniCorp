<?php
// Establecer el tipo de contenido a JSON
header('Content-Type: application/json');

// HABILITAR CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// CONEXIÓN A LA BASE DE DATOS
include_once '../includes/db.php';

// Acción a realizar
$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

// Menú con acciones
switch ($accion) {
    //GET
    case 'getUsuarios':
        getAllUsuarios($conn);
        break;
    case 'getInformes':
        getInformes($conn);
        break;
    case 'getInforme':
        getInforme($conn);
        break;
    //POST
    case 'postCrearInforme':
        postCrearInforme($conn);
        break;
    case 'postActualizarInforme':
        postActualizarInforme($conn);
        break;
    case 'postActualizarEstadoInforme':
        postActualizarEstadoInforme($conn);
        break;
    case 'postSubirImagen':
        postSubirImagen($conn);
        break;
    //DELETE
    case 'deleteInforme':
        deleteInforme($conn);
        break;
    case 'deleteImagen':
        deleteImagen($conn);
        break;
    default:
        echo json_encode(array("mensaje" => "El sistema no reconoce la acción solicitada"));
        break;
}

// =================== FUNCIONES ===================

// GET: Listar todos los usuarios
function getAllUsuarios($conn)
{
    $sql = "SELECT * FROM usuarios";
    $resultado = mysqli_query($conn, $sql);
    $usuarios = array();

    while ($row = mysqli_fetch_assoc($resultado)) {
        $usuarios[] = $row;
    }

    echo json_encode($usuarios);
}

// GET: Obtener informes
function getInformes($conn)
{
    $sql = "SELECT * FROM informes";
    $resultado = mysqli_query($conn, $sql);
    $informes = array();

    while ($row = mysqli_fetch_assoc($resultado)) {
        $informes[] = $row;
    }

    echo json_encode($informes);
}

// GET: Obtener informe por ID con imágenes incluidas
function getInforme($conn)
{
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($id === false) {
        echo json_encode(array("mensaje" => "ID inválido."));
        return;
    }

    // Obtener el informe
    $stmt = mysqli_prepare($conn, "SELECT * FROM informes_usuario WHERE inf_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $informe = mysqli_fetch_assoc($resultado);

    if ($informe) {
        // Recuperar datos del usuario
        $usuarioStmt = mysqli_prepare($conn, "SELECT usu_id, usu_nombre, usu_apellido FROM usuarios WHERE usu_id = ?");
        mysqli_stmt_bind_param($usuarioStmt, "i", $informe['usu_id']);
        mysqli_stmt_execute($usuarioStmt);
        $usuarioResult = mysqli_stmt_get_result($usuarioStmt);
        $usuario = mysqli_fetch_assoc($usuarioResult);

        // Obtener imágenes relacionadas con el informe
        $imagenesStmt = mysqli_prepare($conn, "SELECT img_id, img_ruta FROM informe_imagenes WHERE inf_id = ?");
        mysqli_stmt_bind_param($imagenesStmt, "i", $id);
        mysqli_stmt_execute($imagenesStmt);
        $imagenesResult = mysqli_stmt_get_result($imagenesStmt);

        $imagenes = [];
        while ($row = mysqli_fetch_assoc($imagenesResult)) {
            $imagenes[] = [
                "img_id" => $row["img_id"],
                "ruta" => $row["img_ruta"]
            ];
        }

        // Responder con los datos
        echo json_encode([
            'informe' => $informe,
            'usuario' => $usuario,
            'imagenes' => $imagenes
        ]);
    } else {
        echo json_encode(array("mensaje" => "Informe no encontrado."));
    }
}


// POST: Crear informe
function postCrearInforme($conn)
{
    $usuario_id = $_POST['usuario_id'] ?? null;
    $fecha = $_POST['fecha'] ?? null;
    $contenido = $_POST['contenido'] ?? null;

    $stmt = mysqli_prepare($conn, "INSERT INTO informes (usuario_id, fecha, contenido) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iss", $usuario_id, $fecha, $contenido);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(array("mensaje" => "Informe creado."));
    } else {
        echo json_encode(array("mensaje" => "Error al crear el informe: " . mysqli_error($conn)));
    }
}

// POST: Actualizar informe
function postActualizarInforme($conn)
{
    header('Content-Type: application/json'); // Asegúrate de que la respuesta sea JSON

    // Verifica que todos los datos necesarios estén presentes
    if (isset($_POST['id']) && isset($_POST['usuario_id']) && isset($_POST['fecha']) && isset($_POST['contenido'])) {
        $id = $_POST['id'];
        $usuario_id = $_POST['usuario_id'];
        $fecha = $_POST['fecha'];
        $contenido = $_POST['contenido'];

        // Asegúrate de que la conexión con la base de datos sea correcta
        if ($stmt = mysqli_prepare($conn, "UPDATE informes_usuario SET usu_id = ?, inf_fecha = ?, inf_contenido = ? WHERE inf_id = ?")) {
            mysqli_stmt_bind_param($stmt, "issi", $usuario_id, $fecha, $contenido, $id);

            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(array("mensaje" => "Informe actualizado con éxito."));
            } else {
                echo json_encode(array("mensaje" => "Error al actualizar el informe: " . mysqli_error($conn)));
            }
        } else {
            echo json_encode(array("mensaje" => "Error en la preparación de la consulta SQL"));
        }
    } else {
        echo json_encode(array("mensaje" => "Datos incompletos"));
    }
}



// POST: Actualizar estado
function postActualizarEstadoInforme($conn)
{
    $id = $_POST['id'] ?? null;
    $estado = $_POST['estado'] ?? null;

    $stmt = mysqli_prepare($conn, "UPDATE informes SET estado = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $estado, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(array("mensaje" => "Estado del informe actualizado con éxito."));
    } else {
        echo json_encode(array("mensaje" => "Error al actualizar el estado: " . mysqli_error($conn)));
    }
}

// POST: Subir imagen
function postSubirImagen($conn)
{
    if (empty($_POST['inf_id']) || empty($_FILES['imagen'])) {
        echo json_encode(["mensaje" => "Datos incompletos."]);
        return;
    }

    $inf_id = intval($_POST['inf_id']);
    $imagen = $_FILES['imagen'];

    // Validar imagen (extensión, tamaño)
    $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = pathinfo($imagen["name"], PATHINFO_EXTENSION);

    if (!in_array($ext, $valid_extensions)) {
        echo json_encode(["mensaje" => "Formato de imagen no permitido."]);
        return;
    }

    $directorio = "uploads/";
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $nombreArchivo = "informe_" . $inf_id . "_" . time() . "." . $ext;
    $rutaFinal = $directorio . $nombreArchivo;

    if (move_uploaded_file($imagen["tmp_name"], $rutaFinal)) {
        $stmt = $conn->prepare("INSERT INTO informe_imagenes (inf_id, img_ruta) VALUES (?, ?)");
        $stmt->bind_param("is", $inf_id, $rutaFinal);
        $stmt->execute();

        echo json_encode(["mensaje" => "Imagen subida con éxito", "ruta" => $rutaFinal]);
    } else {
        echo json_encode(["mensaje" => "Error al subir la imagen."]);
    }
}

// DELETE: Eliminar informe
function deleteInforme($conn)
{
    $id = $_POST['id'] ?? null;

    $stmt = mysqli_prepare($conn, "DELETE FROM informes WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(array("mensaje" => "Informe eliminado."));
    } else {
        echo json_encode(array("mensaje" => "Error al eliminar el informe: " . mysqli_error($conn)));
    }
}

// DELETE: Eliminar imagen
function deleteImagen($conn)
{
    // Obtener el id de la imagen desde los parámetros de la solicitud (por ejemplo, via GET o POST)
    $img_id = isset($_GET['img_id']) ? intval($_GET['img_id']) : 0;

    // Verificar que se pasó un ID válido
    if ($img_id <= 0) {
        echo json_encode(["mensaje" => "ID de imagen inválido."]);
        return;
    }

    // Obtener la ruta de la imagen desde la base de datos
    $stmt = $conn->prepare("SELECT img_ruta FROM informe_imagenes WHERE img_id = ?");
    $stmt->bind_param("i", $img_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Verificar si la imagen existe
    if ($fila = $resultado->fetch_assoc()) {
        $ruta = $fila['img_ruta'];

        if (file_exists($ruta)) {
            if (unlink($ruta)) {
                echo json_encode(["mensaje" => "Imagen eliminada correctamente."]);
            } else {
                echo json_encode(["mensaje" => "No se pudo eliminar el archivo de imagen."]);
                return;
            }
        } else {
            echo json_encode(["mensaje" => "Archivo de imagen no encontrado."]);
            return;
        }

        $stmt = $conn->prepare("DELETE FROM informe_imagenes WHERE img_id = ?");
        $stmt->bind_param("i", $img_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(["mensaje" => "Imagen eliminada de la base de datos."]);
        } else {
            echo json_encode(["mensaje" => "Error al eliminar la imagen de la base de datos."]);
        }
    } else {
        echo json_encode(["mensaje" => "Imagen no encontrada."]);
    }
}



// EL POST DE ACTUALIZAR Y GURADR INFIOMES NO VA BIEN
// ELIMINAR IMAGENES DE UN INFORME NO VA BIEN 