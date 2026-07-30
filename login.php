<?php
session_start();
require 'koneksi.php';
/** @var mysqli $conn */

if(isset($_SESSION['siswa'])) { header("Location: siswa/dashboard.php"); exit; }
if(isset($_SESSION['admin'])) { header("Location: admin/dashboard.php"); exit; }

$error = "";

if (isset($_POST['login'])) {
    $identitas = trim($_POST['identitas']);
    $password = $_POST['password'];

    // 1. Cek Admin
    $stmt_admin = mysqli_prepare($conn, "SELECT * FROM admin WHERE username = ?");
    mysqli_stmt_bind_param($stmt_admin, "s", $identitas);
    mysqli_stmt_execute($stmt_admin);
    $res_admin = mysqli_stmt_get_result($stmt_admin);
    
    if ($admin = mysqli_fetch_assoc($res_admin)) {
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin['username'];
            header("Location: admin/dashboard.php"); 
            exit;
        }
    }

    // 2. Cek Siswa
    $stmt_siswa = mysqli_prepare($conn, "SELECT * FROM siswa WHERE nisn = ?");
    mysqli_stmt_bind_param($stmt_siswa, "s", $identitas);
    mysqli_stmt_execute($stmt_siswa);
    $res_siswa = mysqli_stmt_get_result($stmt_siswa);

    if ($siswa = mysqli_fetch_assoc($res_siswa)) {
        if (password_verify($password, $siswa['password'])) {
            $_SESSION['siswa'] = $siswa['id'];
            header("Location: siswa/dashboard.php"); 
            exit;
        }
    }
    $error = "Username/NISN atau Password salah!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - E-Tiket</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
        <meta http-equiv="refresh" content="30">
    <style>
        body { background: linear-gradient(135deg, #2563EB, #1E40AF); height: 100vh; display: flex; align-items: center; justify-content: center; font-family: sans-serif; }
        .login-card { background: white; padding: 40px; border-radius: 24px; width: 400px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
        .btn-login { background: #2563EB; color: white; font-weight: 600; border-radius: 12px; padding: 12px; width: 100%; border: none; transition: 0.3s; }
    </style>
</head>
<body>
<div class="login-card">
    <h3 class="fw-bold text-center mb-4">Login </h3>
    <?php if($error): ?>
        <div class="alert alert-danger py-2 text-center small"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label text-muted small fw-bold">Masukkan NISN</label>
            <input type="text" name="identitas" class="form-control" placeholder="NISN" required>
        </div>
        <div class="mb-4">
            <label class="form-label text-muted small fw-bold">Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" name="login" class="btn-login">Masuk </button>
    </form>
</div>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>