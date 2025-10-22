<!-- Modal: Editar evento -->
<div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="editEventLabel">✏️ Editar evento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="action" value="edit_event">
          <input type="hidden" name="id" id="edit-id">

          <div class="mb-3">
            <label for="edit-title" class="form-label">Título</label>
            <input type="text" class="form-control" name="title" id="edit-title" required>
          </div>

          <div class="mb-3">
            <label for="edit-description" class="form-label">Descripción</label>
            <textarea class="form-control" name="description" id="edit-description" rows="3"></textarea>
          </div>

          <div class="mb-3">
            <label for="edit-date" class="form-label">Fecha y hora</label>
            <input type="datetime-local" class="form-control" name="date" id="edit-date" required>
          </div>

          <div class="mb-3">
            <label for="edit-capacity" class="form-label">Capacidad (personas)</label>
            <input type="number" class="form-control" name="capacity" id="edit-capacity" min="1" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>
