<?php
session_start();
require '../koneksi.php';
/** @var mysqli $conn */

// Proteksi Admin
if(!isset($_SESSION['admin'])){ header("Location: ../login.php"); exit; }

$where = "";
$tgl_awal = "";
$tgl_akhir = "";

// Logika Filter Tanggal
if(isset($_GET['filter'])) {
    $tgl_awal = mysqli_real_escape_string($conn, $_GET['tgl_awal']);
    $tgl_akhir = mysqli_real_escape_string($conn, $_GET['tgl_akhir']);
    
    if(!empty($tgl_awal) && !empty($tgl_akhir)) {
        // Filter dari tanggal awal sampai akhir
        $where = "WHERE DATE(t.waktu_pesan) BETWEEN '$tgl_awal' AND '$tgl_akhir'";
    }
}

// Ambil Data Tiket
$query = mysqli_query($conn, "SELECT t.*, s.nama as nama_siswa, e.judul as nama_event 
    FROM tiket t 
    JOIN siswa s ON t.siswa_id = s.id 
    JOIN events e ON t.event_id = e.id 
    $where 
    ORDER BY t.waktu_pesan DESC");

$total_pendapatan = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan | E-Tiket</title>
    
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/css2.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F8FAFC; color: #1E293B; }
        .navbar { background: white; border-bottom: 1px solid #E2E8F0; }
        .card-pro { border: none; border-radius: 24px; background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
        .print-header { display: none; } /* Sembunyikan header print di tampilan web */

        /* PENGATURAN KHUSUS SAAT CETAK / PRINT */
        @media print {
            body { background: white; padding: 0; margin: 0; }
            .no-print, .navbar, .offcanvas { display: none !important; } /* Sembunyikan sidebar & tombol */
            .card-pro { box-shadow: none !important; border: none !important; padding: 0 !important; }
            .container { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
            .print-header { display: block; text-align: center; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #000 !important; padding: 8px; }
            .text-success { color: black !important; }
            .text-primary { color: black !important; }
        }
    </style>
</head>
<body>

    <nav class="navbar px-4 py-3 sticky-top no-print">
        <div class="container-fluid justify-content-start gap-3">
            <button class="btn btn-light border-0 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">☰ Menu</button>
            <a class="navbar-brand fw-bold text-primary m-0">Administrator Panel</a>
        </div>
    </nav>

    <?php include 'sidebar.php'; ?>

    <div class="container mt-4 mb-5">
        
        <div class="print-header">
            <h2 class="fw-bold m-0">LAPORAN PENDAPATAN TIKET</h2>
            <p class="m-0">SMK Antartika 2 Sidoarjo</p>
            <p class="mt-2 text-muted">
                <?php if(!empty($tgl_awal) && !empty($tgl_akhir)): ?>
                    Periode: <?= date('d M Y', strtotime($tgl_awal)) ?> s/d <?= date('d M Y', strtotime($tgl_akhir)) ?>
                <?php else: ?>
                    Periode: Keseluruhan (Full Dari Awal)
                <?php endif; ?>
            </p>
            <hr style="border-top: 2px solid black;">
        </div>

        <div class="mb-4 no-print">
            <h4 class="fw-800 m-0">Rekap Laporan Keuangan</h4>
            <p class="text-muted small m-0">Filter transaksi berdasarkan bulan/tanggal atau cetak full.</p>
        </div>

        <div class="card card-pro p-4">
            
            <!-- FORM FILTER (Akan hilang saat di-print) -->
            <form method="GET" class="row g-3 mb-4 no-print">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Dari Tanggal</label>
                    <input type="date" name="tgl_awal" class="form-control" value="<?= htmlspecialchars($tgl_awal) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Sampai Tanggal</label>
                    <input type="date" name="tgl_akhir" class="form-control" value="<?= htmlspecialchars($tgl_akhir) ?>">
                </div>
                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button type="submit" name="filter" class="btn btn-primary fw-bold px-4 py-2 rounded-3 shadow-sm">Terapkan Filter</button>
                    <a href="laporan.php" class="btn btn-light border fw-bold px-4 py-2 rounded-3 shadow-sm">Reset</a>
                    <button type="button" onclick="window.print()" class="btn btn-dark fw-bold px-4 py-2 rounded-3 shadow-sm ms-auto">🖨️ Cetak / Print</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tgl Transaksi</th>
                            <th>Nama Siswa</th>
                            <th>Event Acara</th>
                            <th>Metode</th>
                            <th>Tiket</th>
                            <th>Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if(mysqli_num_rows($query) > 0):
                            while($row = mysqli_fetch_assoc($query)): 
                                $total_pendapatan += $row['total_harga'];
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="small text-muted"><?= date('d M Y, H:i', strtotime($row['waktu_pesan'])) ?></td>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_siswa']) ?></td>
                            <td><?= htmlspecialchars($row['nama_event']) ?></td>
                            <td><?= $row['metode_bayar'] ?? '-' ?></td>
                            <td><?= $row['jumlah'] ?>x</td>
                            <td class="fw-bold text-success">
                                <?= ($row['total_harga'] == 0) ? 'Gratis' : 'Rp ' . number_format($row['total_harga']) ?>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted fw-bold">Belum ada transaksi pada periode ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="6" class="text-end fw-800 text-uppercase pe-4">Total Pendapatan Bersih :</td>
                            <td class="fw-800 fs-5 text-primary">Rp <?= number_format($total_pendapatan) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>