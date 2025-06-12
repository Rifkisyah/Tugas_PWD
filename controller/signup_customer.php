<?php
session_start();
require_once('../model/Auth.php');
require_once('../model/Role.php');

$auth = new Auth();
$role = new Role();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $rolename = 'customer';

    // Validasi input
    if (strlen($username) < 3) {
        header("Location: ../customer/signup.php?error=username_too_short");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../customer/signup.php?error=invalid_email");
        exit;
    }

    if (strlen($password) < 8) {
        header("Location: ../customer/signup.php?error=password_too_short");
        exit;
    }

    if ($password !== $confirm_password) {
        header("Location: ../customer/signup.php?error=password_mismatch");
        exit;
    }

    $role_id = $role->getRoleId($rolename);
    if ($role_id === null) {
        header("Location: ../login.php?error=invalid_role");
        exit;
    }

    // Proses signup
    $signupSuccess = $auth->signup($username, $email, $password, $confirm_password, $role_id);

    if ($signupSuccess) {
        $_SESSION['user_id'] = $auth->getUserId($email);
        $_SESSION['username'] = $auth->getUsername($email);
        $_SESSION['role_id'] = $role_id;

        header("Location: ../customer/dashboard.php");
        exit;
    } else {
        header("Location: ../customer/signup.php?error=signup_failed");
        exit;
    }
} else {
    header("Location: ../customer/signup.php");
    exit;
}
