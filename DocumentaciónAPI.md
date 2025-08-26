![Logo Weyland Yutani](./img/logoTransparente.png)


#### Documentación API
# USCSS Paladio

## Explicación

El proyecto gestiona los documentos relacionados con la API en una carpeta denominada **API**, la cual alberga varios archivos de **back-end**. Cada uno de estos archivos contiene funciones específicas para manejar las funcionalidades del sistema.
A continuación, se describe el contenido de cada archivo:

### Archivos en la carpeta API:
- **gestionTripulacionAPI.php**
  Contiene las funciones encargadas de gestionar la tripulación de la nave, incluyendo la asignación y los roles.

- **informesAPI.php**
  Funciones relacionadas con la gestión de los informes de la tripulación, como la creación, modificación y eliminación de informes.

- **registroAPI.php**
  Gestiona el proceso de creación de un nuevo tripulante en la nave.

- **login.php**
  Maneja el proceso de autenticación de los usuarios, incluyendo la verificación de credenciales y la creación de sesiones o tokens.
  
- **mensajeriaAPI.php**
  Proporciona las funciones necesarias para el envío y la recepción de mensajes entre los miembros de la tripulación.
  
- **perfilAPI.php**
  Contiene las funciones relacionadas con el manejo de perfiles de los usuarios, como la visualización y edición de los datos de usuario.

Esta documentación detallará cada función implementada en estos archivos para explicar su propósito, uso y cómo interactúan entre sí en el sistema.

---

##  GESTOR DE TRIPULANTES
`gestionTripilacionAPI.php`

### 📄 getRoles
Obtener todos los roles

**GET** `/API/gestionTripulación?accion=getRoles`

**Respuesta:**
```json
[
  {
    "rol_id": "1",
    "rol_nombre": "Capitán"
  },
  {
    "rol_id": "8",
    "rol_nombre": "Científico principal"
  },
  {
    "rol_id": "5",
    "rol_nombre": "Ingeniero de mantenimiento"
  },
  {
    "rol_id": "3",
    "rol_nombre": "Ingeniero jefe"
  },
  {
    "rol_id": "7",
    "rol_nombre": "Médico oficial"
  },
  {
    "rol_id": "4",
    "rol_nombre": "Oficial de seguridad"
  },
  {
    "rol_id": "6",
    "rol_nombre": "Piloto"
  },
  {
    "rol_id": "2",
    "rol_nombre": "Primer oficial"
  }
]
```
---

### 📄 getEstados
Obtener solo los estados Active/Inactive

**GET** `/API/gestionTripulación?accion=getEstados`

**Respuesta:**
```json
[
  {
    "activo": 1,
    "nombre": "Activos"
  },
  {
    "activo": 0,
    "nombre": "Inactivos"
  }
]
```
---

### 📄 getTripulacion
Listar tripulantes (SOLO activos)

**GET** `/API/gestionTripulación?accion=getTripulacion`

**Respuesta:**
```json
[
  {
    "usu_id": "12",
    "usu_imagen": "fotoPerfil67f3e4eabad46.jpg",
    "usu_nombre": "Tomas",
    "usu_apellido": "Audi",
    "usu_alias": "tomas",
    "rol_nombre": "Capitán"
  }
]
```
---

### 📄 getActivoById
Obtener solo el estado activo de un usuario (no estrictamente necesario)

**GET** `/API/gestionTripulación?accion=getActivoById`

**Respuesta:**
```json
[
 {
  "usu_activo": "1"
 }
]
```
---

### 📄 putActivo
  Marcar inactivo "eliminar" 

**GET** `/API/gestionTripulación?accion=putActivo`

**Respuesta:**
```json
[
{
    "success": "Estado actualizado"
}
]
```
---

### 📄 putTripulante
Actualizar campos (alias, nombre, apellido)

**POST** `/API/gestionTripulacionAPI.php?accion=putTripulante`

**Envio en formato `form-data`:**

```plaintext
usu_id=1
usu_alias=Juanito
usu_nombre=Juan
usu_apellido=Pérez
```

**Respuesta:**
```json
[
 {
    "success": "Tripulante actualizado"
 }
]
```
---

##  GESTOR DE INFORMES

`informesAPI.php`

### 📄 getInforme
Recuperar un informe (y sus imágenes) para editar o ver

**GET** `/API/gestionTripulacionAPI.php?accion=getInforme&inf_id={id}`

**Parámetros:**
- `inf_id`: ID del informe a recuperar.

**Respuesta:**
```json
{
    "informe": {
        "inf_id": 1,
        "inf_concepto": "Informe sobre el estado de la nave",
        "inf_fecha": "2025-04-21",
        "inf_contenido": "El informe contiene detalles sobre el estado general de la nave y sus sistemas."
    },
    "imagenes": [
        {
            "img_id": 1,
            "img_ruta": "/images/informe1.jpg"
        },
        {
            "img_id": 2,
            "img_ruta": "/images/informe2.jpg"
        }
    ],
    "usu_id": 123
}
```
---

### 📄 postGuardarInforme
Crear o actualizar un informe

**POST** `/API/gestionTripulacionAPI.php?accion=postGuardarInforme`

**Parámetros:**
- `inf_id`: (Opcional) ID del informe. Si se proporciona, se actualizará el informe existente.
- `inf_concepto`: Concepto del informe (requerido).
- `inf_fecha`: Fecha del informe (requerido).
- `inf_contenido`: Contenido del informe (requerido).
- `inf_imagenes`: (Opcional) Archivos de imagen asociados al informe.

**Respuesta:**

```json
{
    "success": "Informe guardado",
    "inf_id": 1,
    "usu_id": 123
}
```
---

### 📄 deleteImagen
Eliminar una imagen de informe

**GET** `/API/gestionTripulacionAPI.php?accion=deleteImagen&img_id={id_imagen}&inf_id={id_informe}`

**Parámetros:**
- `img_id`: ID de la imagen a eliminar (requerido).
- `inf_id`: ID del informe asociado a la imagen (requerido).

**Respuesta:**

```json
{
    "success": "Imagen eliminada"
}
```
---

### 📄 getInformes
Obtener los informes de un usuario.

**GET** `/API/gestionTripulacionAPI.php?accion=getInformes&usuario={id_usuario}`

**Parámetros:**
- `usuario`: ID del usuario cuyas informes se quieren obtener (opcional, si no se especifica, se obtienen los informes del usuario logueado).

**Respuesta:**

```json
{
    "usuario_id": 1,
    "usuario_nombre": "Juan Pérez",
    "esPropio": true,
    "informes": [
        {
            "inf_id": 1,
            "inf_concepto": "Informe sobre el estado de la nave",
            "inf_fecha": "2025-04-21",
            "inf_estado": "abierto"
        },
        {
            "inf_id": 2,
            "inf_concepto": "Informe de mantenimiento",
            "inf_fecha": "2025-04-20",
            "inf_estado": "cerrado"
        }
    ]
}
```
---

### 📄 archivarInforme
Cambiar el estado de un informe entre "abierto" y "archivado".

**POST** `/API/gestionTripulacionAPI.php?accion=archivarInforme`

**Parámetros:**
- `inf_id`: ID del informe a actualizar.

**Respuesta:**

```json
{
    "success": "Estado actualizado",
    "nuevo_estado": "archivado"
}
```
---

### 📄 eliminarInforme
Eliminar un informe y sus imágenes asociadas.

**POST** `/API/gestionTripulacionAPI.php?accion=eliminarInforme`

**Parámetros:**
- `inf_id`: ID del informe a eliminar.

**Respuesta:**

```json
{
    "success": "Informe eliminado"
}
```
---

##  SERVICIO DE MENSAJERÍA
`mensajeriaAPU.php`

### 📄 getMensaje
Obtener un mensaje específico.

**GET** `/API/gestionMensajesAPI.php?accion=getMensaje&mensaje_id={mensaje_id}`

**Parámetros:**
- `mensaje_id`: ID del mensaje a obtener.

**Respuesta:**

Si el mensaje se encuentra en la base de datos:

```json
{
    "mensaje": {
        "men_id": 1,
        "men_asunto": "RE: Asunto del mensaje",
        "men_contenido": "Contenido del mensaje"
    }
}
```
---

### 📄 getRoles
Recuperar todos los roles disponibles.

**GET** `/API/rolesAPI.php?accion=getRoles`

**Respuesta:**

Si la consulta es correcta:

```json
{
    "roles": [
        {
            "rol_id": 1,
            "rol_nombre": "Administrador",
            "rol_descripcion": "Acceso completo al sistema"
        },
        {
            "rol_id": 2,
            "rol_nombre": "Usuario",
            "rol_descripcion": "Acceso limitado"
        },
        // más roles...
    ]
}
```
---

### 📄 getUsuarios
Recupera todos los usuarios excepto el usuario actualmente autenticado.

**GET** `/API/usuariosAPI.php?accion=getUsuarios`

**Respuesta:**

Si la consulta es exitosa:

```json
{
    "usuarios": [
        {
            "usu_id": 1,
            "usu_nombre": "Juan",
            "usu_apellido": "Pérez",
            "usu_email": "juan@dominio.com",
            "usu_rol": 2
        },
        {
            "usu_id": 2,
            "usu_nombre": "María",
            "usu_apellido": "García",
            "usu_email": "maria@dominio.com",
            "usu_rol": 1
        },
        // más usuarios...
    ]
}
```
---

### 📩 postEnviarMensaje
Envía un mensaje a uno o varios destinatarios.

**POST** `/API/mensajesAPI.php?accion=postEnviarMensaje`

**Parámetros:**

- `remitente`: ID del usuario remitente (debe estar autenticado).
- `asunto`: Asunto del mensaje. Si no se especifica, será "Sin asunto".
- `rol_destinatario`: ID del rol del destinatario.
- `destinatarios`: Array con los ID de los destinatarios específicos (si se utiliza en lugar de `rol_destinatario`).
- `fecha`: Fecha del mensaje (opcional).
- `contenido`: Contenido del mensaje (obligatorio).
- `archivo`: Archivos adjuntos (opcional). Solo se permite imágenes en formato JPG, JPEG o PNG.

**Respuesta:**

Si la consulta es exitosa:

```json
{
    "success": "Mensaje enviado con éxito"
}
```
---

### 📩 getMensajes
Recupera todos los mensajes recibidos por el usuario logueado.

**GET**  `/API/mensajesAPI.php?accion=getMensajes`

**Parámetros:**

- `conn`: Conexión a la base de datos.

**Descripción:**
1. Recupera los mensajes recibidos por el usuario logueado, ordenados por fecha de recepción.
2. Para cada mensaje, obtiene las imágenes adjuntas de la tabla `mensajes_imagenes`.

**Resultado:**
Devuelve un JSON con los mensajes recibidos, su asunto, contenido, fecha, remitente y las imágenes asociadas.

**Respuesta JSON:**
```json
{
    "mensajes": [
        {
            "men_id": 1,
            "men_asunto": "Asunto del mensaje",
            "men_contenido": "Contenido del mensaje",
            "men_fecha": "2025-04-20 10:00:00",
            "usu_nombre": "Nombre Remitente",
            "usu_apellido": "Apellido Remitente",
            "remitente_id": 2,
            "imagenes": ["imagen1.jpg", "imagen2.jpg"]
        },
        //...
    ]
}
```
---
### 🗑️ deleteMensaje
Elimina un mensaje, pero solo para el receptor del mensaje.

**DELETE**  `/API/mensajesAPI.php?accion=deleteMensaje`

**Parámetros:**

- `conn`: Conexión a la base de datos.

**Descripción:**
1. Verifica que el usuario esté autenticado.
2. Recibe el ID del mensaje a eliminar, que debe ser proporcionado en el cuerpo de la solicitud (en formato JSON).
3. Verifica que el usuario sea receptor del mensaje.
4. Si el usuario tiene permiso, elimina la relación receptor-mensaje en la tabla `mensaje_receptores`.
5. Si el mensaje no tiene más receptores, elimina el mensaje de la tabla `mensajes` y sus imágenes asociadas.

**Resultado:**
Devuelve un JSON con el estado de la operación.

**Respuesta JSON:**
```json
{
    "success": true
}
```
---

### 🔔 actualizarNotificacion
Actualiza el estado de la notificación de un mensaje para el usuario receptor (pone la notificación como leída).

**POST**  `/API/mensajesAPI.php?accion=actualizarNotificacion`

**Parámetros:**

- `conn`: Conexión a la base de datos.

**Descripción:**
1. Verifica que el usuario esté autenticado.
2. Recibe el ID del mensaje a actualizar, que debe ser proporcionado en el cuerpo de la solicitud (en formato JSON).
3. Verifica que el `mensaje_id` sea válido.
4. Actualiza el campo `mec_notificacion` en la tabla `mensaje_receptores` para marcar la notificación como leída (establece su valor a 0).

**Resultado:**
Devuelve un JSON con el estado de la operación.

**Respuesta JSON:**
```json
{
    "success": true
}
```
---
##  PERFIL DEL TRIPUANTE
`perfilAPI.php`

### 👤 getPerfil
Recupera la información completa del perfil del usuario autenticado.

**GET**  `/API/perfilAPI.php?accion=getPerfil`


**Parámetros:**

- `conn`: Conexión a la base de datos.

**Descripción:**
1. Verifica que el ID de usuario esté disponible en la sesión.
2. Realiza una consulta para obtener los datos del usuario, su rol, y la nave asignada (si existe) mediante una combinación de las tablas `usuarios`, `roles` y `nave`.
3. Si el usuario se encuentra, devuelve todos los datos del perfil junto con el nombre completo del usuario, concatenando `usu_nombre` y `usu_apellido`.
4. Si no se encuentra el usuario o hay un error, devuelve un mensaje de error adecuado.

**Resultado:**
Devuelve un JSON con los datos del perfil del usuario.

**Respuesta JSON:**
```json
{
    "usu_id": 1,
    "usu_nombre": "John",
    "usu_apellido": "Doe",
    "usu_alias": "johndoe",
    "usu_genero": "M",
    "usu_biografia": "Texto de biografía.",
    "usu_imagen": "path_to_image.jpg",
    "usu_numero_empleado": "12345",
    "usu_fecha_creacion": "2022-01-01",
    "usu_contrasena": "hashedpassword",
    "usu_activo": 1,
    "rol_id": 1,
    "rol_nombre": "Administrador",
    "rol_descripcion": "Rol de administrador",
    "nav_nombre": "Nave A",
    "nav_tipo": "Tipo 1",
    "nav_descripcion": "Descripción de la nave",
    "usu_nombreCompleto": "John Doe"
}
```
---

### 👥 getAllUsuarios
Recupera la lista de todos los usuarios de la base de datos.

**GET**  `/API/perfilAPI.php?accion=getAllUsuarios`


**Parámetros:**

- `conn`: Conexión a la base de datos.

**Descripción:**
1. Realiza una consulta para obtener todos los registros de la tabla `usuarios`.
2. Almacena cada registro en un array `$usuarios`.
3. Devuelve un JSON con los datos de todos los usuarios.

**Resultado:**
Devuelve un JSON con una lista de todos los usuarios en la base de datos.

**Respuesta JSON:**
```json
[
    {
        "usu_id": 1,
        "usu_nombre": "John",
        "usu_apellido": "Doe",
        "usu_alias": "johndoe",
        "usu_genero": "M",
        "usu_biografia": "Texto de biografía.",
        "usu_imagen": "path_to_image.jpg",
        "usu_numero_empleado": "12345",
        "usu_fecha_creacion": "2022-01-01",
        "usu_contrasena": "hashedpassword",
        "usu_activo": 1,
        "rol_id": 1,
        "usu_idnave": 1
    },
    {
        "usu_id": 2,
        "usu_nombre": "Jane",
        "usu_apellido": "Doe",
        "usu_alias": "janedoe",
        "usu_genero": "F",
        "usu_biografia": "Texto de biografía.",
        "usu_imagen": "path_to_image2.jpg",
        "usu_numero_empleado": "67890",
        "usu_fecha_creacion": "2021-11-15",
        "usu_contrasena": "hashedpassword2",
        "usu_activo": 1,
        "rol_id": 2,
        "usu_idnave": 2
    }
]
```
---

### 👤 getUsuario
Recupera los datos de un usuario específico basado en su ID.

**GET**  `/API/perfilAPI.php?accion=getUsuario`


**Parámetros:**

- `conn`: Conexión a la base de datos.
- `id`: El ID del usuario, que debe pasarse como parámetro en la URL (`GET`).

**Descripción:**
1. Verifica si el parámetro `id` ha sido proporcionado en la URL. Si no se proporciona, devuelve un mensaje de error.
2. Realiza una consulta para obtener los detalles del usuario con el ID proporcionado.
3. Si el usuario existe, devuelve los detalles del usuario como un JSON. Si no se encuentra, devuelve un JSON vacío.

**Resultado:**
Devuelve un JSON con los datos del usuario correspondiente al ID proporcionado.

**Respuesta JSON:**
Si el usuario existe:
```json
{
    "usu_id": 1,
    "usu_nombre": "John",
    "usu_apellido": "Doe",
    "usu_alias": "johndoe",
    "usu_genero": "M",
    "usu_biografia": "Texto de biografía.",
    "usu_imagen": "path_to_image.jpg",
    "usu_numero_empleado": "12345",
    "usu_fecha_creacion": "2022-01-01",
    "usu_contrasena": "hashedpassword",
    "usu_activo": 1,
    "rol_id": 1,
    "usu_idnave": 1
}
```
---

### 🔑 getRoles
Recupera la lista de todos los roles disponibles en el sistema.

**GET**  `/API/perfilAPI.php?accion=getRoles`


**Parámetros:**

- `conn`: Conexión a la base de datos.

**Descripción:**
1. Realiza una consulta a la base de datos para obtener todos los roles registrados en la tabla `roles`.
2. Devuelve una lista con los roles disponibles en formato JSON.

**Resultado:**
Devuelve un JSON con los detalles de todos los roles.

**Respuesta JSON:**
```json
[
    {
        "rol_id": 1,
        "rol_nombre": "Administrador",
        "rol_descripcion": "Rol con privilegios completos."
    },
    {
        "rol_id": 2,
        "rol_nombre": "Usuario",
        "rol_descripcion": "Rol con acceso limitado."
    },
    {
        "rol_id": 3,
        "rol_nombre": "Moderador",
        "rol_descripcion": "Rol con privilegios de moderación."
    }
]
```
---

### 🚀 getNaves
Recupera la lista de todas las naves disponibles en el sistema.

**GET**  `/API/perfilAPI.php?accion=getNaves`


**Parámetros:**

- `conn`: Conexión a la base de datos.

**Descripción:**
1. Realiza una consulta a la base de datos para obtener todos los registros de la tabla `nave`.
2. Devuelve una lista con todas las naves disponibles en formato JSON.

**Resultado:**
Devuelve un JSON con los detalles de todas las naves.

**Respuesta JSON:**
```json
[
    {
        "nav_id": 1,
        "nav_nombre": "USCSS Paladio",
        "nav_tipo": "Exploración",
        "nav_descripcion": "Nave de investigación de la Weyland-Yutani."
    },
    {
        "nav_id": 2,
        "nav_nombre": "USCSS Nostromo",
        "nav_tipo": "Transporte",
        "nav_descripcion": "Nave de transporte interplanetario de la Weyland-Yutani."
    }
]
```
---

# Función `putActualizarPerfil`

**PUT**  `/API/perfilAPI.php?accion=putActualizarPerfil?usu_id=1`


### Propósito:
Actualiza los datos del perfil de un usuario, incluyendo su nombre, alias, apellido, género, biografía y, opcionalmente, su imagen de perfil.

### Parámetros del `FormData`:
- `usu_id`: ID del usuario (requerido).
- `nombre`: Nombre del usuario (requerido).
- `alias`: Alias del usuario (requerido).
- `apellido`: Apellido del usuario (requerido).
- `rol_id`: ID del rol asignado al usuario (opcional).
- `genero`: Género del usuario (opcional).
- `biografia`: Biografía del usuario (opcional).
- `imagen`: Nueva imagen de perfil (opcional).

### Flujo de la función:

1. **Validación de ID de usuario**: Si no se envía un `usu_id`, se retorna un error.
2. **Validación de campos obligatorios**: `nombre`, `alias`, y `apellido` son requeridos. Si faltan, se retorna un error.
3. **Subida de imagen**:
   - Se valida que la imagen sea de tipo `jpg`, `jpeg` o `png` y no exceda los 2MB.
   - Si es válida, se elimina la imagen anterior (si existe) y se guarda la nueva imagen.
4. **Actualización de perfil**: Se actualizan los datos del perfil del usuario en la base de datos.
5. **Respuesta**: Se retorna un mensaje de éxito o error según el resultado de la operación.

### Código:

```php
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

    // Actualizar los datos del perfil
    $sql = "UPDATE usuarios SET usu_nombre = ?, usu_alias = ?, usu_apellido = ?, rol_id = ?, usu_genero = ?, usu_biografia = ? WHERE usu_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssiisi", $nombre, $alias, $apellido, $rol_id, $genero, $biografia, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo json_encode(["mensaje" => "Perfil actualizado correctamente."]);
}
```
---

### 🔑 REGISTRO & INICIO DE SESION DE TRIPULANTES
### 🔑 registroUsuario
Registra un nuevo usuario en el sistema.

**Método:** `POST`

**Parámetros:**
- `nombre` (string): Nombre del usuario.
- `apellido` (string): Apellido del usuario.
- `alias` (string): Alias único para el usuario.
- `rol` (int): ID del rol del usuario.
- `genero` (string): Género del usuario.
- `biografia` (string): Biografía del usuario.
- `contrasena` (string): Contraseña del usuario.
- `contrasenaRepetida` (string): Confirmación de la contraseña del usuario.
- `fotoPerfil` (archivo): Foto de perfil del usuario (opcional).

**Descripción:**
1. Se valida que todos los campos requeridos estén presentes.
2. Se verifica que las contraseñas coincidan.
3. Se comprueba que el alias del usuario sea único.
4. Se procesa la imagen de perfil (si se proporciona) y se guarda.
5. Se encripta la contraseña y se inserta el nuevo usuario en la base de datos.
6. Se genera un token único para el usuario.
7. Se inicializa la sesión del usuario con sus datos.
8. Se devuelve una respuesta JSON con el éxito de la operación, el token y los detalles del usuario.

**Respuesta JSON:**

```json
{
    "success": true,
    "token": "token_generado_aleatoriamente",
    "usuario": {
        "id": 1,
        "alias": "usuario_alias",
        "nombre": "Juan",
        "apellido": "Pérez",
        "imagen": "fotoPerfil_12345.jpg",
        "rol": 2
    }
}
```
---
### 🔑 loginUsuario
Inicia sesión en el sistema utilizando alias y contraseña.

**Método:** `POST`

**Parámetros:**
- `usu_alias` (string): Alias del usuario.
- `usu_contrasena` (string): Contraseña del usuario.

**Descripción:**
1. Se valida que se proporcionen tanto el alias como la contraseña.
2. Se realiza una consulta en la base de datos para verificar que el alias exista y esté activo.
3. Si el alias no existe o está desactivado, se devuelve un error con una redirección.
4. Si la contraseña no coincide con la almacenada en la base de datos, se devuelve un error con una redirección.
5. Si las credenciales son correctas, se genera un token de sesión único.
6. Se guarda la información del usuario y el token en la sesión.
7. Se devuelve una respuesta JSON con el éxito de la operación y el token generado.

**Respuesta JSON:**

```json
{
    "success": true,
    "token": "token_generado_aleatoriamente"
}
```
---