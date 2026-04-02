<?php
require_once '../config/database.php';

$import_id = $_GET['id'];

// ❌ KHÔNG update inventory
// ❌ KHÔNG đổi status

// 👉 chỉ quay về
header("Location: import.php");
exit();