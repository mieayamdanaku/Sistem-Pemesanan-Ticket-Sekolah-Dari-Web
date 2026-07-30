<?php
session_start();
if(isset($_SESSION['siswa'])){
    header("Location: siswa/dashboard.php");
    exit;
} elseif(isset($_SESSION['admin'])){
    header("Location: admin/dashboard.php");
    exit;
}
// Jika belum login, langsung arahkan ke form login
header("Location: login.php");
exit;
?>