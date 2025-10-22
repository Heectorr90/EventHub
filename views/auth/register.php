<?php include __DIR__ . '/../../includes/header.php'; ?>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <?php if (isset($_SESSION['user_id'])): ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
          <a href="<?= url('dashboard') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
          </a>
        </div>
      <?php endif; ?>
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="text-center mb-4"> <?= isset($_SESSION['user_id']) ? '👥 Registrar Participante' : '📝 Crear cuenta' ?></h3>
          <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>
          
          <form method="POST">
            <div class="mb-3">
              <label for="name" class="form-label">Nombre completo</label>
              <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Contraseña</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Registrarse</button>
          </form>
          <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="text-center mt-3">
              <a href="<?= url('login') ?>">¿Ya tienes cuenta? Inicia sesión</a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
