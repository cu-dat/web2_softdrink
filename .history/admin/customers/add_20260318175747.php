<form action="insert.php" method="POST">
    Tên: <input type="text" name="full_name" required><br>
    Email: <input type="email" name="email"><br>
    Phone: <input type="text" name="phone"><br>
    Address: <input type="text" name="address"><br>

    Role:
    <select name="role">
        <option value="customer">Customer</option>
        <option value="staff">Staff</option>
        <option value="admin">Admin</option>
    </select><br>

    <button type="submit">Thêm</button>
</form>