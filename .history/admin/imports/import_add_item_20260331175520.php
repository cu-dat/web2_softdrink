<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/navbar.php';
require_once '../includes/header.php';

requireAdmin($conn);

$import_id = $_GET['id'];

// sản phẩm
$products = $conn->query("SELECT id, name FROM products");

// thêm item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pid = (int)$_POST['product_id'];
    $qty = (int)$_POST['quantity'];
    $price = (int)$_POST['price'];

    if ($pid <= 0 || $qty <= 0 || $price < 0) {
        die("Dữ liệu không hợp lệ!");
    }

    // check trùng
    $check = $conn->query("
        SELECT id, quantity 
        FROM import_details 
        WHERE import_id=$import_id AND product_id=$pid
    ");

    if ($check->num_rows > 0) {
        $row = $check->fetch_assoc();
        $newQty = $row['quantity'] + $qty;

        $conn->query("
            UPDATE import_details 
            SET quantity=$newQty, import_price=$price 
            WHERE id={$row['id']}
        ");
    } else {
        $stmt = $conn->prepare("
            INSERT INTO import_details(import_id,product_id,quantity,import_price)
            VALUES(?,?,?,?)
        ");
        $stmt->bind_param("iiii", $import_id,$pid,$qty,$price);
        $stmt->execute();
    }

    header("Location: import_add_item.php?id=$import_id");
    exit();
}

// load items
$items = $conn->query("
    SELECT d.*, p.name 
    FROM import_details d
    JOIN products p ON d.product_id = p.id
    WHERE d.import_id = $import_id
");
?>

<div class="container mt-4">

<div class="card shadow">
<div class="card-header bg-primary text-white">
    <h5>📦 Thêm sản phẩm vào phiếu</h5>
</div>

<div class="card-body">

<form method="POST" class="row g-2">
    <div class="col-md-4">
        <select name="product_id" class="form-select" required>
            <option value="">-- Chọn sản phẩm --</option>
            <?php while($p = $products->fetch_assoc()): ?>
                <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-3">
        <input type="number" name="quantity" class="form-control" placeholder="Số lượng" required>
    </div>

    <div class="col-md-3">
        <input type="number" name="price" class="form-control" placeholder="Giá nhập" required>
    </div>

    <div class="col-md-2">
        <button class="btn btn-success w-100">Thêm</button>
    </div>
</form>

<!-- TABLE -->
<table class="table table-bordered mt-3 text-center">
<tr>
    <th>Sản phẩm</th>
    <th>SL</th>
    <th>Giá</th>
    <th>Tổng</th>
</tr>

<?php while($i = $items->fetch_assoc()): ?>
<tr>
    <td><?= $i['name'] ?></td>
    <td><?= $i['quantity'] ?></td>
    <td><?= formatCurrency($i['import_price']) ?></td>
    <td><?= formatCurrency($i['quantity'] * $i['import_price']) ?></td>
</tr>
<?php endwhile; ?>

</table>

<a href="import_complete.php?id=<?= $import_id ?>" 
   class="btn btn-success"
   onclick="return confirm('Hoàn thành phiếu?')">
   ✔ Hoàn thành
</a>

</div>
</div>
</div>