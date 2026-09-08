<?php
session_start();
require '../koneksi.php';
/** @var mysqli $conn */

// Sesuaikan session check dengan hak akses petugas (misal: 'petugas')
if(!isset($_SESSION['petugas'])){ header("Location: ../login.php"); exit; }

// --- 1. LOGIKA FILTER (DRILL-DOWN) ---
$view = $_GET['view'] ?? 'semua';
$query_filter = "";
$filter_title = "Semua Riwayat Pemesanan Tiket Event";

if($view == 'hari'){
    $query_filter = "WHERE DATE(tiket.waktu_pesan) = CURDATE()";
    $filter_title = "Riwayat Pemesanan Hari Ini";
} elseif($view == 'minggu'){
    $query_filter = "WHERE tiket.waktu_pesan >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    $filter_title = "Riwayat Pemesanan 7 Hari Terakhir";
} elseif($view == 'bulan'){
    $query_filter = "WHERE tiket.waktu_pesan >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    $filter_title = "Riwayat Pemesanan 30 Hari Terakhir";
}

// --- 2. LOGIKA STATISTIK OPERASIONAL KHUSUS PETUGAS EVENT ---
$total_tiket_terjual = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as total FROM tiket"))['total'] ?? 0;
$total_event = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM events"))['total'] ?? 0;
$event_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM events WHERE (tanggal_akhir >= CURDATE() OR (tanggal_akhir IS NULL AND tanggal >= CURDATE()))"))['total'] ?? 0;
$event_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM events WHERE (tanggal_akhir < CURDATE() OR (tanggal_akhir IS NULL AND tanggal < CURDATE()))"))['total'] ?? 0;

// Ambil data pemesanan/tiket yang masuk
$pemesanan = mysqli_query($conn, "SELECT tiket.*, siswa.nama as nama_siswa, events.judul as nama_event 
    FROM tiket JOIN siswa ON tiket.siswa_id = siswa.id JOIN events ON tiket.event_id = events.id 
    $query_filter ORDER BY tiket.id DESC LIMIT 50");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Petugas Event | E-Tiket</title>
    <meta http-equiv="refresh" content="15">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/css2.css" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F8FAFC; color: #1E293B; scroll-behavior: smooth; }
        .navbar { background: white; border-bottom: 1px solid #E2E8F0; }
        .card-operational { border: none; border-radius: 24px; transition: 0.4s; color: white; text-decoration: none; display: block; overflow: hidden; position: relative; }
        .card-operational:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .bg-primary-custom { background: linear-gradient(135deg, #2563EB, #1D4ED8); }
        .bg-success-custom { background: linear-gradient(135deg, #10B981, #059669); }
        .bg-danger-custom { background: linear-gradient(135deg, #EF4444, #DC2626); }
        .bg-warning-custom { background: linear-gradient(135deg, #F59E0B, #D97706); }
        .table-card, .mini-card { border: none; border-radius: 24px; background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.03); transition: 0.3s; text-decoration: none; display: block; }
        .mini-card:hover { background: #F1F5F9; transform: scale(1.02); }
        .active-filter { border: 4px solid #1E293B !important; }
        .icon-box { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    </style>
</head>
<body>

    <nav class="navbar px-4 py-3 sticky-top">
        <div class="container-fluid justify-content-start gap-3">
            <button class="btn btn-light border-0 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">☰ Menu</button>
            <a class="navbar-brand fw-bold text-primary m-0">Panel Petugas Event</a>
        </div>
    </nav>

    <?php include 'sidebar.php'; ?>

    <div class="container mt-5 mb-5">
        
        <div class="mb-4">
            <h3 class="fw-800">Dashboard Pengelolaan Event</h3>
            <p class="text-muted">Kelola data event, pantau status keaktifan event, dan monitoring tiket masuk.</p>
        </div>

        <!-- Ringkasan Operasional (Menggantikan Kartu Omzet/Keuangan) -->
        <div class="row mb-5">
            <div class="col-md-3 mb-3">
                <div class="card card-operational bg-primary-custom p-4">
                    <div class="small fw-bold opacity-75 text-uppercase">Total Tiket Terpesan</div>
                    <h2 class="fw-800 m-0 mt-1"><?= number_format($total_tiket_terjual) ?></h2>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-operational bg-success-custom p-4">
                    <div class="small fw-bold opacity-75 text-uppercase">Event Aktif</div>
                    <h2 class="fw-800 m-0 mt-1"><?= $event_aktif ?></h2>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-operational bg-danger-custom p-4">
                    <div class="small fw-bold opacity-75 text-uppercase">Event Selesai</div>
                    <h2 class="fw-800 m-0 mt-1"><?= $event_selesai ?></h2>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-operational bg-warning-custom p-4">
                    <div class="small fw-bold opacity-75 text-uppercase">Total Seluruh Event</div>
                    <h2 class="fw-800 m-0 mt-1"><?= $total_event ?></h2>
                </div>
            </div>
        </div>

        <!-- Filter Shortcut Riwayat Pemesanan Berdasarkan Waktu -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-800 m-0">Filter Riwayat Berdasarkan Waktu</h4>
            <div class="btn-group" role="group">
                <a href="?view=semua" class="btn btn-outline-dark <?= ($view == 'semua') ? 'active' : '' ?>">Semua</a>
                <a href="?view=hari" class="btn btn-outline-dark <?= ($view == 'hari') ? 'active' : '' ?>">Hari Ini</a>
                <a href="?view=minggu" class="btn btn-outline-dark <?= ($view == 'minggu') ? 'active' : '' ?>">7 Hari</a>
                <a href="?view=bulan" class="btn btn-outline-dark <?= ($view == 'bulan') ? 'active' : '' ?>">30 Hari</a>
            </div>
        </div>

        <div id="riwayat-section" class="d-flex justify-content-between align-items-center mb-4 pt-2">
            <h5 class="fw-bold text-secondary m-0"><?= $filter_title ?></h5>
            <span class="badge bg-white text-dark border rounded-pill px-3 py-2 fw-bold shadow-sm">Data Terkini</span>
        </div>
        
        <!-- Tabel Pemesanan Tiket Masuk -->
        <div class="card table-card p-4 shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-3 border-0 rounded-start">Nama Siswa</th>
                            <th class="border-0">Event Terkait</th>
                            <th class="border-0">Jumlah Tiket</th>
                            <th class="px-3 border-0 rounded-end text-center">Waktu Pemesanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($pemesanan) > 0): ?>
                            <?php while($p = mysqli_fetch_assoc($pemesanan)): ?>
                            <tr>
                                <td class="px-3 py-3 fw-bold"><?= htmlspecialchars($p['nama_siswa']) ?></td>
                                <td><?= htmlspecialchars($p['nama_event']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= $p['jumlah'] ?> Tiket</span></td>
                                <td class="text-center small text-muted fw-600"><?= date('d M Y, H:i', strtotime($p['waktu_pesan'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted fw-bold">Belum ada data pemesanan pada periode ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>