<?php
require_once(__DIR__ . "/../admin/config/database.php");

// ===== CATEGORY =====
$cats = $conn->query("SELECT * FROM categories");

// ===== MIN MAX PRICE =====
$priceData = $conn->query("
    SELECT MIN(price) as min_price, MAX(price) as max_price 
    FROM products
")->fetch_assoc();

$minPrice = $priceData['min_price'] ?? 0;
$maxPrice = $priceData['max_price'] ?? 100000;

// current filter
$currentMin = $_GET['min'] ?? $minPrice;
$currentMax = $_GET['max'] ?? $maxPrice;
?>

<style>
.filter-box{
    background:#fff;
    border-radius:12px;
    padding:15px;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
}

/* CATEGORY */
.filter-item{
    display:flex;
    align-items:center;
    gap:8px;
    margin-bottom:10px;
    font-size:14px;
}
.filter-item input{
    accent-color:#cbb48b;
}

/* SLIDER */
.slider-box{
    position:relative;
    height:40px;
}
.slider-track{
    height:4px;
    background:#ddd;
    position:absolute;
    top:18px;
    width:100%;
}
.slider-range{
    position:absolute;
    height:4px;
    background:#cbb48b;
    top:18px;
}
.slider-box input{
    position:absolute;
    width:100%;
    pointer-events:none;
    -webkit-appearance:none;
    background:none;
}
.slider-box input::-webkit-slider-thumb{
    pointer-events:auto;
    width:14px;
    height:14px;
    background:#cbb48b;
    border-radius:50%;
}

/* INPUT */
.price-input{
    display:flex;
    gap:10px;
    margin-top:10px;
}
.price-input input{
    width:100%;
    padding:6px;
    border:1px solid #ddd;
    border-radius:6px;
}

/* BUTTON */
.filter-btn{
    width:100%;
    padding:10px;
    background:#111;
    color:#fff;
    border:none;
    border-radius:8px;
}
</style>

<div class="filter-box">

<form method="GET" action="">
<input type="hidden" name="page" value="products">

<!-- CATEGORY -->
<strong>Danh mục</strong>
<?php while($c = $cats->fetch_assoc()): ?>
<label class="filter-item">
    <input type="checkbox" name="category[]" value="<?= $c['name'] ?>"
    <?= (isset($_GET['category']) && in_array($c['name'], $_GET['category'])) ? 'checked' : '' ?>>
    <?= $c['name'] ?>
</label>
<?php endwhile; ?>

<!-- PRICE -->
<strong>Giá</strong>

<div class="slider-box">
    <div class="slider-track"></div>
    <div class="slider-range" id="range"></div>

    <input type="range" id="minRange"
           min="<?= $minPrice ?>"
           max="<?= $maxPrice ?>"
           value="<?= $currentMin ?>">

    <input type="range" id="maxRange"
           min="<?= $minPrice ?>"
           max="<?= $maxPrice ?>"
           value="<?= $currentMax ?>">
</div>

<div class="price-input">
    <input type="number" name="min" id="minInput" value="<?= $currentMin ?>">
    <input type="number" name="max" id="maxInput" value="<?= $currentMax ?>">
</div>

<button class="filter-btn mt-3">LỌC</button>

</form>
</div>

<script>
const minRange = document.getElementById("minRange");
const maxRange = document.getElementById("maxRange");
const minInput = document.getElementById("minInput");
const maxInput = document.getElementById("maxInput");
const range = document.getElementById("range");

const min = <?= $minPrice ?>;
const max = <?= $maxPrice ?>;

function updateSlider(){
    let minVal = parseInt(minRange.value);
    let maxVal = parseInt(maxRange.value);

    let left = ((minVal - min) / (max - min)) * 100;
    let right = ((maxVal - min) / (max - min)) * 100;

    range.style.left = left + "%";
    range.style.width = (right - left) + "%";
}

minRange.oninput = () => {
    if(parseInt(minRange.value) > parseInt(maxRange.value)){
        minRange.value = maxRange.value;
    }
    minInput.value = minRange.value;
    updateSlider();
};

maxRange.oninput = () => {
    if(parseInt(maxRange.value) < parseInt(minRange.value)){
        maxRange.value = minRange.value;
    }
    maxInput.value = maxRange.value;
    updateSlider();
};

minInput.oninput = () => {
    minRange.value = minInput.value;
    updateSlider();
};

maxInput.oninput = () => {
    maxRange.value = maxInput.value;
    updateSlider();
};

updateSlider();
</script>