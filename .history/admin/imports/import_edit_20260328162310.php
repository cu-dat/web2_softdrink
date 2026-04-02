<div class="container mt-4">

    <div class="card shadow">

        <!-- HEADER -->
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📦 Phiếu: <?= $import['import_code']; ?></h5>

            <span class="badge <?= $import['status'] ? 'bg-success' : 'bg-secondary' ?>">
                <?= $import['status'] ? 'Hoàn thành' : 'Nháp' ?>
            </span>
        </div>

        <div class="card-body">

            <!-- FORM THÊM -->
            <?php if (!$import['status']): ?>
            <form method="POST" action="import_add_item.php" class="row mb-3">

                <input type="hidden" name="import_id" value="<?= $id ?>">

                <div class="col-md-4">
                    <select name="product_id" class="form-select">
                        <?php while($p = $products->fetch_assoc()): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= $p['name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <input type="number" name="quantity" class="form-control" placeholder="Số lượng">
                </div>

                <div class="col-md-3">
                    <input type="number" name="price" class="form-control" placeholder="Giá nhập">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-success w-100">+ Thêm</button>
                </div>

            </form>
            <?php endif; ?>

            <!-- TABLE -->
            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá nhập</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    $total = 0;
                    while($d = $details->fetch_assoc()):
                        $amount = $d['quantity'] * $d['import_price'];
                        $total += $amount;
                    ?>
                    <tr>
                        <td><?= $d['name'] ?></td>
                        <td><span class="badge bg-info"><?= $d['quantity'] ?></span></td>
                        <td class="text-success"><?= number_format($d['import_price']) ?> đ</td>
                        <td class="text-danger fw-bold"><?= number_format($amount) ?> đ</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>

            <!-- TOTAL -->
            <div class="text-end">
                <h5>Tổng tiền: <span class="text-danger"><?= number_format($total) ?> đ</span></h5>
            </div>

            <!-- BUTTON -->
            <?php if (!$import['status']): ?>
            <form method="POST" action="import_complete.php" class="text-end mt-3">
                <input type="hidden" name="id" value="<?= $id ?>">
                <button class="btn btn-primary">💾 Lưu</button>
                <button class="btn btn-success">✅ Hoàn thành</button>
            </form>
            <?php endif; ?>

        </div>
    </div>

</div>