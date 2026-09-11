<?php
session_start();
require '../koneksi.php';

/** @var mysqli $conn */
if(!isset($_SESSION['siswa'])){ 
    header("Location: ../login.php"); 
    exit; 
}

$id_siswa = $_SESSION['siswa'];

// Ambil riwayat pemesanan siswa dari database
$q_history = mysqli_query($conn, "SELECT tiket.*, events.judul, events.tanggal, events.gambar 
    FROM tiket 
    JOIN events ON tiket.event_id = events.id 
    WHERE tiket.siswa_id = '$id_siswa' 
    ORDER BY tiket.waktu_pesan DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Pembelian - E-Tiket</title>

    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/css2.css" rel="stylesheet">
    <!-- PANGGIL FILE CSS BARU DI SINI -->
    <link href="../assets/css/history.css" rel="stylesheet">

</head>

<body>

    <nav class="navbar px-4 py-3 sticky-top">
        <div class="container-fluid justify-content-start gap-3">
            <button class="btn btn-light border-0 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarSiswa">☰</button>
            <a class="navbar-brand m-0">Sistem Pemesanan Tiket</a>
        </div>
    </nav>

    <?php include 'sidebar.php'; ?>

    <div class="container mt-5 mb-5">
        <h4 class="section-title">📜 History Pembelian Anda</h4>

        <div class="row">
            <?php if(mysqli_num_rows($q_history) > 0): ?>
                <?php while($h = mysqli_fetch_assoc($q_history)): ?>
                <div class="col-md-12 mb-4">
                    <div class="card card-history">
                        <img src="../assets/img/<?= $h['gambar'] ?>" class="history-img me-md-4">
                        <div class="flex-grow-1">
                            <h5 class="fw-bold text-dark mb-1"><?= $h['judul'] ?></h5>
                            <p class="text-muted small mb-2">Tanggal Pesan: <?= date('d M Y, H:i', strtotime($h['waktu_pesan'])) ?></p>
                            <div class="d-flex flex-wrap gap-3 mb-2">
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Total: Rp <?= number_format($h['total_harga']) ?></span>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><?= $h['jumlah'] ?> Tiket</span>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Metode: <?= $h['metode_bayar'] ?? '-' ?></span>
                            </div>
                        </div>
                        <div class="ms-md-3">
                            <a href="struk.php?id=<?= $h['id'] ?>" class="btn-struk d-inline-block text-center">Lihat Struk</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h5 class="text-muted fw-bold">Belum ada riwayat pembelian.</h5>
                    <a href="dashboard.php" class="btn btn-primary mt-3 px-4 py-2 rounded-pill">Cari Event Sekarang</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>