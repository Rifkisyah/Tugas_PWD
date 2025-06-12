<?php
session_start();
require_once('../model/Auth.php');
require_once('../model/Role.php');
require_once('../model/Users.php');

$auth = new Auth();
$role = new Role();
$user = new Users();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rolename = 'admin';

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../admin/signin.php?error=invalid_email");
        exit;
    }

    // Validasi panjang password
    if (strlen($password) < 8) {
        header("Location: ../admin/signin.php?error=password_too_short");
        exit;
    }

    if ($auth->signin($email, $password)) {
        $userRoleId = $user->getUserRoleIdByEmail($email);
        $expectedRoleId = $role->getRoleId($rolename);

        if ($userRoleId !== $expectedRoleId) {
            header("Location: ../admin/signin.php?error=role_mismatch");
            exit;
        }

        $_SESSION['admin_user_id'] = $auth->getUserId($email);
        $_SESSION['admin_username'] = $auth->getUsername($email);
        $_SESSION['admin_role'] = $rolename;
        $_SESSION['admin_role_id'] = $expectedRoleId;

        header("Location: ../admin/dashboard.php");
        exit;
    } else {
        header("Location: ../admin/signin.php?error=signin_failed");
        exit;
    }
} else {
    header("Location: ../admin/signin.php");
    exit;
}
