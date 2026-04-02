<?php
require_once("admin/config/database.php");

if(!isset($_SESSION['user'])){
    echo "<script>window.location='index.php?page=login'</script>";
    exit;
}

$user_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<style>
.profile-container{
    min-height: 80vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #f5f5f5;
}

.profile-box{
    width: 420px;
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    font-family: Arial;
}

.profile-box h3{
    text-align: center;
    margin-bottom: 20px;
}

.profile-box label{
    font-weight: 600;
    margin-top: 10px;
    display: block;
}

.profile-box input{
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ddd;
    border-radius: 6px;
}

.profile-box input:focus{
    border-color: #0a7f65;
    outline: none;
}

.profile-box button{
    width: 100%;
    margin-top: 15px;
    padding: 10px;
    background: #0a7f65;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.profile-box button:hover{
    background: #086a55;
}

.logout-btn{
    display: block;
    text-align: center;
    margin-top: 12px;
    color: red;
    text-decoration: none;
}

.alert-success{
    background: #d4edda;
    color: #155724;
    padding: 8px;
    border-radius: 5px;
    margin-bottom: 10px;
    text-align: center;
}
</style>

<div class="profile-container">

    <div class="profile-box">

        <h3>THÔNG TIN TÀI KHOẢN</h3>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert-success">Cập nhật thành công!</div>
        <?php endif; ?>

        <form method="POST" action="action/update_profile.php">

            <label>Họ tên</label>
            <input name="name" value="<?= $user['full_name'] ?>">

            <label>Email</label>
            <input value="<?= $user['email'] ?>" disabled>

            <label>Số điện thoại</label>
            <input name="phone" value="<?= $user['phone'] ?>">

            <label>Địa chỉ</label>
            <input name="address" value="<?= $user['address'] ?>">

            <button>Lưu thay đổi</button>

        </form>

        <a href="action/logout.php" class="logout-btn">Đăng xuất</a>

    </div>

</div>