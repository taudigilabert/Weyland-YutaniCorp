<?php
header('Content-Type: application/json; charset=UTF-8');
session_start();
include '../includes/db.php';

$accion = $_GET['accion'] ?? '';

switch ($accion) {

    // Recuperar un informe (y sus imágenes) para editar o ver
    case 'getInforme':
        if (!isset($_SESSION['usuario'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }
        $inf_id = intval($_GET['inf_id'] ?? 0);
        if (!$inf_id) {
            http_response_code(400);
            echo json_encode(['error' => 'inf_id inválido']);
            exit;
        }
        // Primero, el informe
        $sql = "SELECT inf_concepto, inf_fecha, inf_contenido, usu_id 
            FROM informes_usuario 
            WHERE inf_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $inf_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if (!$inf = mysqli_fetch_assoc($res)) {
            http_response_code(404);
            echo json_encode(['error' => 'Informe no encontrado']);
            exit;
        }
        // Control de permisos
        $esAdmin = in_array($_SESSION['usuario']['rol'], [1, 2]);
        if (!$esAdmin && $inf['usu_id'] !== $_SESSION['usuario']['id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Sin permiso']);
            exit;
        }
        // Luego, las imágenes
        $sql2 = "SELECT img_id, img_ruta FROM informe_imagenes WHERE inf_id = ?";
        $stmt2 = mysqli_prepare($conn, $sql2);
        mysqli_stmt_bind_param($stmt2, 'i', $inf_id);
        mysqli_stmt_execute($stmt2);
        $res2 = mysqli_stmt_get_result($stmt2);
        $imagenes = [];
        while ($row = mysqli_fetch_assoc($res2)) {
            $imagenes[] = $row;
        }
        echo json_encode([
            'informe' => [
                'inf_id' => $inf_id,
                'inf_concepto' => $inf['inf_concepto'],
                'inf_fecha' => $inf['inf_fecha'],
                'inf_contenido' => $inf['inf_contenido']
            ],
            'imagenes' => $imagenes,
            'usu_id' => $inf['usu_id']
        ]);
        break;

    // Crear o actualizar un informe
    case 'postGuardarInforme':
        if (!isset($_SESSION['usuario'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }
        // Recogemos datos
        $inf_id = isset($_POST['inf_id']) && $_POST['inf_id'] !== ''
            ? intval($_POST['inf_id']) : null;
        $concepto = trim($_POST['inf_concepto'] ?? '');
        $fecha = $_POST['inf_fecha'] ?? '';
        $contenido = trim($_POST['inf_contenido'] ?? '');
        $usu_id_sess = $_SESSION['usuario']['id'];
        $esAdmin = in_array($_SESSION['usuario']['rol'], [1, 2]);

        if ($concepto === '' || $fecha === '' || $contenido === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan campos']);
            exit;
        }

        // Insert o update
        if ($inf_id) {
            // Sólo admin o propietario pueden actualizar
            $sql = "UPDATE informes_usuario 
              SET inf_concepto=?, inf_fecha=?, inf_contenido=? 
              WHERE inf_id=? AND usu_id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sssii', $concepto, $fecha, $contenido, $inf_id, $usu_id_sess);
            $ok = mysqli_stmt_execute($stmt);
            $owner = $usu_id_sess;
        } else {
            $estado = 'abierto';
            $sql = "INSERT INTO informes_usuario 
                (usu_id, inf_concepto, inf_fecha, inf_contenido, inf_estado)
              VALUES (?,?,?,?,?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'issss', $usu_id_sess, $concepto, $fecha, $contenido, $estado);
            $ok = mysqli_stmt_execute($stmt);
            $inf_id = mysqli_insert_id($conn);
            $owner = $usu_id_sess;
        }

        if (!$ok) {
            http_response_code(500);
            echo json_encode(['error' => 'Error BD: ' . mysqli_error($conn)]);
            exit;
        }

        // Procesar imágenes subidas (si las hay)
        if (!empty($_FILES['inf_imagenes']['name'][0])) {
            $carpeta = "../img/img-subidas/informes/";
            if (!file_exists($carpeta))
                mkdir($carpeta, 0777, true);
            foreach ($_FILES['inf_imagenes']['tmp_name'] as $i => $tmp) {
                $nombre = uniqid('inf_' . $inf_id . '_') . '_' . basename($_FILES['inf_imagenes']['name'][$i]);
                if (move_uploaded_file($tmp, $carpeta . $nombre)) {
                    $sqlI = "INSERT INTO informe_imagenes (inf_id, img_ruta) VALUES (?,?)";
                    $stI = mysqli_prepare($conn, $sqlI);
                    mysqli_stmt_bind_param($stI, 'is', $inf_id, $nombre);
                    mysqli_stmt_execute($stI);
                    mysqli_stmt_close($stI);
                }
            }
        }

        echo json_encode([
            'success' => 'Informe guardado',
            'inf_id' => $inf_id,
            'usu_id' => $owner
        ]);
        break;

    // Eliminar una imagen de informe
    case 'deleteImagen':
        if (!isset($_SESSION['usuario'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }
        $img_id = intval($_GET['img_id'] ?? 0);
        $inf_id = intval($_GET['inf_id'] ?? 0);
        if (!$img_id || !$inf_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Parámetros inválidos']);
            exit;
        }
        // Comprobamos que el informe nos pertenece o somos admin
        $sql0 = "SELECT usu_id, img_ruta FROM informe_imagenes i
             JOIN informes_usuario u ON i.inf_id = u.inf_id
             WHERE i.img_id=? AND i.inf_id = ?";

        $st0 = mysqli_prepare($conn, $sql0);
        mysqli_stmt_bind_param($st0, 'ii', $img_id, $inf_id);
        mysqli_stmt_execute($st0);
        $r0 = mysqli_stmt_get_result($st0);
        if (!$row0 = mysqli_fetch_assoc($r0)) {
            http_response_code(404);
            echo json_encode(['error' => 'Imagen no encontrada']);
            exit;
        }
        $owner = $row0['usu_id'];
        $esAdmin = in_array($_SESSION['usuario']['rol'], [1, 2]);
        if (!$esAdmin && $owner !== $_SESSION['usuario']['id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Sin permiso']);
            exit;
        }

        // Borramos el fichero y la BD
        @unlink("../img/img-subidas/informes/" . $row0['img_ruta']);
        $sqlD = "DELETE FROM informe_imagenes WHERE img_id=?";
        $stD = mysqli_prepare($conn, $sqlD);
        mysqli_stmt_bind_param($stD, 'i', $img_id);
        mysqli_stmt_execute($stD);
        echo json_encode(['success' => 'Imagen eliminada']);
        break;


    case 'getInformes':
        $rolesPermitidos = ['1', '2'];
        $usuLogueado = $_SESSION['usuario']['id'];

        $usuarioID = isset($_GET['usuario']) && in_array($_SESSION['usuario']['rol'], $rolesPermitidos)
            ? (int) $_GET['usuario']
            : $usuLogueado;

        // Obtener nombre completo
        $sqlUsuario = "SELECT CONCAT(usu_nombre, ' ', usu_apellido) AS nombre_completo FROM usuarios WHERE usu_id = ?";
        $stmt = mysqli_prepare($conn, $sqlUsuario);
        mysqli_stmt_bind_param($stmt, "i", $usuarioID);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $usuario = mysqli_fetch_assoc($res)['nombre_completo'] ?? null;
        mysqli_stmt_close($stmt);

        if (!$usuario) {
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit();
        }

        // Obtener informes
        $sqlInformes = "SELECT inf_id, inf_concepto, inf_fecha, inf_estado FROM informes_usuario WHERE usu_id = ? ORDER BY inf_fecha DESC";
        $stmt = mysqli_prepare($conn, $sqlInformes);
        mysqli_stmt_bind_param($stmt, "i", $usuarioID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $informes = [];
        while ($fila = mysqli_fetch_assoc($result)) {
            $informes[] = $fila;
        }
        mysqli_stmt_close($stmt);

        echo json_encode([
            'usuario_id' => $usuarioID,
            'usuario_nombre' => $usuario,
            'esPropio' => ($usuarioID === $usuLogueado),
            'informes' => $informes
        ]);
        break;



    // Cambiar el estado de un informe (abierto/archivado)
    case 'archivarInforme':
        if (!isset($_SESSION['usuario'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }

        $inf_id = intval($_POST['inf_id'] ?? 0);
        if (!$inf_id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
            exit;
        }

        // Verificar permisos
        $sql = "SELECT usu_id, inf_estado FROM informes_usuario WHERE inf_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $inf_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);

        if (!$row) {
            http_response_code(404);
            echo json_encode(['error' => 'Informe no encontrado']);
            exit;
        }

        $esAdmin = in_array($_SESSION['usuario']['rol'], [1, 2]);
        if (!$esAdmin && $row['usu_id'] !== $_SESSION['usuario']['id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Sin permiso']);
            exit;
        }

        // Toggle estado
        $nuevoEstado = $row['inf_estado'] === 'archivado' ? 'abierto' : 'archivado';

        $sqlA = "UPDATE informes_usuario SET inf_estado = ? WHERE inf_id = ?";
        $stmt = mysqli_prepare($conn, $sqlA);
        mysqli_stmt_bind_param($stmt, 'si', $nuevoEstado, $inf_id);
        mysqli_stmt_execute($stmt);

        echo json_encode(['success' => 'Estado actualizado', 'nuevo_estado' => $nuevoEstado]);
        break;


    // Eliminar un informe
    case 'eliminarInforme':
        if (!isset($_SESSION['usuario'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }

        $inf_id = intval($_POST['inf_id'] ?? 0);
        if (!$inf_id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
            exit;
        }

        // Verificamos si el usuario es propietario o admin
        $sql = "SELECT usu_id FROM informes_usuario WHERE inf_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $inf_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);

        if (!$row) {
            http_response_code(404);
            echo json_encode(['error' => 'Informe no encontrado']);
            exit;
        }

        $esAdmin = in_array($_SESSION['usuario']['rol'], [1, 2]);
        if (!$esAdmin && $row['usu_id'] !== $_SESSION['usuario']['id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Sin permiso']);
            exit;
        }

        // Eliminar imágenes asociadas
        $sqlImgs = "SELECT img_ruta FROM informe_imagenes WHERE inf_id = ?";
        $stmtImgs = mysqli_prepare($conn, $sqlImgs);
        mysqli_stmt_bind_param($stmtImgs, 'i', $inf_id);
        mysqli_stmt_execute($stmtImgs);
        $resImgs = mysqli_stmt_get_result($stmtImgs);
        while ($img = mysqli_fetch_assoc($resImgs)) {
            @unlink("../img/img-subidas/informes/" . $img['img_ruta']);
        }

        $sqlDelImgs = "DELETE FROM informe_imagenes WHERE inf_id = ?";
        $stmtDelImgs = mysqli_prepare($conn, $sqlDelImgs);
        mysqli_stmt_bind_param($stmtDelImgs, 'i', $inf_id);
        mysqli_stmt_execute($stmtDelImgs);

        // Eliminar el informe
        $sqlDelInf = "DELETE FROM informes_usuario WHERE inf_id = ?";
        $stmtDelInf = mysqli_prepare($conn, $sqlDelInf);
        mysqli_stmt_bind_param($stmtDelInf, 'i', $inf_id);
        mysqli_stmt_execute($stmtDelInf);

        echo json_encode(['success' => 'Informe eliminado']);
        break;


    default:
        http_response_code(400);
        echo json_encode(['error' => 'Acción no válida']);
        break;
}
