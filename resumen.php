<?php
// 1. Iniciar la sesión para recordar datos entre peticiones
session_start();

// Lista de errores de validación (la usa también el bloque .error-box de más abajo)
$errores = [];

// 2. SI LA PETICIÓN ES POST (Viene desde AJAX para validar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $heroElegido = trim($_POST['heroElegido'] ?? '');
    $nombre      = trim($_POST['nombre'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $motivo      = trim($_POST['motivo'] ?? '');

    $regexNombre = "/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u";

    // ---- VALIDACIONES (todas en PHP). Formato: $errores['idDelCampo'] = 'mensaje' ----

    // Hero elegido: obligatorio, solo letras y espacios
    if ($heroElegido === '') {
        $errores['heroElegido'] = 'Debes elegir un hero.';
    } elseif (!preg_match($regexNombre, $heroElegido) || mb_strlen($heroElegido) > 60) {
        $errores['heroElegido'] = 'El hero elegido no es válido.';
    }

    // Nombre: obligatorio, solo letras y espacios, entre 2 y 60 caracteres
    if ($nombre === '') {
        $errores['nombre'] = 'El nombre es obligatorio.';
    } elseif (!preg_match($regexNombre, $nombre)) {
        $errores['nombre'] = 'El nombre no es válido. Solo se permiten letras y espacios.';
    } elseif (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 60) {
        $errores['nombre'] = 'El nombre debe tener entre 2 y 60 caracteres.';
    }

    // Email: obligatorio, formato válido y terminado en .es o .com
    if ($email === '') {
        $errores['email'] = 'El correo electrónico es obligatorio.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'El correo electrónico no tiene un formato válido.';
    } elseif (!preg_match('/\.(es|com)$/i', $email)) {
        $errores['email'] = 'El correo debe terminar en .es o .com';
    }

    // Motivo: obligatorio, entre 10 y 500 caracteres
    if ($motivo === '') {
        $errores['motivo'] = 'Debes describir el motivo de tu elección.';
    } elseif (mb_strlen($motivo) < 10) {
        $errores['motivo'] = 'El motivo debe tener al menos 10 caracteres.';
    } elseif (mb_strlen($motivo) > 500) {
        $errores['motivo'] = 'El motivo no puede superar los 500 caracteres.';
    }

    // ¿Petición AJAX? (jQuery envía la cabecera X-Requested-With)
    $esAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

    // Si hay errores: se descartan datos antiguos de la sesión y NO se avanza al resumen
    if (!empty($errores)) {
        unset($_SESSION['datosProyecto']);

        if ($esAjax) {
            // Establecer cabecera JSON
            header('Content-Type: application/json');
            echo json_encode([
                'status'  => 'error',
                'mensaje' => 'Revisa los campos marcados antes de enviar.',
                'errores' => $errores
            ]);
        } else {
            // Envío sin JavaScript: se vuelve al formulario de index
            header('Location: index.html#contacto');
        }
        exit;
    }

    // Si la validación es correcta, guardamos los datos en la SESIÓN
    $_SESSION['datosProyecto'] = [
        'heroElegido' => $heroElegido,
        'nombre'      => $nombre,
        'email'       => $email,
        'motivo'      => $motivo
    ];

    if ($esAjax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
    } else {
        header('Location: resumen.php');
    }
    exit;
}

// 3. SI LA PETICIÓN ES GET (Cuando se abre resumen.php en el navegador)
// Recuperamos los datos de la sesión (o un arreglo vacío si se entra directamente)
$datos = $_SESSION['datosProyecto'] ?? [];

$heroElegido = $datos['heroElegido'] ?? 'No especificado';
$nombre      = $datos['nombre'] ?? 'No especificado';
$email       = $datos['email'] ?? 'No especificado';
$motivo      = $datos['motivo'] ?? 'No especificado';

// Variable de control para saber si hay datos que mostrar
$datosValidos = !empty($datos['nombre']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resumen del Proyecto - Agencia Premium</title>
  <link rel="stylesheet" href="main.css">
</head>
<body class="page-resumen">

  <header class="main-header header-static">
    <div class="header-container">
      <a href="index.html" class="header-logo" aria-label="Volver al inicio">
        <div class="logo-wrapper">
          <img src="snippy_logo_opci_n_3_isotipo_minimalista_c_digo_beagle-removebg-preview.png" alt="Logo Plataforma" class="custom-png-logo">
        </div>
        <span class="logo-brand-text">SNIPPETS<span class="accent-dot">.</span></span>
      </a>
      <nav class="header-nav">
        <ul class="nav-list">
          <li><a href="index.html" class="nav-link">Inicio</a></li>
        </ul>
      </nav>
    </div>
  </header>
<main class="resumen-main-container">
    <div class="contact-wrap resumen-card">
      <h1>Resumen del Proyecto <small>Confirmación de los datos de tu propuesta</small></h1>

      <?php if (!empty($errores)): ?>
        <div class="error-box">
            <strong>Se encontraron errores en el envío:</strong>
            <ul>
                <?php foreach ($errores as $error) echo '<li>' . htmlspecialchars($error) . '</li>'; ?>
            </ul>
        </div>
      <?php endif; ?>

      <!-- Contenedor que PHP muestra si es POST, o que JavaScript mostrará si detecta localStorage -->
      <div id="contenedor-datos" <?php if (!$datosValidos) echo 'style="display: none;"'; ?>>
        <div class="resumen-item">
          <span class="resumen-label">Hero Seleccionado</span>
          <div class="resumen-value" id="resumen-hero"><?php echo htmlspecialchars($heroElegido); ?></div>
        </div>

        <div class="resumen-item">
          <span class="resumen-label">Nombre del Solicitante</span>
          <div class="resumen-value" id="resumen-nombre"><?php echo htmlspecialchars($nombre); ?></div>
        </div>

        <div class="resumen-item">
          <span class="resumen-label">Correo Electrónico</span>
          <div class="resumen-value" id="resumen-email"><?php echo htmlspecialchars($email); ?></div>
        </div>

        <div class="resumen-item">
          <span class="resumen-label">Motivo de la Elección</span>
          <div class="resumen-value" id="resumen-motivo"><?php echo nl2br(htmlspecialchars($motivo)); ?></div>
        </div>
      </div>

      <!-- Mensaje de estado vacío si no se envían datos y localStorage está vacío -->
      <div id="mensaje-vacio" <?php if ($datosValidos) echo 'style="display: none;"'; ?>>
        <div class="resumen-item">
            <div class="resumen-value-empty">Aún no se han enviado datos.</div>
        </div>
      </div>

      <div class="btn-container">
        <a href="index.html" class="btn-secondary">
          Volver a la Página Principal
        </a>
      </div>
    </div>
  </main>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <!--Cargar script.js -->
  <script src="script.js"></script>
</body>
</html>
