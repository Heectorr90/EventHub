<!-- Modal para agregar participante -->
<div class="modal fade" id="addParticipantModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">➕ Agregar Participante</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="addParticipantForm">
        <input type="hidden" name="event_id" value="<?= $event_id ?>">
        <div class="modal-body">
          <div id="messageDiv"></div>
          
          <div class="mb-3">
            <label class="form-label">Correo del usuario *</label>
            <input type="email" class="form-control" name="user_email" id="user_email" 
                   placeholder="ejemplo@correo.com" required>
            <small class="text-muted">Ingresa el correo del usuario registrado en el sistema</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">
            <span id="btnText">Agregar</span>
            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('addParticipantForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = e.target;
    const messageDiv = document.getElementById('messageDiv');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Limpiar mensajes
    messageDiv.innerHTML = '';
    
    // Mostrar loading
    btnText.classList.add('d-none');
    btnSpinner.classList.remove('d-none');
    submitBtn.disabled = true;
    
    try {
        const formData = new FormData(form);
        
        const response = await fetch('/EventHub/public/index.php?page=add_participant', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            messageDiv.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
            form.reset();
            
            // Recargar la página después de 1.5 segundos
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            messageDiv.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (error) {
        messageDiv.innerHTML = `<div class="alert alert-danger">Error de conexión</div>`;
    } finally {
        btnText.classList.remove('d-none');
        btnSpinner.classList.add('d-none');
        submitBtn.disabled = false;
    }
});
</script>