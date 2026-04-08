<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/database.php';
requireAdmin($conn);
$flash = getFlashMessage();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soft Drink Admin - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="admin-wrapper">
        <?php if ($flash): ?>
            <div id="toast-alert"
                class="position-fixed top-0 end-0 p-3"
                style="z-index:9999; min-width:300px;">

                <div class="alert alert-warning no-auto-hide">
                    <?= $flash['message'] ?>
                </div>
            </div>

            <script>
                setTimeout(() => {
                    document.querySelectorAll('.alert:not(.no-auto-hide)').forEach(el => el.remove());
                }, 3000);
            </script>
        <?php endif; ?>