<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require('../includes/db.php');

// 1) Sólo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// 2) Validación de campos
$campos = ['nombre','apellido','alias','rol','genero','biografia','contrasena','contrasenaRepetida'];
foreach ($campos as $c) {
    if (empty($_POST[$c])) {
        echo json_encode(['success' => false, 'message' => "Falta el campo $c"]);
        exit();
    }
}

// 3) Recogida de datos y validación de contraseña
$nombre    = trim($_POST['nombre']);
$apellido  = trim($_POST['apellido']);
$alias     = trim($_POST['alias']);
$rol_id    = intval($_POST['rol']);
$genero    = $_POST['genero'];
$bio       = trim($_POST['biografia']);
$pass      = $_POST['contrasena'];
$pass2     = $_POST['contrasenaRepetida'];

if ($pass !== $pass2) {
    echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden']);
    exit();
}

// 4) Comprueba alias único
$stmt = $conn->prepare("SELECT usu_id FROM usuarios WHERE usu_alias = ?");
$stmt->bind_param("s", $alias);
$stmt->execute(); $stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'El alias ya está en uso']);
    exit();
}
$stmt->close();

// 5) Procesa la imagen (igual que antes)…
$nombreArchivo = "userDefault.jpg";
if (isset($_FILES['fotoPerfil']) && $_FILES['fotoPerfil']['error']===UPLOAD_ERR_OK) {
    $tmp  = $_FILES['fotoPerfil']['tmp_name'];
    $orig = $_FILES['fotoPerfil']['name'];
    $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    $permit = ['jpg','jpeg','png'];
    if (!in_array($ext,$permit) || $_FILES['fotoPerfil']['size']>2*1024*1024) {
        echo json_encode(['success'=>false,'message'=>'Imagen inválida o demasiado grande']);
        exit();
    }
    $nombreArchivo = uniqid("fotoPerfil_",true).".".$ext;
    if (!move_uploaded_file($tmp, "../img/fotoPerfil/$nombreArchivo")) {
        echo json_encode(['success'=>false,'message'=>'Error al guardar la imagen']);
        exit();
    }
}

// 6) Inserta usuario
$hash = password_hash($pass, PASSWORD_BCRYPT);
$sql = "INSERT INTO usuarios 
    (usu_nombre,usu_apellido,usu_alias,rol_id,usu_genero,usu_biografia,usu_imagen,usu_contrasena)
    VALUES (?,?,?,?,?,?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssissss",
    $nombre, $apellido, $alias, $rol_id, $genero, $bio, $nombreArchivo, $hash
);
if (!$stmt->execute()) {
    echo json_encode(['success'=>false,'message'=>'Error al registrar']);
    exit();
}
$usu_id = $conn->insert_id;
$stmt->close();

// 7) Genera token y actualiza la fila
$token = bin2hex(random_bytes(32));
$upd = $conn->prepare("UPDATE usuarios SET usu_token = ? WHERE usu_id = ?");
$upd->bind_param("si", $token, $usu_id);
$upd->execute();
$upd->close();

// 8) Carga los datos del usuario para la sesión
$q = $conn->prepare("SELECT usu_nombre, usu_apellido, usu_alias, rol_id, usu_imagen FROM usuarios WHERE usu_id = ?");
$q->bind_param("i", $usu_id);
$q->execute();
$res = $q->get_result()->fetch_assoc();
$q->close();

// 9) Inicializa la sesión igual que en login.php
$_SESSION['usuario'] = [
    'id'       => $usu_id,
    'alias'    => $res['usu_alias'],
    'nombre'   => $res['usu_nombre'],
    'apellido' => $res['usu_apellido'],
    'imagen'   => $res['usu_imagen'],
    'rol'      => $res['rol_id'],
];
$_SESSION['token'] = $token;

// 10) Respuesta JSON
echo json_encode([
    'success' => true,
    'token'   => $token,
    'usuario' => $_SESSION['usuario']
]);

$conn->close();
