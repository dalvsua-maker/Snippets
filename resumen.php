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
  <style>
    body.page-resumen { padding-top: 0 !important; min-height: 100vh; display: flex; flex-direction: column; justify-content: space-between; }
    .main-header.header-static { position: relative !important; top: auto !important; left: auto !important; box-shadow: none; }
    .resumen-main-container { flex: 1; display: flex; align-items: center; justify-content: center; padding: 80px 20px; width: 100%; }
    .resumen-card { margin: 0 auto !important; max-width: 650px; width: 100%; padding: 48px 40px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 30px rgba(59, 130, 246, 0.12); }
    .resumen-item { margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color); }
    .resumen-value { font-size: 1.15rem; color: var(--text-main); font-weight: 500; word-break: break-word; line-height: 1.5;  }
    .resumen-label { font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; display: block; font-weight: 600; }
    .resumen-value-empty { font-size: 1.15rem; color: var(--text-main); font-weight: 500; word-break: break-word; line-height: 1.5; margin-top: 20px;
    justify-self: center; }
    .btn-container { margin-top: 32px; }
    .error-box { background-color: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
  </style>
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
      <div id="contenedor-datos" style="<?php echo (!$datosValidos) ? 'display: none;' : ''; ?>">
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
          <div class="resumen-value" id="resumen-email"><?php echo $email; ?></div>
        </div>

        <div class="resumen-item">
          <span class="resumen-label">Motivo de la Elección</span>
          <div class="resumen-value" id="resumen-motivo"><?php echo nl2br($motivo); ?></div>
        </div>
      </div>

      <!-- Mensaje de estado vacío si no se envían datos y localStorage está vacío -->
      <div id="mensaje-vacio" style="<?php echo ($datosValidos) ? 'display: none;' : ''; ?>">
        <div class="resumen-item">
            <div class="resumen-value-empty" style="color: var(--text-muted);">Aún no se han enviado datos.</div>
        </div>
      </div>

      <div class="btn-container">
        <a href="index.html" class="btn-secondary" style="display: block; text-align: center; text-decoration: none; padding: 14px 28px; border-radius: 12px;">
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