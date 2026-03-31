<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $code = 'IMP' . time();
    $note = $_POST['note'];

    $stmt = $conn->prepare("
        INSERT INTO imports (import_code, note) 
        VALUES (?, ?)
    ");
    $stmt->bind_param("ss", $code, $note);
    $stmt->execute();

    $id = $stmt->insert_id;

    header("Location: import_add_item.php?id=$id");
}
?>