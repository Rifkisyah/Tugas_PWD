<?php
require_once('../model/stores.php');
$store = new Store();

if (isset($_GET['id'])) {
    $store_id = $_GET['id'];

    if ($store->delete($store_id)) {
        header("Location: dashboard.php?module=store&pages=list-store&status=success");
    } else {
        header("Location: dashboard.php?module=store&pages=list-store&status=error");
    }
    exit;
} else {
    header("Location: dashboard.php?module=store&pages=list-store&status=invalid");
    exit;
}
?>
