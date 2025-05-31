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
    $rolename = $_POST['role'];

    // Validasi role hanya admin dan customer yang diizinkan login di sini
    if ($rolename !== 'admin' && $rolename !== 'customer') {
        header("Location: ../login.php?error=invalid_role");
        exit;
    }

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $redirectPage = ($rolename === 'admin') ? '../admin/signin.php' : '../customer/signin.php';
        header("Location: $redirectPage?error=invalid_email");
        exit;
    }

    // Validasi panjang password
    if (strlen($password) < 8) {
        $redirectPage = ($rolename === 'admin') ? '../admin/signin.php' : '../customer/signin.php';
        header("Location: $redirectPage?error=password_too_short");
        exit;
    }

    // Cek login
    if ($auth->signin($email, $password)) {
        // Pastikan role user sesuai dengan input role, jika perlu cek dari DB di sini
        $userRoleId = $user->getUserRoleIdByEmail($email);
        $expectedRoleId = $role->getRoleId($rolename);
        if ($userRoleId !== $expectedRoleId) {
            // Role mismatch, tolak login
            $redirectPage = ($rolename === 'admin') ? '../admin/signin.php' : '../customer/signin.php';
            header("Location: $redirectPage?error=role_mismatch");
            exit;
        }

        $_SESSION['user_id'] = $auth->getUserId($email);
        $_SESSION['username'] = $auth->getUsername($email);
        $_SESSION['role'] = $rolename;

        if ($rolename === 'admin') {
            $_SESSION['role_id'] = $role->getRoleId('admin');
            header("Location: ../admin/dashboard.php");
            exit;
        } else {
            $_SESSION['role_id'] = $role->getRoleId('customer');
            header("Location: ../customer/dashboard.php");
            exit;
        }
    } else {
        $redirectPage = ($rolename === 'admin') ? '../admin/signin.php' : '../customer/signin.php';
        header("Location: $redirectPage?error=signin_failed");
        exit;
    }

} else {
    header("Location: ../login.php");
    exit;
}
