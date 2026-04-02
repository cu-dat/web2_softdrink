<?php

require_once(__DIR__ . "/../admin/config/database.php");

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

$products = [];

if($result){
    while($row = $result->fetch_assoc()){
        $products[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'image' => $row['image'] ?: 'default.png'
        ];
    }
}

return $products;