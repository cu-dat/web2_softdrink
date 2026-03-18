<?php
include "../config/database.php";

$id = $_GET['id'];

$sql = "UPDATE customers 
        SET status = IF(status=1,0,1) 
        WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: list.php");
?>