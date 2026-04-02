</div><!-- end page-content -->
</div><!-- end main-content -->
</div><!-- end admin-wrapper -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-hide alerts after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert:not(.no-remove)');

        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.classList.add('fade');
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }, 3000);
        });
    });
    // Confirm delete
    function confirmDelete(url) {
        if (confirm('Are you sure you want to delete this item?')) {

            window.location.href = url;
        }
    }
</script>
</body>

</html>