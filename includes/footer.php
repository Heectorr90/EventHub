</main>

<!-- Footer -->
<!-- Ajustado de mt-5 py-4 a mt-4 py-3 para reducir el tamaño vertical -->
<footer class="bg-dark text-white mt-auto">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5><i class="bi bi-calendar-event"></i> EventHub </h5>
                <p class="text-muted mb-1">Sistema de gestión de eventos y participantes.</p>
            </div>
            <!-- En móvil el texto se centrará, y en escritorio se alineará a la derecha -->
            <div class="col-md-6 text-md-end text-center mt-3 mt-md-0"> 
                <p class="text-muted mb-0">© <?= date('Y') ?> EventHub. Todos los derechos reservados.</p>
                <?php if (defined('BASE_URL')): ?>
                    <!-- d-block asegura que la versión esté en su propia línea en todos los tamaños -->
                    <small class="text-muted d-block"><?= 'v1.0.0 | ' . (strpos(BASE_URL, 'localhost') !== false ? 'LOCAL' : 'PRODUCCIÓN') ?></small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS desde CDN -->
<script src="<?= asset('assets/js/bootstrap.bundle.min.js') ?>"></script>

<!-- Script personalizado (opcional) -->
<script>
// Auto-cerrar alertas después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            // Se añade una verificación para asegurar que bootstrap.Alert esté definido
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }
        }, 5000);
    });
});
</script>

</body>
</html>