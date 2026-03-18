<?php
require_once 'includes/functions.php';
require_once 'config/database.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        setFlashMessage('success', 'Category deleted successfully!');
    } else {
        setFlashMessage('error', 'Failed to delete category. It may have products linked.');
    }
    $stmt->close();
}

header("Location: categories.php");
exit();
?>