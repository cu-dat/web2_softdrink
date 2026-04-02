<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/database.php';
requireAdmin($conn);
$flash = getFlashMessage();
<?php if ($msg = getFlashMessage()): ?>
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
        