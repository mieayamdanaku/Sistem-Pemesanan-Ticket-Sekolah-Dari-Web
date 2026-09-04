<?php
session_start();
require '../koneksi.php';
/** @var mysqli $conn */

if(!isset($_SESSION['admin'])){ header("Location: ../login.php"); exit; }

$today = date('Y-m-d');

// Logika Hapus Aman (MENGGUNAKAN CARA 1)
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    
    // --- CARA 1 DIMULAI ---
    // Hapus semua tiket yang terhubung dengan event ini dulu, supaya pendapatan di dashboard hilang
    mysqli_query($conn, "DELETE FROM tiket WHERE event_id = '$id'");
    
    // Setelah tiket bersih, baru hapus data event-nya
    mysqli_query($conn, "DELETE FROM events WHERE id = '$id'");
    // --- CARA 1 SELESAI ---

    header("Location: kelola_event.php?deleted=1"); exit;
}

$aktif = mysqli_query($conn, "SELECT * FROM events WHERE tanggal >= '$today' OR tanggal_akhir >= '$today' ORDER BY id DESC");
$expired = mysqli_query($conn, "SELECT * FROM events WHERE (tanggal < '$today' AND (tanggal_akhir < '$today' OR tanggal_akhir IS NULL)) OR tanggal_akhir < '$today' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Event | E-Tiket</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/css2.css" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F8FAFC; scroll-behavior: smooth; }
        .card-main { border: none; border-radius: 24px; background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
        .badge-aktif { background: #dcfce7; color: #16a34a; font-weight: 700; }
        .badge-expired { background: #fee2e2; color: #dc2626; font-weight: 700; }
    </style>
</head>
<body>

    <nav class="navbar px-4 py-3 bg-white border-bottom sticky-top">
      <div class="container-fluid justify-content-start gap-3">
            <button class="btn btn-light border-0 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">☰ Menu</button>
            <a class="navbar-brand fw-bold text-primary m-0">Administrator Panel</a>
        
    </nav>

    <?php include 'sidebar.php'; ?>

    <div class="container mt-5 mb-5">
        
        <div id="aktif-section" class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-800 m-0">Event Yang Sedang Aktif</h3>
            <a href="tambah_event.php" class="btn btn-primary rounded-pill px-4 fw-bold shadow">+ Tambah Baru</a>
        </div>

        <div class="card card-main p-4 mb-5 shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-3 border-0 rounded-start">Judul Event</th>
                            <th class="border-0">Harga</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Kuota Slot</th>
                            <th class="border-0 rounded-end text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($e = mysqli_fetch_assoc($aktif)): ?>
                        <tr>
                            <td class="px-3 py-3 fw-bold"><?= $e['judul'] ?></td>
                            <td class="text-primary fw-bold">Rp<?= number_format($e['harga']) ?></td>
                            <td><span class="badge badge-aktif px-3 py-2 rounded-pill">Aktif</span></td>
                            <td class="fw-bold"><?= $e['slot_tiket'] ?></td>
                            <td class="text-center">
                                <a href="edit_event.php?id=<?= $e['id'] ?>" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold">Edit</a>
                                <a href="?hapus=<?= $e['id'] ?>" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold" onclick="return confirm('Hapus event?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <h3 id="expired-section" class="fw-800 mb-4 pt-4 text-muted">History Event Yang Telah Selesai (Expired)</h3>
        <div class="card card-main p-4 opacity-75 shadow-none border">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-3 border-0 rounded-start">Judul Event</th>
                            <th class="border-0">Harga</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Tanggal Berakhir</th>
                            <th class="border-0 rounded-end text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($ex = mysqli_fetch_assoc($expired)): ?>
                        <tr>
                            <td class="px-3 py-3 fw-bold text-muted"><?= $ex['judul'] ?></td>
                            <td class="text-muted">Rp<?= number_format($ex['harga']) ?></td>
                            <td><span class="badge badge-expired px-3 py-2 rounded-pill">Expired</span></td>
                            <td class="small text-muted"><?= date('d M Y', strtotime($ex['tanggal_akhir'] ?: $ex['tanggal'])) ?></td>
                            <td class="text-center">
                                <a href="edit_event.php?id=<?= $ex['id'] ?>" class="btn btn-outline-warning btn-sm rounded-pill px-3 fw-bold">Edit</a>
                                
                                <a href="?hapus=<?= $ex['id'] ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" onclick="return confirm('Bersihkan riwayat?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>