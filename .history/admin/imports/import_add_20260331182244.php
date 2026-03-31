<?php
require_once '../config/database.php';

$conn->query("INSERT INTO imports (status) VALUES ('draft')");
$id = $conn->insert_id;

header("Location: import_add_item.php?id=$id");
exit();