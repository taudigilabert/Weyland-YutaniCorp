<?php
header('Content-Type: application/json');
include '../includes/db.php';
session_start();
if (!isset($_SESSION['usuario'])) {
    echo json_encode(array("error" => "No autenticado"));
    return;
}

$accion = $_GET['accion'] ?? '';

switch ($accion) {

    // Obtener todos los roles
    case 'getRoles':
        $sql = "SELECT rol_id, rol_nombre FROM roles ORDER BY rol_nombre ASC";
        $res = $conn->query($sql);
        echo json_encode($res->fetch_all(MYSQLI_ASSOC));
        break;

    // Obtener solo los estados Active/Inactive (sin opción "Todos")
    case 'getEstados':
        echo json_encode([
            ['activo' => 1, 'nombre' => 'Activos'],
            ['activo' => 0, 'nombre' => 'Inactivos']
        ]);
        break;

    // Listar tripulantes (SOLO activos)
    case 'getTripulacion':
        $filtroRol = $_GET['rol'] ?? '';
        // Por defecto activo=1
        $filtroActivo = isset($_GET['activo']) ? $_GET['activo'] : '1';
        $orden = $_GET['orden'] ?? 'ASC';

        $sql = "
            SELECT 
                u.usu_id,
                u.usu_imagen,
                u.usu_nombre,
                u.usu_apellido,
                u.usu_alias,
                r.rol_nombre
            FROM usuarios u
            JOIN roles r ON u.rol_id = r.rol_id
            WHERE u.usu_activo = " . intval($filtroActivo) . "
        ";

        if ($filtroRol !== '') {
            $sql .= " AND u.rol_id = " . intval($filtroRol);
        }

        $sql .= " ORDER BY u.usu_nombre $orden, u.usu_apellido $orden";

        $res = $conn->query($sql);
        echo json_encode($res->fetch_all(MYSQLI_ASSOC));
        break;

    // Obtener solo el estado activo de un usuario (no estrictamente necesario)
    case 'getActivoById':
        $id = intval($_GET['usu_id'] ?? 0);
        $sql = "SELECT usu_activo FROM usuarios WHERE usu_id = $id";
        $r = $conn->query($sql)->fetch_assoc();
        echo json_encode($r);
        break;

    // Marcar inactivo "eliminar" 
    case 'putActivo':
        $id = $_POST['usu_id'] ?? null;
        $activo = $_POST['usu_activo'] ?? null;
        if ($id === null || $activo === null) {
            echo json_encode(["error" => "Faltan datos"]);
            break;
        }
        $sql = "UPDATE usuarios SET usu_activo = ? WHERE usu_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $activo, $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => "Estado actualizado"]);
        } else {
            echo json_encode(["error" => "Error al actualizar"]);
        }
        break;

    // Actualizar campos (alias, nombre, apellido)
    case 'putTripulante':
        $id = $_POST['usu_id'];
        $alias = $_POST['usu_alias'];
        $nombre = $_POST['usu_nombre'];
        $apellido = $_POST['usu_apellido'];

        $sql = "
            UPDATE usuarios SET 
              usu_alias    = ?,
              usu_nombre   = ?,
              usu_apellido = ?
            WHERE usu_id = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssi', $alias, $nombre, $apellido, $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => "Tripulante actualizado"]);
        } else {
            echo json_encode(["error" => "Error al actualizar"]);
        }
        break;

    default:
        echo json_encode(["error" => "Acción no válida"]);
        break;
}
