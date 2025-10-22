<?php
global $pdo;
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Controllers/EventController.php';
require_once __DIR__ . '/../app/Models/Participant.php';

// Redirigir si no hay sesión activa
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit;
}

$userModel = new User($pdo);
$user = $userModel->findById($_SESSION['user_id']);

// 🔹 Delegamos TODA la lógica de eventos al controlador
$events = EventController::handleRequest($pdo);
$participantModel = new Participant($pdo);

// Incluir header y modales
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/events/create.php';
include __DIR__ . '/events/edit.php';
?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>👋 Bienvenido, <?= htmlspecialchars($user['name']) ?></h2>
    <a href="index.php?page=logout" class="btn btn-outline-danger btn-sm">
      <i class="bi bi-box-arrow-right"></i> Cerrar sesión
    </a>
  </div>

  <!-- Información del usuario -->
  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">👤 Tu perfil</h5>
          <p><strong>Nombre:</strong> <?= htmlspecialchars($user['name']) ?></p>
          <p class="mb-0"><strong>Correo:</strong> <?= htmlspecialchars($user['email']) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-sm bg-primary text-white">
        <div class="card-body">
          <h5 class="card-title">📊 Estadísticas</h5>
          <div class="row text-center">
            <div class="col-6">
              <h3><?= count($events) ?></h3>
              <small>Eventos creados</small>
            </div>
            <div class="col-6">
              <h3><?= count($participantModel->getEventsByUser($_SESSION['user_id'])) ?></h3>
              <small>Registros activos</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Sección de eventos -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>🎟️ Mis eventos</h3>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createEventModal">
      + Nuevo evento
    </button>
  </div>

  <!-- Mensajes de acción -->
  <?php if (isset($_GET['event_created'])): ?>
    <div class="alert alert-success alert-dismissible fade show text-center">
      ✅ Evento creado con éxito.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif (isset($_GET['event_updated'])): ?>
    <div class="alert alert-info alert-dismissible fade show text-center">
      ✏️ Evento actualizado correctamente.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif (isset($_GET['event_deleted'])): ?>
    <div class="alert alert-danger alert-dismissible fade show text-center">
      🗑️ Evento eliminado.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Tabla de eventos -->
  <?php if (empty($events)): ?>
    <div class="text-center py-5">
      <i class="bi bi-calendar-x" style="font-size: 4rem; color: #ccc;"></i>
      <p class="mt-3 text-muted">No has creado ningún evento todavía.</p>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createEventModal">
        Crear mi primer evento
      </button>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>Título</th>
            <th>Fecha</th>
            <th>Capacidad</th>
            <th>Participantes</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($events as $event): 
            $participantCount = $participantModel->countByEvent($event['id']);
          ?>
            <tr>
              <td><?= htmlspecialchars($event['title']) ?></td>
              <td><?= htmlspecialchars($event['date']) ?></td>
              <td><?= htmlspecialchars($event['capacity']) ?> personas</td>
              <td>
                <span class="badge bg-info text-dark">
                  <?= $participantCount ?> inscritos
                </span>
              </td>
              <td>
                <a href="/EventHub/public/index.php?page=view_participants&event_id=<?= $event['id'] ?>" 
                  class="btn btn-info btn-sm" 
                  title="Ver participantes">
                  <i class="bi bi-people-fill"></i> Ver participantes
                </a>


                <button 
                  class="btn btn-warning btn-sm edit-btn"
                  data-id="<?= $event['id'] ?>"
                  data-title="<?= htmlspecialchars($event['title']) ?>"
                  data-description="<?= htmlspecialchars($event['description']) ?>"
                  data-date="<?= $event['date'] ?>"
                  data-capacity="<?= $event['capacity'] ?>"
                  data-bs-toggle="modal" 
                  data-bs-target="#editEventModal">
                  <i class="bi bi-pencil">Editar</i>
                </button>

                <form action="" method="POST" class="d-inline">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $event['id'] ?>">
                  <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este evento?')">
                    <i class="bi bi-trash">Eliminar</i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- Script para rellenar el modal de edición -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".edit-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      document.getElementById("edit-id").value = btn.dataset.id;
      document.getElementById("edit-title").value = btn.dataset.title;
      document.getElementById("edit-description").value = btn.dataset.description;
      document.getElementById("edit-date").value = btn.dataset.date;
      document.getElementById("edit-capacity").value = btn.dataset.capacity;
    });
  });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>