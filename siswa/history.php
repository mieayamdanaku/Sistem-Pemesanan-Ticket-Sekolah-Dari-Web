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

    <style>
        :root{ --primary:#1E3A8A; --secondary:#DC2626; --light:#F8FAFC; --dark:#0F172A; --soft:#E2E8F0; }
        *{ font-family:'Plus Jakarta Sans',sans-serif; }
        body{ background:linear-gradient(to bottom,#F8FAFC,#EEF2FF); color:var(--dark); min-height: 100vh;}
        
        .navbar{ background:rgba(255,255,255,0.93); backdrop-filter:blur(14px); box-shadow:0 4px 20px rgba(0,0,0,0.05); border-bottom:1px solid rgba(0,0,0,0.04); }
        .navbar-brand{ color:var(--primary)!important; font-weight:800; letter-spacing:.3px; }
        
        .section-title{ font-weight:800; margin-bottom:28px; color:var(--dark); }
        
        /* CARD HISTORY */
        .card-history { border:none; border-radius:24px; overflow:hidden; background:white; transition:.3s; box-shadow:0 10px 30px rgba(0,0,0,0.05); display: flex; flex-direction: row; align-items: center; padding: 15px;}
        .card-history:hover { transform:translateY(-5px); box-shadow:0 20px 40px rgba(30,58,138,0.10); }
        .history-img { width: 120px; height: 120px; border-radius: 16px; object-fit: cover; }
        
        .btn-struk { background:white; border:2px solid var(--primary); color:var(--primary); border-radius:999px; font-weight:700; transition:.3s; padding: 8px 24px; text-decoration: none; }
        .btn-struk:hover { background:var(--primary); color:white; }

        @media(max-width:768px){
            .card-history { flex-direction: column; text-align: center; }
            .history-img { width: 100%; height: 180px; margin-bottom: 15px; }
            .btn-struk { width: 100%; margin-top: 15px; }
        }
    </style>
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