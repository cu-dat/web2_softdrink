<?php
require_once '../web2_softdrink/config/database.php';

$id = (int)$_GET['id'];

$conn->query("
    UPDATE products 
    SET status = IF(status = 1, 0, 1)
    WHERE id = $id
");

header("Location: product.php");
exit;