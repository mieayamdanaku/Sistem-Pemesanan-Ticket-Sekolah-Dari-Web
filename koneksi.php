<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "ticket_event_sekolah";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>