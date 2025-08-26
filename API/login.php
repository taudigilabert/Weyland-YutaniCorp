<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require('../includes/db.php');

$usu_alias      = isset($_POST['usu_alias'])      ? trim($_POST['usu_alias'])      : '';
$usu_contrasena = isset($_POST['usu_contrasena']) ? trim($_POST['usu_contrasena']) : '';

if ($usu_alias === '' || $usu_contrasena === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, ingresa un alias y una contraseña.'
    ]);
    exit();
}

// Ya filtras activo = 1 en la consulta
$query = "SELECT * FROM usuarios WHERE usu_alias = ? AND usu_activo = 1";
$stmt  = $conn->prepare($query);
$stmt->bind_param("s", $usu_alias);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // No existe o está inactivo
    echo json_encode([
        'success'  => false,
        'redirect' => '../Paladio/error.php?error=credenciales'
    ]);
    exit();
}

$user = $result->fetch_assoc();  // ← Aquí ya tienes al usuario

// (Si no usas el filtro en SQL, podrías comprobar:
// if (!$user['usu_activo']) { ...redirect desactivado... }
// )

if (!password_verify($usu_contrasena, $user['usu_contrasena'])) {
    echo json_encode([
        'success'  => false,
        'redirect' => '../Paladio/error.php?error=credenciales'
    ]);
    exit();
}

// Si llegamos aquí, las credenciales son válidas
$token = bin2hex(random_bytes(32));
$update_query = "UPDATE usuarios SET usu_token = ? WHERE usu_id = ?";
$update_stmt  = $conn->prepare($update_query);
$update_stmt->bind_param("si", $token, $user['usu_id']);
$update_stmt->execute();

// Guardar datos en sesión
$_SESSION['usuario'] = [
    'id'       => $user['usu_id'],
    'alias'    => $user['usu_alias'],
    'nombre'   => $user['usu_nombre'],
    'apellido' => $user['usu_apellido'],
    'imagen'   => $user['usu_imagen'],
    'rol'      => $user['rol_id'],
];
$_SESSION['token'] = $token;

// Respuesta JSON para el frontend
echo json_encode([
    'success' => true,
    'token'   => $token
]);

$conn->close();
