<?php
// 1. Iniciar la sesión para recordar datos entre peticiones
session_start();

// 2. SI LA PETICIÓN ES POST (Viene desde AJAX para validar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Establecer cabecera JSON
    header('Content-Type: application/json');

    $heroElegido = trim($_POST['heroElegido'] ?? '');
    $nombre      = trim($_POST['nombre'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $motivo      = trim($_POST['motivo'] ?? '');

    $errores = [];

    // Validar Hero
    if (empty($heroElegido)) {
        $errores[] = 'Debes seleccionar un hero de la lista.';
    }

    // Validar nombre en PHP
    $regexNombre = "/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/";
    if (empty($nombre) || !preg_match($regexNombre, $nombre)) {
        $errores[] = 'El nombre no es válido. Solo se permiten letras y espacios.';
    }

    // Validar Email (solo en servidor)
    $regexEmail = "/.*@.*\.(es|com)$/";
    if (empty($email) || !preg_match($regexEmail, $email)) {
        $errores[] = 'El correo no es válido. Debe contener "@" y terminar en .es o .com.';
    }

    // Validar Motivo
    if (empty($motivo)) {
        $errores[] = 'Debes describir el motivo de tu elección.';
    }

    // Si hay errores, devolvemos el estado de error y la lista de mensajes unidos
    if (!empty($errores)) {
        echo json_encode([
            'status'  => 'error',
            'mensaje' => implode('<br>', $errores)
        ]);
        exit;
    }

 // Si la validación es correcta, guardamos en la base de datos MySQL
    try {
        $host = 'db'; // Nombre del servicio en docker-compose
        $db   = 'snippets_db';
        $user = 'snippets_user';
        $pass = 'snippets_password';

        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Guardar en la tabla 'propuestas'
        $stmt = $pdo->prepare("INSERT INTO propuestas (hero, nombre, email, motivo) VALUES (?, ?, ?, ?)");
        $stmt->execute([$heroElegido, $nombre, $email, $motivo]);

    } catch (PDOException $e) {
        echo json_encode([
            'status'  => 'error',
            'mensaje' => 'Error al guardar en la base de datos: ' . $e->getMessage()
        ]);
        exit;
    }

    // Guardar también en la sesión
    $_SESSION['datosProyecto'] = [
        'heroElegido' => $heroElegido,
        'nombre'      => $nombre,
        'email'       => $email,
        'motivo'      => $motivo
    ];

    echo json_encode(['status' => 'success']);
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