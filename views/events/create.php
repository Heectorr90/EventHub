<!-- Modal: Crear evento -->
<div class="modal fade" id="createEventModal" tabindex="-1" aria-labelledby="createEventLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="createEventLabel">➕ Crear nuevo evento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="action" value="create_event">

          <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" class="form-control" name="title" id="title" required>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea class="form-control" name="description" id="description" rows="3"></textarea>
          </div>

          <div class="mb-3">
            <label for="date" class="form-label">Fecha y hora</label>
            <input type="datetime-local" class="form-control" name="date" id="date" required>
          </div>

          <div class="mb-3">
            <label for="capacity" class="form-label">Capacidad (personas)</label>
            <input type="number" class="form-control" name="capacity" id="capacity" min="1" value="10" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Guardar evento</button>
        </div>
      </form>
    </div>
  </div>
</div>
