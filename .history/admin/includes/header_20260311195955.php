<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/database.php';
requireLogin();
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soft Drink Admin - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="admin-wrapper">