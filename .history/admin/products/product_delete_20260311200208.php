<?php
require_once 'includes/functions.php';
require_once 'config/database.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Delete product image
    $product = $conn->query("SELECT image FROM products WHERE id = $id")->fetch_assoc();
    if ($product && $product['image'] && file_exists('uploads/' . $product['image'])) {
        unlink('uploads/' . $product['image']);
    }

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        setFlashMessage('success', 'Product deleted successfully!');
    } else {
        setFlashMessage('error', 'Failed to delete product.');
    }
    $stmt->close();
}

header("Location: products.php");
exit();
?>