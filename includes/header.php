<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'EventHub - Sistema de Gestión de Eventos'; ?></title>

  <!-- Bootstrap CSS desde CDN -->
  <link rel="stylesheet" href="<?= asset('assets/css/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/bootstrap-icons.css') ?>">
  
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<?php if (isset($_SESSION['user_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="<?= url('dashboard') ?>">
      <i class="bi bi-calendar-event"></i> EventHub
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?= ($_GET['page'] ?? '') === 'dashboard' ? 'active' : '' ?>" href="<?= url('dashboard') ?>">
            <i class="bi bi-house-door"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($_GET['page'] ?? '') === 'register' ? 'active' : '' ?>" href="<?= url('register') ?>">
            <i class="bi bi-ticket-perforated"></i> Registrar Participante
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="<?= url('dashboard') ?>">
              <i class="bi bi-speedometer2"></i> Mi Dashboard
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="<?= url('logout') ?>">
              <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
<?php else: ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="<?= url('login') ?>">
      <i class="bi bi-calendar-event"></i> EventHub
    </a>
    <div class="ms-auto">
      <a href="<?= url('login') ?>" class="btn btn-outline-light btn-sm me-2">
        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
      </a>
      <a href="<?= url('register') ?>" class="btn btn-light btn-sm">
        <i class="bi bi-person-plus"></i> Registrarse
      </a>
    </div>
  </div>
</nav>
<?php endif; ?>

<main>
