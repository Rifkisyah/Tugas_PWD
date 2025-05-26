<?php
require_once('../model/products.php');
$product = new Product();

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    if ($product->delete($product_id)) {
        header("Location: dashboard.php?module=product&pages=list-product&status=success");
    } else {
        header("Location: dashboard.php?module=product&pages=list-product&status=error");
    }
    exit;
} else {
    header("Location: dashboard.php?module=product&pages=list-product&status=invalid");
    exit;
}
?>