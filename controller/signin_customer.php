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
    $rolename = 'customer';

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../customer/signin.php?error=invalid_email");
        exit;
    }

    // Validasi panjang password
    if (strlen($password) < 8) {
        header("Location: ../customer/signin.php?error=password_too_short");
        exit;
    }

    if ($auth->signin($email, $password)) {
        $userRoleId = $user->getUserRoleIdByEmail($email);
        $expectedRoleId = $role->getRoleId($rolename);

        if ($userRoleId !== $expectedRoleId) {
            header("Location: ../customer/signin.php?error=role_mismatch");
            exit;
        }

        $_SESSION['user_id'] = $auth->getUserId($email);
        $_SESSION['username'] = $auth->getUsername($email);
        $_SESSION['role'] = $rolename;
        $_SESSION['role_id'] = $expectedRoleId;

        header("Location: ../customer/dashboard.php");
        exit;
    } else {
        header("Location: ../customer/signin.php?error=signin_failed");
        exit;
    }
} else {
    header("Location: ../customer/signin.php");
    exit;
}
