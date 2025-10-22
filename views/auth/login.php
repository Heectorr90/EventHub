<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="text-center mb-4">🔐 Iniciar sesión</h3>

          <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success text-center">
              ✅ Registro exitoso. Ahora puedes iniciar sesión.
            </div>
          <?php endif; ?>

          <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="POST">
            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Contraseña</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
          </form>

          <div class="text-center mt-3">
            <a href="<?= url('register') ?>">¿No tienes cuenta? Regístrate</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
