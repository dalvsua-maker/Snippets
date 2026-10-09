# Snippets
# 💻 Snippets Lab

¡Bienvenido a **Snippets Lab**! Una plataforma web moderna e interactiva dedicada a la recolección, exhibición y catálogo de secciones **Hero** de alto impacto visual. Desarrollada de forma nativa utilizando código limpio, ligero y con rendimiento optimizado sin dependencias pesadas.

El proyecto cuenta con estilos avanzados en **CSS3**, interactividad enriquecida con **jQuery** y un entorno de desarrollo contenedorizado con **Docker Compose**. La rama `BBDD` añade persistencia de datos en **MongoDB Atlas** (base de datos en la nube) mediante el driver oficial de PHP. El procesamiento del formulario con **PHP 8.2** existe en las ramas `main`, `php` y `BBDD`; la rama `js` valida íntegramente en el navegador (ver [Ramas del Proyecto](#-ramas-del-proyecto-validación-y-persistencia)).

---

## 🚀 Características Principales

*   **Catálogo de Heros Interactivos:** Colección de componentes visuales adaptables:
    *   *Fluid Design:* Gradientes dinámicos tipo malla interactiva de fondo (Mesh).
    *   *Cinematic Reveal:* Efecto de desplazamiento que escala y desvanece capas de texto mediante scroll.
    *   *Video Mask Hero:* Enmascaramiento de vídeo nativo a través de tipografías pesadas.
    *   *Typing Text Effect:* Animaciones de escritura automatizada por pasos.
    *   *Text Reveal Staggered:* Transición escalonada de títulos con recortes de máscara (`clip-path`).
*   **Formulario de Solicitudes Integrado:** Sistema donde el usuario puede seleccionar un Hero y enviar una propuesta técnica a medida.
*   **Persistencia de Datos en MongoDB Atlas:** Al enviar el formulario (en la rama `BBDD`), los datos se validan en el servidor y se insertan en la colección `propuestas` de MongoDB Atlas mediante el driver oficial `mongodb/mongodb`.
*   **Gestión de la Base de Datos desde Atlas:** Los registros se consultan y administran desde el *Data Explorer* de MongoDB Atlas, sin instalar ni mantener un servidor de base de datos local.
*   **Validación del Formulario en Varias Variantes:** La rama `main` combina la validación nativa del navegador (HTML5) con una comprobación del nombre en PHP. Las ramas `php` y `js` llevan toda la validación al servidor o al navegador, respectivamente. La rama `BBDD` valida en el servidor y persiste los datos en MongoDB Atlas. En `php`, `js` y `BBDD`, los errores se muestran en `index.html` y no se avanza al resumen si falla alguna validación.
*   **Diseño Premium y Responsivo:** Paleta de colores futurista oscura ("Cyber-Blue") adaptada exhaustivamente para resoluciones móviles, tablets y ordenadores con componentes interactivos avanzados (menú móvil nativo y botones magnéticos).

---

## 🛠️ Tech Stack

El proyecto utiliza un set de tecnologías nativas para maximizar la velocidad de carga y retención visual:

*   **Frontend:** HTML5, CSS3 (Variables Globales, Flexbox, Grids), JavaScript, jQuery (v3+).
*   **Backend:** PHP 8.2 (en la rama `BBDD`, conexión a MongoDB con la extensión `mongodb` y la librería `mongodb/mongodb`, gestionada con Composer).
*   **Base de Datos (rama `BBDD`):** MongoDB Atlas (cluster en la nube).
*   **Herramientas de BBDD (rama `BBDD`):** Atlas Data Explorer.
*   **Infraestructura:** Docker & Docker Compose.

---

## 📦 Estructura del Proyecto

```text
├── .git/                  # Historial de Git
├── docker-compose.yml     # Configuración del entorno Docker
├── dockerfile             # (rama BBDD) Imagen personalizada del servidor PHP Apache: extensión mongodb + Composer
├── composer.json          # (rama BBDD) Dependencias de PHP: mongodb/mongodb
├── .env.example           # (rama BBDD) Plantilla de variables de entorno (conexión a Atlas)
├── .gitignore             # (ramas main y BBDD) Excluye .env y vendor/ del repositorio
├── index.html             # Landing page principal, catálogo y formulario
├── resumen.php            # (ramas main, php y BBDD) Validación en servidor, guardado e interfaz de resumen
├── resumen.html           # (rama js) Página de resumen estática
├── main.css               # Estilos globales y responsive del sitio
├── script.js              # Lógica jQuery, efectos, animaciones y envío del formulario
├── README.md              # Documentación del proyecto
└── [imágenes/assets]      # Logotipos y recursos multimedia del sitio
```

> `resumen.php` existe en las ramas `main`, `php` y `BBDD`, mientras que `resumen.html` se utiliza únicamente en la rama `js`. El resto de archivos existe en todas las ramas, adaptando la lógica en cada una.

---

## ⚙️ Requisitos Previos

Asegúrate de tener instalados los siguientes componentes en tu entorno local:

1.  **Docker Desktop** (versión 20.10 o superior; en Windows requiere WSL 2)
2.  **Docker Compose** (versión 1.29 o superior)
3.  **Git**
4.  **Una cuenta de MongoDB Atlas** con un cluster (el plan gratuito M0 es suficiente). Solo es necesaria para la rama `BBDD`.

---

## 🚀 Instalación y Despliegue Local

Sigue estos pasos para clonar el repositorio e iniciar el entorno. Los pasos 2 y 3 solo son necesarios en la rama `BBDD`, que es la que guarda los datos en MongoDB Atlas.

### 1. Clonar el repositorio
```bash
git clone https://github.com/dalvsua-maker/Snippets.git
cd Snippets
git checkout BBDD
```

### 2. Preparar MongoDB Atlas (rama `BBDD`)
1.  Crea un cluster en [MongoDB Atlas](https://www.mongodb.com/atlas) (el plan gratuito M0 es suficiente).
2.  En **Database Access**, crea un usuario de base de datos con permisos de lectura y escritura. Es distinto de tu cuenta de Atlas.
3.  En **Network Access**, permite tu dirección IP. Sin este paso la conexión falla aunque la cadena sea correcta.
4.  En **Database → Connect → Drivers**, copia la cadena de conexión `mongodb+srv://...`.

No hace falta crear la base de datos ni la colección: Atlas las crea al guardar el primer registro.

### 3. Configurar las variables de entorno (rama `BBDD`)
Copia la plantilla y rellénala con tu cadena de conexión. Sustituye `USUARIO` y `CONTRASEÑA` por el usuario de base de datos y su contraseña (la cadena que te da Atlas trae `<db_password>` en el lugar de la contraseña):

```bash
cp .env.example .env
```
En PowerShell: `Copy-Item .env.example .env`

```env
MONGODB_URI=mongodb+srv://USUARIO:CONTRASEÑA@CLUSTER.xxxxx.mongodb.net/?retryWrites=true&w=majority
MONGODB_DB=snippets_db
```

### 4. Levantar el entorno con Docker
En las ramas `main`, `php` y `js` basta con:

```bash
docker compose up -d
```

En la rama `BBDD` hay que construir la imagen. La primera vez tarda unos minutos, porque se compila la extensión de MongoDB para PHP; las siguientes veces aprovecha la caché de Docker:

```bash
docker compose up -d --build
```

Si cambias de rama y vuelves a `BBDD`, ejecuta antes `docker compose down` y después `docker compose up -d --build --remove-orphans`, para que Docker use la imagen correcta.

### 5. Acceder a la aplicación
Una vez desplegado el contenedor, abre tu navegador en:

*   🌐 **Aplicación Web (PHP):** 👉 **[http://localhost:8080](http://localhost:8080)**

Para comprobar que los datos se guardan, envía el formulario y consulta en Atlas **Browse Collections → `snippets_db` → `propuestas`**.

---

## 🐳 Detalles de la Infraestructura Docker

En las ramas `main`, `php` y `js`, `docker-compose.yml` define un único servicio, `mi_servidor_php`, con la imagen oficial `php:8.2-apache`, el puerto `8080:80` y el directorio del proyecto montado en `/var/www/html/`. Ninguna de estas ramas usa base de datos.

En la rama `BBDD`, el entorno se orquesta mediante `docker-compose.yml`, que también define un único servicio, porque la base de datos no se ejecuta en local sino en MongoDB Atlas:

1.  **`mi_servidor_php` (`php-apache`):**
    *   **Imagen:** construida con el `dockerfile` a partir de `php:8.2-apache`. Instala la extensión `mongodb` con PECL y las dependencias de Composer.
    *   **Dependencias de Composer:** se instalan en `/opt/app` y no en `/var/www/html`, porque ese directorio se sustituye por el volumen del proyecto y ocultaría la carpeta `vendor`.
    *   **Puerto mapeado:** `8080:80` (Redirecciona las peticiones locales al puerto 80 interno).
    *   **Volúmenes:** Enlace directo del directorio local (`./`) a `/var/www/html/`.
    *   **Variables de entorno:** `MONGODB_URI` (cadena de conexión de Atlas) y `MONGODB_DB` (nombre de la base de datos, `snippets_db` por defecto), leídas del archivo `.env` mediante `env_file`.

---

## 🗄️ Modelo de Datos (MongoDB)

En la rama `BBDD`, las propuestas enviadas a través del formulario se guardan en la colección **`propuestas`** de la base de datos `snippets_db` de MongoDB Atlas. Atlas crea la base de datos y la colección automáticamente al insertar el primer documento, por lo que no hay ningún script de inicialización. Cada propuesta es un documento con esta forma:

```js
{
  _id: ObjectId("..."),            // generado por MongoDB
  hero: "Fluid Design",
  nombre: "Ana García",
  email: "ana@ejemplo.es",
  motivo: "Quiero usarlo en la web de mi proyecto.",
  created_at: ISODate("2026-10-09T00:44:00Z")
}
```

---

## 📝 Detalle de Lógica y Procesamiento

1.  **Lógica común del cliente (`script.js`):** Gestiona los efectos cinemáticos de scroll, las animaciones por intersección de pantalla (`IntersectionObserver`), el desplazamiento suave por anclas, el menú móvil, el botón magnético del header, el posicionamiento animado de los labels de los inputs y la carga dinámica de las opciones del desplegable de heros a partir de los títulos del catálogo.
2.  **Procesamiento del formulario:** depende de la rama activa.
    *   **Rama `main`:** el navegador valida con los atributos HTML5 del formulario (`required`, `type="email"` y `pattern`); si todo pasa, `script.js` envía los datos por AJAX a `resumen.php`, que solo comprueba el nombre, los guarda en la sesión y los muestra en el resumen.
    *   **Rama `php`:** `script.js` envía los datos por AJAX a `resumen.php`, que los limpia con `trim()`, aplica las validaciones (expresiones regulares, `FILTER_VALIDATE_EMAIL`, longitudes), guarda los datos válidos en la sesión y los muestra escapados con `htmlspecialchars()`. Los errores devueltos se pintan en `index.html`.
    *   **Rama `js`:** `script.js` valida el formulario en el navegador, guarda los datos válidos en `localStorage` y `resumen.html` los muestra al cargar. Los errores se pintan en `index.html`.
    *   **Rama `BBDD`:** `script.js` envía los datos por AJAX a `resumen.php`. PHP valida los campos en el servidor (no vacíos, formato del nombre y del email) y ejecuta la inserción en la colección `propuestas` de MongoDB Atlas (`insertOne` del driver oficial) antes de redirigir al resumen.

---

## 🌿 Ramas del Proyecto: Validación y Persistencia

El formulario de `index.html` se ha implementado de distintas formas en cada rama de Git: `main` (versión base con validación mixta), las ramas `php` y `js` (que concentran la validación en servidor o cliente), y la rama `BBDD` (que añade persistencia de datos en MongoDB Atlas). En `php`, `js` y `BBDD` se cumple la misma regla: **los mensajes de error se muestran en `index.html` y no se puede avanzar al resumen si alguna validación falla**.

```bash
git checkout main  # validación mixta: HTML5 en el navegador + nombre en PHP
git checkout php   # validación completa en el servidor (PHP)
git checkout js    # validación completa en el navegador (JavaScript)
git checkout BBDD  # validación en PHP + persistencia en MongoDB Atlas + Docker con Composer
```

### Reglas de validación (ramas `php` y `js`, idénticas)

| Campo | Regla |
| --- | --- |
| Hero elegido | Obligatorio. Solo letras y espacios (máx. 60 caracteres). |
| Nombre | Obligatorio. Solo letras (con acentos y ñ) y espacios, entre 2 y 60 caracteres. |
| Email | Obligatorio, con formato válido y terminado en `.es` o `.com`. |
| Motivo | Obligatorio, entre 10 y 500 caracteres. |

### Reglas de validación de la rama `BBDD`

La rama `BBDD` aplica reglas más sencillas, sin límites de longitud:

| Campo | Regla |
| --- | --- |
| Hero elegido | Obligatorio (no puede estar vacío). |
| Nombre | Obligatorio. Solo letras (con acentos y ñ) y espacios. |
| Email | Obligatorio, debe contener `@` y terminar en `.es` o `.com`. |
| Motivo | Obligatorio (no puede estar vacío). |

### Validación del navegador (HTML5)

*   En las ramas `php` y `js` el `<form>` lleva el atributo `novalidate`: los campos conservan `required`, `type="email"` y `pattern`, pero el navegador no muestra sus propios avisos y toda la validación depende de la lógica de la rama.
*   En la rama `BBDD` el `<form>` no lleva `novalidate`, pero los campos no tienen `required` ni `pattern` y el email es `type="text"`. El navegador no valida nada y toda la validación la hace PHP.
*   En la rama `main` el `<form>` no lleva `novalidate` y los campos conservan sus atributos HTML5, por lo que el navegador valida antes de enviar.

### 🌳 Rama `main`: validación mixta (HTML5 + PHP)

**Archivos:** `index.html`, `resumen.php`, `script.js`, `main.css`.

**Flujo:**

1.  El `<form>` no lleva `novalidate`, así que al pulsar **Send** el **navegador valida** con los atributos HTML5: los cuatro campos son `required`, el email es `type="email"` y además un `pattern` exige que termine en `.es` o `.com`. Si algo falla, el navegador muestra su propio aviso y el envío no llega a ejecutarse.
2.  Si el navegador da el visto bueno, `script.js` intercepta el envío (`preventDefault`) y manda los datos por **AJAX (POST)** a `resumen.php`.
3.  `resumen.php` solo valida el **nombre** (obligatorio y solo letras y espacios). Si falla, responde en JSON con `status: "error"` y `script.js` muestra el mensaje en `index.html`, encima del botón.
4.  Si el nombre es válido, PHP guarda los cuatro campos en `$_SESSION['datosProyecto']` y responde `status: "success"`. `script.js` guarda además una copia en `localStorage` y redirige a `resumen.php`.
5.  `resumen.php` (petición GET) lee la sesión y muestra el resumen. Si se entra directamente sin datos en la sesión, aparece el mensaje "Aún no se han enviado datos.".

**Detalles a tener en cuenta:**

*   Solo el nombre se valida en el servidor. El hero, el email (incluido el requisito de `.es`/`.com`) y el motivo dependen únicamente de la validación del navegador.
*   Los avisos de los campos vacíos o del email son los nativos del navegador; solo el error del nombre aparece como caja roja en `index.html`.
*   El resumen se muestra a partir de la sesión de PHP; la copia en `localStorage` se guarda pero no se usa para pintar el resumen.
*   Requiere un servidor con PHP y sesiones.

### 🐘 Rama `php`: validación solo con PHP

**Archivos:** `index.html`, `resumen.php`, `script.js`, `main.css`.

**Flujo:**

1.  El usuario pulsa **Send** en `index.html`. `script.js` intercepta el envío (`preventDefault`) y manda los datos por **AJAX (POST)** a `resumen.php`. JavaScript **no valida nada**.
2.  `resumen.php` recoge los datos, los limpia con `trim()` y aplica todas las validaciones (expresiones regulares, `filter_var` con `FILTER_VALIDATE_EMAIL` y comprobaciones de longitud).
3.  **Si hay errores**, PHP elimina de la sesión cualquier dato anterior y responde en JSON con `status: "error"` y un mensaje por campo. `script.js` los pinta en `index.html`, encima del botón, y marca cada campo con `has-error`. **No se redirige**.
4.  **Si todo es correcto**, PHP guarda los datos en `$_SESSION['datosProyecto']` y responde `status: "success"`. Solo entonces `script.js` redirige a `resumen.php`.
5.  `resumen.php` (petición GET) lee la sesión y muestra el resumen, escapando todo con `htmlspecialchars()`. Si se entra directamente sin datos válidos, aparece el mensaje "Aún no se han enviado datos.".

**Detalles a tener en cuenta:**

*   Requiere un servidor con PHP y sesiones.
*   Si el envío no llega por AJAX (JavaScript desactivado), PHP redirige a `index.html#contacto` o a `resumen.php`.
*   `script.js` guarda además una copia de los datos en `localStorage` tras un envío correcto, pero no se usa para pintar el resumen, que sale de la sesión de PHP.

### 🟨 Rama `js`: validación solo con JavaScript

**Archivos:** `index.html`, `resumen.html`, `script.js`, `main.css`. **Desaparece `resumen.php`**, sustituido por `resumen.html`.

**Flujo:**

1.  El usuario pulsa **Send** en `index.html`. `script.js` intercepta el envío (`preventDefault`) y ejecuta todas las validaciones en el navegador.
2.  **Si hay errores**, se muestran en `index.html`, encima del botón, con la caja roja y `has-error` en cada campo. Además se borran los datos antiguos de `localStorage`. **No se redirige**.
3.  **Si todo es correcto**, los datos se guardan en `localStorage` (clave `datosProyecto`) y se redirige a `resumen.html`.
4.  `resumen.html` es una página estática: al cargar, `script.js` lee `localStorage` y rellena el resumen con `.text()` (sin inyectar HTML). Si no hay datos, se muestra "Aún no se han enviado datos.".

**Detalles a tener en cuenta:**

*   No necesita PHP: funciona con cualquier servidor estático.
*   Los datos viven en el navegador, por lo que `resumen.html` solo muestra el envío hecho desde ese mismo navegador.

### 🗄️ Rama `BBDD`: validación en PHP y persistencia en MongoDB Atlas

**Archivos:** `index.html`, `resumen.php`, `script.js`, `main.css`, `docker-compose.yml`, `dockerfile`, `composer.json`, `.env.example`, `.gitignore`.

**Flujo:**

1.  El usuario envía el formulario en `index.html`. `script.js` intercepta la petición (`preventDefault`) y manda los datos por **AJAX (POST)** a `resumen.php`. JavaScript no valida nada.
2.  `resumen.php` limpia los datos con `trim()` y comprueba en el servidor que ninguno de los cuatro campos esté vacío, que el nombre solo tenga letras y espacios y que el email contenga `@` y termine en `.es` o `.com`. No comprueba longitudes.
3.  **Si hay errores**, responde en JSON con `status: "error"` y todos los mensajes unidos por saltos de línea. `script.js` los muestra en una caja roja en `index.html`, encima del botón, sin marcar los campos. **No se redirige**.
4.  **Si todo es correcto**, `resumen.php` se conecta a MongoDB Atlas con la cadena `MONGODB_URI` (leída del entorno) e inserta un documento en la colección `propuestas`. Si Atlas no responde en 5 segundos o falla el guardado, devuelve un error en JSON (`Error al guardar en la base de datos: ...`) que se muestra en `index.html`.
5.  Guarda una copia de confirmación en la sesión de PHP y responde con `status: "success"`.
6.  `script.js` redirige a `resumen.php`, que lee los datos de la sesión para mostrarlos en la pantalla de confirmación. Si se entra directamente sin datos, aparece el mensaje "Aún no se han enviado datos.".

**Detalles a tener en cuenta:**

*   Requiere un archivo `.env` con `MONGODB_URI`. Está en `.gitignore`: las credenciales nunca se suben al repositorio.
*   En Atlas hay que permitir la IP desde la que se ejecuta la aplicación (**Network Access**) y usar un usuario de base de datos con permisos de lectura y escritura.
*   MongoDB solo almacena las propuestas; el resumen que se muestra tras enviar sigue leyéndose de la sesión de PHP.
*   `script.js` guarda además una copia en `localStorage` tras un envío correcto, pero no se usa para pintar el resumen.
*   Esta rama ya no usa MySQL, phpMyAdmin ni `init.sql`.

---

### Comparativa rápida

| | Rama `main` | Rama `php` | Rama `js` | Rama `BBDD` |
| --- | --- | --- | --- | --- |
| Dónde se valida | Navegador (HTML5) y servidor (solo el nombre) | Servidor (`resumen.php`) | Navegador (`script.js`) | Servidor (`resumen.php`) |
| Campos validados en el servidor | Nombre | Todos | Ninguno | Todos (sin límites de longitud) |
| Validación del navegador (HTML5) | Sí (`required`, `type="email"`, `pattern`) | Desactivada (`novalidate`) | Desactivada (`novalidate`) | Sin atributos HTML5 (no valida) |
| Persistencia en BBDD | No | No | No | Sí (MongoDB Atlas, colección `propuestas`) |
| Mensajes de error | Avisos nativos y caja roja para nombre | Caja roja con un mensaje por campo | Caja roja con un mensaje por campo | Caja roja con un mensaje por línea, sin marcar los campos |
| Página de resumen | `resumen.php` | `resumen.php` | `resumen.html` | `resumen.php` |
| Datos entre páginas | Sesión de PHP | Sesión de PHP | `localStorage` | Sesión de PHP + MongoDB Atlas |
| Infraestructura Docker | Básica (Apache + PHP) | Básica (Apache + PHP) | Opcional / Estática | PHP Apache con Composer; base de datos externa en Atlas |

---

## 📄 Licencia

Este proyecto está bajo la **Licencia MIT**. Siéntete libre de explorar, adaptar y reutilizar los fragmentos de código en tus proyectos personales o comerciales.