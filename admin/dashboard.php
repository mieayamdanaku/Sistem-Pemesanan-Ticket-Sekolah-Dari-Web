<?php
session_start();
require '../koneksi.php';
/** @var mysqli $conn */

if(!isset($_SESSION['admin'])){ header("Location: ../login.php"); exit; }

// --- 1. LOGIKA FILTER (DRILL-DOWN) ---
$view = $_GET['view'] ?? 'semua';
$query_filter = "";
$filter_title = "Semua Riwayat Pemesanan";
$jml_hari_grafik = 7; 
$judul_grafik = "Tren Penjualan";

if($view == 'semua'){
    $q_awal = mysqli_query($conn, "SELECT MIN(waktu_pesan) as awal FROM tiket");
    $data_awal = mysqli_fetch_assoc($q_awal);
    if($data_awal['awal']){
        $tgl_pertama = new DateTime($data_awal['awal']);
        $tgl_sekarang = new DateTime(date('Y-m-d'));
        $diff = $tgl_pertama->diff($tgl_sekarang);
        $total_hari_sistem = $diff->days + 1;
        $jml_hari_grafik = ($total_hari_sistem < 7) ? 7 : $total_hari_sistem;
    }
    $query_filter = ""; 
    $judul_grafik = "Grafik Penjualan Tiket Event";
} elseif($view == 'hari'){
    $query_filter = "WHERE DATE(tiket.waktu_pesan) = CURDATE()";
    $filter_title = "Riwayat Pemesanan Hari Ini";
    $jml_hari_grafik = 1; 
    $judul_grafik = "Penjualan Hari Ini ";
} elseif($view == 'minggu'){
    $query_filter = "WHERE tiket.waktu_pesan >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    $filter_title = "Riwayat Pemesanan 7 Hari Terakhir";
    $jml_hari_grafik = 7;
    $judul_grafik = "Penjualan 7 Hari Terakhir";
} elseif($view == 'bulan'){
    // PERBAIKAN: Ubah menjadi 30 hari terakhir agar logikanya sinkron dengan mingguan
    $query_filter = "WHERE tiket.waktu_pesan >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    $filter_title = "Riwayat Pemesanan 30 Hari Terakhir";
    $jml_hari_grafik = 30;
    $judul_grafik = "Penjualan 30 Hari Terakhir";
}

// --- 2. LOGIKA STATISTIK ANGKA (FINANSIAL) ---
$omzet_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as total FROM tiket"))['total'] ?? 0;
$omzet_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as total FROM tiket WHERE DATE(waktu_pesan) = CURDATE()"))['total'] ?? 0;
$omzet_minggu_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as total FROM tiket WHERE waktu_pesan >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)"))['total'] ?? 0;
// PERBAIKAN: Ubah menjadi 30 hari terakhir
$omzet_bulan_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as total FROM tiket WHERE waktu_pesan >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"))['total'] ?? 0;

// --- 3. LOGIKA STATISTIK OPERASIONAL ---
if($view == 'semua') {
    $total_tiket_terjual = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as total FROM tiket"))['total'] ?? 0;
    $total_event = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM events"))['total'] ?? 0;
    $event_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM events WHERE (tanggal_akhir >= CURDATE() OR (tanggal_akhir IS NULL AND tanggal >= CURDATE()))"))['total'] ?? 0;
    $event_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM events WHERE (tanggal_akhir < CURDATE() OR (tanggal_akhir IS NULL AND tanggal < CURDATE()))"))['total'] ?? 0;
}

// --- 4. LOGIKA GRAFIK ---
$grafik_tgl = [];
$grafik_pendapatan = [];
if($jml_hari_grafik == 1) {
    for($h=0; $h<=23; $h++) {
        $jam = str_pad($h, 2, "0", STR_PAD_LEFT);
        $q_jam = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM tiket WHERE DATE(waktu_pesan) = CURDATE() AND HOUR(waktu_pesan) = '$h'");
        $grafik_tgl[] = $jam . ":00";
        $grafik_pendapatan[] = mysqli_fetch_assoc($q_jam)['total'] ?? 0;
    }
} else {
    for($i = ($jml_hari_grafik - 1); $i >= 0; $i--){
        $tgl_cek = date('Y-m-d', strtotime("-$i days"));
        $q_grafik = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM tiket WHERE DATE(waktu_pesan) = '$tgl_cek'");
        $grafik_tgl[] = date('d M', strtotime($tgl_cek));
        $grafik_pendapatan[] = mysqli_fetch_assoc($q_grafik)['total'] ?? 0;
    }
}

$pemesanan = mysqli_query($conn, "SELECT tiket.*, siswa.nama as nama_siswa, events.judul as nama_event 
    FROM tiket JOIN siswa ON tiket.siswa_id = siswa.id JOIN events ON tiket.event_id = events.id 
    $query_filter ORDER BY tiket.id DESC LIMIT 50");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin | E-Tiket</title>
    <meta http-equiv="refresh" content="15">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="../assets/css/css2.css" rel="stylesheet">
    <script src="../assets/js/chart.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F8FAFC; color: #1E293B; scroll-behavior: smooth; }
        .navbar { background: white; border-bottom: 1px solid #E2E8F0; }
        .card-finance { border: none; border-radius: 24px; transition: 0.4s; color: white; text-decoration: none; display: block; overflow: hidden; position: relative; }
        .card-finance:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .bg-blue { background: linear-gradient(135deg, #2563EB, #1D4ED8); }
        .bg-green { background: linear-gradient(135deg, #10B981, #059669); }
        .bg-orange { background: linear-gradient(135deg, #F59E0B, #D97706); }
        .bg-purple { background: linear-gradient(135deg, #8B5CF6, #7C3AED); }
        .chart-card, .table-card, .mini-card { border: none; border-radius: 24px; background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.03); transition: 0.3s; text-decoration: none; display: block; }
        .mini-card:hover { background: #F1F5F9; transform: scale(1.02); }
        .active-filter { border: 4px solid #1E293B !important; }
        .icon-box { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    </style>
</head>
<body>

    <nav class="navbar px-4 py-3 sticky-top">
        <div class="container-fluid justify-content-start gap-3">
            <button class="btn btn-light border-0 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">☰ Menu</button>
            <a class="navbar-brand fw-bold text-primary m-0">Administrator Panel</a>
        </div>
    </nav>

    <?php include 'sidebar.php'; ?>

    <div class="container mt-5 mb-5">
        
        <h4 class="fw-800 mb-4">Ringkasan Pendapatan</h4>
        <div class="row mb-5">
            <div class="col-md-3 mb-3"><a href="?view=semua" class="card card-finance bg-blue p-4 <?= ($view == 'semua') ? 'active-filter' : '' ?>"><div class="small fw-bold opacity-75 text-uppercase">Total Pendapatan</div><h2 class="fw-800 m-0 mt-1">Rp <?= number_format($omzet_total) ?></h2></a></div>
            <div class="col-md-3 mb-3"><a href="?view=hari" class="card card-finance bg-green p-4 <?= ($view == 'hari') ? 'active-filter' : '' ?>"><div class="small fw-bold opacity-75 text-uppercase">Hari Ini</div><h2 class="fw-800 m-0 mt-1">Rp <?= number_format($omzet_hari_ini) ?></h2></a></div>
            <div class="col-md-3 mb-3"><a href="?view=minggu" class="card card-finance bg-orange p-4 <?= ($view == 'minggu') ? 'active-filter' : '' ?>"><div class="small fw-bold opacity-75 text-uppercase">Minggu Ini</div><h2 class="fw-800 m-0 mt-1">Rp <?= number_format($omzet_minggu_ini) ?></h2></a></div>
            <div class="col-md-3 mb-3"><a href="?view=bulan" class="card card-finance bg-purple p-4 <?= ($view == 'bulan') ? 'active-filter' : '' ?>"><div class="small fw-bold opacity-75 text-uppercase">Bulan Ini</div><h2 class="fw-800 m-0 mt-1">Rp <?= number_format($omzet_bulan_ini) ?></h2></a></div>
        </div>

        <?php if($view == 'semua'): ?>
        <h4 class="fw-800 mb-4">Statistik Operasional</h4>
        <div class="row mb-5">
            <div class="col-md-3 mb-3">
                <a href="#riwayat-section" class="card mini-card p-3 d-flex flex-row align-items-center gap-3">
                    <div class="icon-box bg-primary text-white shadow-sm">🎫</div>
                    <div><div class="text-muted small fw-bold text-uppercase">Tiket Terjual</div><h4 class="fw-800 m-0 text-dark"><?= number_format($total_tiket_terjual) ?></h4></div>
                </a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="kelola_event.php#aktif-section" class="card mini-card p-3 d-flex flex-row align-items-center gap-3">
                    <div class="icon-box bg-success text-white shadow-sm">🚀</div>
                    <div><div class="text-muted small fw-bold text-uppercase">Event Aktif</div><h4 class="fw-800 m-0 text-dark"><?= $event_aktif ?></h4></div>
                </a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="kelola_event.php#expired-section" class="card mini-card p-3 d-flex flex-row align-items-center gap-3">
                    <div class="icon-box bg-danger text-white shadow-sm">⌛</div>
                    <div><div class="text-muted small fw-bold text-uppercase">Event Selesai</div><h4 class="fw-800 m-0 text-dark"><?= $event_selesai ?></h4></div>
                </a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="kelola_event.php" class="card mini-card p-3 d-flex flex-row align-items-center gap-3">
                    <div class="icon-box bg-warning text-white shadow-sm">📅</div>
                    <div><div class="text-muted small fw-bold text-uppercase">Total Event</div><h4 class="fw-800 m-0 text-dark"><?= $total_event ?></h4></div>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <div class="card chart-card p-4 mb-5 border-0">
            <h5 class="fw-bold mb-4">📈 <?= $judul_grafik ?></h5>
            <div style="height: 350px;"><canvas id="salesChart"></canvas></div>
        </div>

        <div id="riwayat-section" class="d-flex justify-content-between align-items-center mb-4 pt-4">
            <h4 class="fw-800 m-0"><?= $filter_title ?></h4>
            <span class="badge bg-white text-dark border rounded-pill px-3 py-2 fw-bold shadow-sm">Data Terkini</span>
        </div>
        
        <div class="card table-card p-4 shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-3 border-0 rounded-start">Nama Siswa</th>
                            <th class="border-0">Event</th>
                            <th class="border-0">Total Harga</th>
                            <th class="px-3 border-0 rounded-end text-center">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($pemesanan) > 0): ?>
                            <?php while($p = mysqli_fetch_assoc($pemesanan)): ?>
                            <tr>
                                <td class="px-3 py-3 fw-bold"><?= htmlspecialchars($p['nama_siswa']) ?></td>
                                <td><?= htmlspecialchars($p['nama_event']) ?></td>
                                <td class="fw-bold text-success">Rp <?= number_format($p['total_harga']) ?></td>
                                <td class="text-center small text-muted fw-600"><?= date('d M Y, H:i', strtotime($p['waktu_pesan'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted fw-bold">Belum ada transaksi pada periode ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($grafik_tgl) ?>,
            datasets: [{
                label: 'Revenue (Rp)',
                data: <?= json_encode($grafik_pendapatan) ?>,
                borderColor: '#2563EB',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderWidth: 4, fill: true, tension: 0.4,
                pointRadius: <?= ($jml_hari_grafik > 20) ? 2 : 5 ?>
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: function(v) { return 'Rp ' + v.toLocaleString(); } } },
                x: { grid: { display: false } }
            }
        }
    });
    </script>
</body>
</html>