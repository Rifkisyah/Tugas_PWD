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
    $rolename = $_POST['role'];

    // Validasi input umum
    if (strlen($username) < 3) {
        $redirectPage = ($rolename === 'admin') ? '../admin/signup.php' : '../customer/signup.php';
        header("Location: $redirectPage?error=username_too_short");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $redirectPage = ($rolename === 'admin') ? '../admin/signup.php' : '../customer/signup.php';
        header("Location: $redirectPage?error=invalid_email");
        exit;
    }

    if (strlen($password) < 8) {
        $redirectPage = ($rolename === 'admin') ? '../admin/signup.php' : '../customer/signup.php';
        header("Location: $redirectPage?error=password_too_short");
        exit;
    }

    if ($password !== $confirm_password) {
        $redirectPage = ($rolename === 'admin') ? '../admin/signup.php' : '../customer/signup.php';
        header("Location: $redirectPage?error=password_mismatch");
        exit;
    }

    // Cek role valid dan ambil role_id
    if ($rolename !== 'admin' && $rolename !== 'customer') {
        // role selain admin dan customer dianggap invalid
        header("Location: login.php?error=invalid_role");
        exit;
    }

    $role_id = $role->getRoleId($rolename);
    if ($role_id === null) {
        header("Location: login.php?error=invalid_role");
        exit;
    }

    // Proses signup
    $signupSuccess = $auth->signup($username, $email, $password, $confirm_password, $role_id);

    if ($signupSuccess) {
        $_SESSION['id'] = $auth->getUserId($email);
        $_SESSION['username'] = $auth->getUsername($email);

        if ($rolename === 'admin') {
            header("Location: ../admin/dashboard.php");
            exit;
        } elseif ($rolename === 'customer') {
            header("Location: ../customer/dashboard.php");
            exit;
        }
    } else {
        $redirectPage = ($rolename === 'admin') ? '../admin/signup.php' : '../customer/signup.php';
        header("Location: $redirectPage?error=signup_failed");
        exit;
    }

} else {
    header("Location: login.php");
    exit;
}
