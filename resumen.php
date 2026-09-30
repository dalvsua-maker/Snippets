<?php
// Recogemos y saneamos los datos del formulario (si existen)
$heroElegido = htmlspecialchars(trim($_POST['heroElegido'] ?? ''));
$nombre      = htmlspecialchars(trim($_POST['nombre'] ?? ''));
$email       = trim($_POST['email'] ?? '');
$motivo      = htmlspecialchars(trim($_POST['motivo'] ?? ''));

$errores = [];
$datosValidos = false;

// Validación en el servidor PHP
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($heroElegido)) $errores[] = "El hero es obligatorio.";
    if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = "El formato del email no es válido.";
    if (empty($motivo)) $errores[] = "El motivo es obligatorio.";
    
    if (empty($errores)) {
        $datosValidos = true;
    }
}
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
                <?php foreach ($errores as $error) echo "<li>$error</li>"; ?>
            </ul>
        </div>
      <?php endif; ?>

      <!-- Contenedor que PHP muestra si es POST, o que JavaScript mostrará si detecta localStorage -->
      <div id="contenedor-datos" <?php if (!$datosValidos) echo 'style="display: none;"'; ?>>
        <div class="resumen-item">
          <span class="resumen-label">Hero Seleccionado</span>
          <div class="resumen-value" id="resumen-hero"><?php echo $heroElegido; ?></div>
        </div>

        <div class="resumen-item">
          <span class="resumen-label">Nombre del Solicitante</span>
          <div class="resumen-value" id="resumen-nombre"><?php echo $nombre; ?></div>
        </div>

        <div class="resumen-item">
          <span class="resumen-label">Correo Electrónico</span>
          <div class="resumen-value" id="resumen-email"><?php echo htmlspecialchars($email); ?></div>
        </div>

        <div class="resumen-item">
          <span class="resumen-label">Motivo de la Elección</span>
          <div class="resumen-value" id="resumen-motivo"><?php echo nl2br($motivo); ?></div>
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