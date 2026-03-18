<?php
include "../config/database.php";

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM customers WHERE id = $id");
$row = $result->fetch_assoc();
?>

<form action="update.php" method="POST">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">

    Tên: <input type="text" name="full_name" value="<?= $row['full_name'] ?>"><br>
    Email: <input type="email" name="email" value="<?= $row['email'] ?>"><br>

    Role:
    <select name="role">
        <option value="customer" <?= $row['role']=="customer"?"selected":"" ?>>Customer</option>
        <option value="staff" <?= $row['role']=="staff"?"selected":"" ?>>Staff</option>
        <option value="admin" <?= $row['role']=="admin"?"selected":"" ?>>Admin</option>
    </select><br>

    <button type="submit">Cập nhật</button>
</form>