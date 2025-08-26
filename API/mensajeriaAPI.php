<?php
// HABILITAR CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../includes/db.php';
session_start();


$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

switch ($accion) {
    case 'getRoles':
        getRoles($conn);
        break;
    case 'getUsuarios':
        getUsuarios($conn);
        break;
    case 'postEnviarMensaje':
        postEnviarMensaje($conn);
        break;
    case 'getMensajes':
        getMensajes($conn);
        break;
    case 'deleteMensaje':
        deleteMensaje($conn);
        break;
    case 'actualizarNotificacion':
        actualizarNotificacion($conn);
        break;

    // Para obtener un mensaje específico (para responder)
    case 'getMensaje':
        if (!isset($_GET['mensaje_id']) || !is_numeric($_GET['mensaje_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Falta mensaje_id válido']);
            exit;
        }
        $mensajeID = (int) $_GET['mensaje_id'];

        // Preparar y ejecutar consulta
        $sql = "SELECT men_id, men_asunto, men_contenido FROM mensajes WHERE men_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $mensajeID);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($res)) {
            // Si no empieza con "RE: ", lo añadimos
            if (strpos($row['men_asunto'], 'RE: ') !== 0) {
                $row['men_asunto'] = 'RE: ' . $row['men_asunto'];
            }
            echo json_encode(['mensaje' => $row]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Mensaje no encontrado']);
        }
        mysqli_stmt_close($stmt);
        break;

    default:
        echo json_encode(array("mensaje" => "El sistema no reconoce la acción solicitada"));
        break;
}

// =================== FUNCIONES ===================

/* GET: Recuperar todos los roles*/
function getRoles($conn)
{
    $sqlRoles = "SELECT * FROM roles";
    $resultado = mysqli_query($conn, $sqlRoles);
    $roles = array();

    while ($row = mysqli_fetch_assoc($resultado)) {
        $roles[] = $row;
    }

    echo json_encode(array("roles" => $roles));
}

/* GET: Recuperar todos los usuarios excepto el usu logueado */
function getUsuarios($conn)
{
    if (!isset($_SESSION['usuario'])) {
        echo json_encode(array("error" => "No autenticado"));
        return;
    }

    $loggedUserId = $_SESSION['usuario']['id'];

    // Preparar sentencia: seleccionar todos los usuarios menos con el del logueado
    $sqlUsuarios = "SELECT * FROM usuarios WHERE usu_id != ?";
    $stmt = mysqli_prepare($conn, $sqlUsuarios);
    mysqli_stmt_bind_param($stmt, "i", $loggedUserId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $usuarios = array();
    while ($row = mysqli_fetch_assoc($resultado)) {
        $usuarios[] = $row;
    }

    mysqli_stmt_close($stmt);

    echo json_encode(array("usuarios" => $usuarios));
}

// POST: Enviar mensaje
function postEnviarMensaje($conn)
{
    if (!isset($_SESSION['usuario'])) {
        echo json_encode(array("error" => "No autenticado"));
        return;
    }

    // Recoger datos de entrada
    $remitente = $_POST['remitente'] ?? null;
    $asunto = isset($_POST['asunto']) && !empty(trim($_POST['asunto'])) ? trim($_POST['asunto']) : 'Sin asunto';
    $rol_destinatario = isset($_POST['rol_destinatario']) ? (int) $_POST['rol_destinatario'] : null;
    $destinatarios = $_POST['destinatarios'] ?? [];
    $fecha = $_POST['fecha'] ?? null;
    $contenido = isset($_POST['contenido']) ? trim($_POST['contenido']) : '';

    // Validar que se haya seleccionado al menos 1 receptor
    if (empty($rol_destinatario) && empty($destinatarios)) {
        echo json_encode(array("error" => "Debes seleccionar un destinatario"));
        return;
    }

    // INSERT del mensaje en tabla "mensajes"
    $sqlMensaje = "INSERT INTO mensajes (men_asunto, men_remitente, men_contenido, men_fecha, rol_id) VALUES (?, ?, ?, ?, ?)";
    $stmtMensaje = mysqli_prepare($conn, $sqlMensaje);
    mysqli_stmt_bind_param($stmtMensaje, "sissi", $asunto, $remitente, $contenido, $fecha, $rol_destinatario);
    $mensajeInsertado = mysqli_stmt_execute($stmtMensaje);

    if (!$mensajeInsertado) {
        echo json_encode(array("error" => "Error al enviar el mensaje: " . mysqli_error($conn)));
        return;
    }

    $mensajeID = mysqli_insert_id($conn); // ID del mensaje insertado

    // Procesar imágenes si existen
    if (isset($_FILES['archivo']) && !empty($_FILES['archivo']['name'][0])) {
        $carpetaImagenes = "../img/img-subidas/mensajes/";
        if (!file_exists($carpetaImagenes)) {
            mkdir($carpetaImagenes, 0777, true);
        }

        $archivos = $_FILES['archivo'];
        $extensionesPermitidas = ['jpg', 'jpeg', 'png'];
        $maxTamano = 5 * 1024 * 1024; // 5mb

        foreach ($archivos['tmp_name'] as $index => $tmpName) {
            $nombreArchivo = $archivos['name'][$index];
            $extensionArchivo = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
            $tamanoArchivo = $archivos['size'][$index];

            if (!in_array($extensionArchivo, $extensionesPermitidas)) {
                echo json_encode(array("error" => "Archivo no válido. Solo se permite jpg, jpeg, png"));
                return;
            }

            if ($tamanoArchivo > $maxTamano) {
                echo json_encode(array("error" => "El archivo es demasiado grande"));
                return;
            }

            // Generar un nombre único para la imagen
            $nombreImagen = uniqid('img_', true) . '.' . $extensionArchivo;
            $rutaImagen = $nombreImagen;

            if (move_uploaded_file($tmpName, $carpetaImagenes . $rutaImagen)) {
                // Insertar la ruta de la imagen en la tabla "mensajes_imagenes"
                $sqlImagen = "INSERT INTO mensajes_imagenes (men_id, mimg_ruta) VALUES (?, ?)";
                $stmtImagen = mysqli_prepare($conn, $sqlImagen);
                mysqli_stmt_bind_param($stmtImagen, "is", $mensajeID, $rutaImagen);
                mysqli_stmt_execute($stmtImagen);
                mysqli_stmt_close($stmtImagen);
            } else {
                echo json_encode(array("error" => "Error al subir la imagen"));
                return;
            }
        }
    }

    // Función interna para insertar receptores
    function insertarReceptores($conn, $mensajeID, $receptores)
    {
        $sqlReceptores = "INSERT INTO mensaje_receptores (men_id, mec_receptor) VALUES (?, ?)";
        $stmtReceptores = mysqli_prepare($conn, $sqlReceptores);

        foreach ($receptores as $receptor) {
            mysqli_stmt_bind_param($stmtReceptores, "ii", $mensajeID, $receptor);
            mysqli_stmt_execute($stmtReceptores);
        }
        mysqli_stmt_close($stmtReceptores);
    }

    // Si se ha definido un rol para los destinatarios, se buscan todos los usuarios con ese rol
    if (!empty($rol_destinatario)) {
        $sqlUsuariosRol = "SELECT usu_id FROM usuarios WHERE rol_id = ?";
        $stmtUsuariosRol = mysqli_prepare($conn, $sqlUsuariosRol);
        mysqli_stmt_bind_param($stmtUsuariosRol, "i", $rol_destinatario);
        mysqli_stmt_execute($stmtUsuariosRol);
        $resultUsuariosRol = mysqli_stmt_get_result($stmtUsuariosRol);

        $receptores = array();
        while ($usuarioRol = mysqli_fetch_assoc($resultUsuariosRol)) {
            $receptores[] = $usuarioRol['usu_id'];
        }
        insertarReceptores($conn, $mensajeID, $receptores);
    }

    // Si se han seleccionado receptores individuales
    if (!empty($destinatarios)) {
        insertarReceptores($conn, $mensajeID, $destinatarios);
    }

    mysqli_stmt_close($stmtMensaje);
    echo json_encode(array("success" => "Mensaje enviado correctamente"));

}

/* GET: Recuperar todos los mensajes del usuario logueado */
function getMensajes($conn)
{
    if (!isset($_SESSION['usuario'])) {
        echo json_encode(["error" => "No autenticado"]);
        return;
    }
    $usuarioID = $_SESSION['usuario']['id'];

    // Mensajes recibidos ordenados por fecha
    $sql = "
                SELECT
                    m.men_id,
                    m.men_asunto,
                    m.men_contenido,
                    m.men_fecha,
                    u.usu_nombre,
                    u.usu_apellido,
                    u.usu_id AS remitente_id
                FROM mensajes m
                JOIN mensaje_receptores mr ON m.men_id = mr.men_id
                JOIN usuarios u ON m.men_remitente = u.usu_id
                WHERE mr.mec_receptor = ?
                ORDER BY m.men_fecha DESC
            ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $usuarioID);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $mensajes = [];
    while ($m = mysqli_fetch_assoc($res)) {
        // Traer imágenes adjuntas
        $sqlImg = "SELECT mimg_ruta FROM mensajes_imagenes WHERE men_id = ?";
        $stImg = mysqli_prepare($conn, $sqlImg);
        mysqli_stmt_bind_param($stImg, "i", $m['men_id']);
        mysqli_stmt_execute($stImg);
        $resImg = mysqli_stmt_get_result($stImg);

        $imgs = [];
        while ($rowImg = mysqli_fetch_assoc($resImg)) {
            $imgs[] = $rowImg['mimg_ruta'];
        }
        mysqli_stmt_close($stImg);

        $m['imagenes'] = $imgs;
        $mensajes[] = $m;
    }
    mysqli_stmt_close($stmt);

    echo json_encode(["mensajes" => $mensajes]);
}

/* POST: Eliminar un mensaje (solo para el usuario receptor) */
function deleteMensaje($conn)
{
    if (!isset($_SESSION['usuario'])) {
        echo json_encode(["error" => "No autenticado"]);
        return;
    }
    // Obtener ID de mensaje (puede venir por POST o JSON)
    $data = json_decode(file_get_contents("php://input"), true);
    $mensajeID = isset($data['mensaje_id']) ? (int) $data['mensaje_id'] : null;
    $usuarioID = $_SESSION['usuario']['id'];

    if (!$mensajeID) {
        echo json_encode(["error" => "Falta mensaje_id"]);
        return;
    }

    // Verificar que el usuario sea receptor
    $check = "SELECT 1 FROM mensaje_receptores WHERE men_id = ? AND mec_receptor = ?";
    $st = mysqli_prepare($conn, $check);
    mysqli_stmt_bind_param($st, "ii", $mensajeID, $usuarioID);
    mysqli_stmt_execute($st);
    $resChk = mysqli_stmt_get_result($st);
    if (mysqli_num_rows($resChk) === 0) {
        echo json_encode(["error" => "No tienes permiso para borrar este mensaje"]);
        return;
    }
    mysqli_stmt_close($st);

    // 1) Eliminar solo la relación receptor–mensaje
    $delRec = "DELETE FROM mensaje_receptores WHERE men_id = ? AND mec_receptor = ?";
    $stDel = mysqli_prepare($conn, $delRec);
    mysqli_stmt_bind_param($stDel, "ii", $mensajeID, $usuarioID);
    if (!mysqli_stmt_execute($stDel)) {
        echo json_encode(["error" => "Error al eliminar receptor"]);
        return;
    }
    mysqli_stmt_close($stDel);

    // 2) Comprobar si quedan receptores para ese mensaje
    $countSql = "SELECT COUNT(*) AS cnt FROM mensaje_receptores WHERE men_id = ?";
    $stCnt = mysqli_prepare($conn, $countSql);
    mysqli_stmt_bind_param($stCnt, "i", $mensajeID);
    mysqli_stmt_execute($stCnt);
    $resCnt = mysqli_stmt_get_result($stCnt);
    $rowCnt = mysqli_fetch_assoc($resCnt);
    mysqli_stmt_close($stCnt);

    if ($rowCnt['cnt'] == 0) {
        // Si ya no hay receptores, borrar mensaje e imágenes
        $delMsg = "DELETE FROM mensajes WHERE men_id = ?";
        $stM = mysqli_prepare($conn, $delMsg);
        mysqli_stmt_bind_param($stM, "i", $mensajeID);
        mysqli_stmt_execute($stM);
        mysqli_stmt_close($stM);

        $delImg = "DELETE FROM mensajes_imagenes WHERE men_id = ?";
        $stI = mysqli_prepare($conn, $delImg);
        mysqli_stmt_bind_param($stI, "i", $mensajeID);
        mysqli_stmt_execute($stI);

        mysqli_stmt_close($stI);
    }

    echo json_encode(["success" => true]);
    return;
}

function actualizarNotificacion($conn)
{
    if (!isset($_SESSION['usuario'])) {
        echo json_encode(["error" => "No autenticado"]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $mensajeID = isset($data['mensaje_id']) ? (int) $data['mensaje_id'] : null;
    $usuarioID = $_SESSION['usuario']['id'];

    if (!$mensajeID) {
        echo json_encode(["error" => "Falta mensaje_id"]);
        return;
    }

    $sqlActualizar = "UPDATE mensaje_receptores SET mec_notificacion = 0 WHERE men_id = ? AND mec_receptor = ?";
    $stmtActualizar = mysqli_prepare($conn, $sqlActualizar);
    mysqli_stmt_bind_param($stmtActualizar, "ii", $mensajeID, $usuarioID);

    if (!mysqli_stmt_execute($stmtActualizar)) {
        echo json_encode(["error" => "Error al actualizar notificación"]);
        return;
    }

    mysqli_stmt_close($stmtActualizar);
    echo json_encode(["success" => true]);
    return;
}


?>