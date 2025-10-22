<?php
global $pdo;

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Models/Event.php';
require_once __DIR__ . '/../../app/Models/Participant.php';

$event_id = $_GET['event_id'] ?? 0;

$eventModel = new Event($pdo);
$participantModel = new Participant($pdo);

$event = $eventModel->find($event_id);

// Verificar que el evento exista y pertenezca al usuario
if (!$event || $event['user_id'] != $_SESSION['user_id']) {
    header("Location: /EventHub/views/dashboard.php");
    exit;
}

$participants = $participantModel->getByEvent($event_id);
$totalParticipants = count($participants);

include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>👥 Participantes del Evento</h2>
            <h4 class="text-muted"><?= htmlspecialchars($event['title']) ?></h4>
        </div>
        <div>
            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addParticipantModal">
                <i class="bi bi-person-plus"></i> Agregar participante
            </button>
            <a href="<?= url('dashboard') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Mensajes -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            ✅ <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Información del evento -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary"><?= $totalParticipants ?></h3>
                    <p class="mb-0">Inscritos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success"><?= $event['capacity'] ?></h3>
                    <p class="mb-0">Capacidad total</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning"><?= max(0, $event['capacity'] - $totalParticipants) ?></h3>
                    <p class="mb-0">Cupos disponibles</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del evento -->
    <div class="card mb-4">
        <div class="card-body">
            <p><strong>📅 Fecha:</strong> <?= date('d/m/Y H:i', strtotime($event['date'])) ?></p>
            <p class="mb-0"><strong>📝 Descripción:</strong> <?= htmlspecialchars($event['description']) ?></p>
        </div>
    </div>

    <!-- Lista de participantes -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Lista de Participantes Registrados</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($participants)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-person-x" style="font-size: 4rem; color: #ccc;"></i>
                    <p class="mt-3 text-muted">No hay participantes registrados todavía.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Fecha de Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 1; foreach ($participants as $participant): ?>
                                <tr>
                                    <td><?= $counter++ ?></td>
                                    <td>
                                        <i class="bi bi-person-circle text-primary"></i>
                                        <?= htmlspecialchars($participant['name']) ?>
                                    </td>
                                    <td><?= htmlspecialchars($participant['email']) ?></td>
                                    <td>
                                        <i class="bi bi-clock"></i>
                                        <?= date('d/m/Y H:i', strtotime($participant['registered_at'])) ?>
                                    </td>
                                    <td>
                                        <a href="/EventHub/public/index.php?page=remove_participant&event_id=<?= $event_id ?>&user_id=<?= $participant['user_id'] ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('¿Eliminar a este participante?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Botón de exportar (opcional) -->
    <?php if (!empty($participants)): ?>
        <div class="mt-3 text-end">
            <button class="btn btn-success" onclick="window.print()">
                <i class="bi bi-printer"></i> Imprimir lista
            </button>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/add_participant_modal.php'; ?>
<?php include __DIR__ . '/../../includes/footer.php'; ?>