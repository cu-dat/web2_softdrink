</div><!-- end page-content -->
    </div><!-- end main-content -->
</div><!-- end admin-wrapper -->

<script>
    // Auto-hide alerts after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s';
                setTimeout(() => alert.remove(), 500);
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