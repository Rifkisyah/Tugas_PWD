<?php
require_once('../model/users.php');
$user = new Users();

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    if ($user->delete($user_id)) {
        header("Location: dashboard.php?module=user&pages=list-user&status=success");
    } else {
        header("Location: dashboard.php?module=user&pages=list-user&status=error");
    }
    exit;
} else {
    header("Location: dashboard.php?module=user&pages=list-user&status=invalid");
    exit;
}
?>
