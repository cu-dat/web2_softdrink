<?php
include "../config/database.php";

$id = $_GET['id'];

$conn->query("DELETE FROM customers WHERE id = $id");

header("Location: list.php");
?>