<?php
session_start();
require '../koneksi.php';

/** @var mysqli $conn */
if(!isset($_SESSION['siswa'])){ 
    header("Location: ../login.php"); 
    exit; 
}

$id_siswa = $_SESSION['siswa'];

if(!isset($_GET['id'])){
    header("Location: history.php");
    exit;
}

$id_tiket = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil detail struk dengan Join ke 3 tabel
$query = mysqli_query($conn, "SELECT t.*, e.judul, e.tanggal, e.tanggal_akhir, e.jam_mulai, e.jam_selesai, e.lokasi, s.nama as nama_siswa, s.nisn 
    FROM tiket t 
    JOIN events e ON t.event_id = e.id 
    JOIN siswa s ON t.siswa_id = s.id 
    WHERE t.id = '$id_tiket' AND t.siswa_id = '$id_siswa'");

if(mysqli_num_rows($query) == 0){
    header("Location: history.php");
    exit;
}

$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembelian - <?= $data['kode_unik'] ?></title>
    
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/css2.css" rel="stylesheet">
    <!-- PANGGIL FILE CSS BARU DI SINI -->
    <link href="../assets/css/struk.css" rel="stylesheet">
    
    <script src="../assets/js/qrcode.min.js"></script>
</head>
<body>

    <!-- STRUK AREA -->
    <div class="receipt-card">
        <div class="receipt-header">
            <h4 class="fw-800 m-0">RECEIPT</h4>
            <p class="small opacity-75 m-0 mt-1">SMK Antartika 2 Sidoarjo</p>
        </div>
        
        <div class="receipt-body">
            <div class="text-center mb-4">
                <div id="qrcode" class="d-flex justify-content-center mb-2"></div>
                <h3 class="fw-800 m-0" style="letter-spacing: 3px;"><?= $data['kode_unik'] ?></h3>
            </div>

            <div class="receipt-item">
                <span class="receipt-label">Tgl Transaksi</span>
                <span class="receipt-val"><?= date('d M Y, H:i', strtotime($data['waktu_pesan'])) ?></span>
            </div>
            <div class="receipt-item">
                <span class="receipt-label">Nama Pemesan</span>
                <span class="receipt-val"><?= htmlspecialchars($data['nama_siswa']) ?> <br><small class="text-muted">(<?= $data['nisn'] ?>)</small></span>
            </div>
            
            <div class="divider"></div>

            <div class="receipt-item">
                <span class="receipt-label">Event</span>
                <span class="receipt-val"><?= htmlspecialchars($data['judul']) ?></span>
            </div>
            
            <?php
                // LOGIKA TANGGAL MULAI & SELESAI
                $tgl_display = date('d M Y', strtotime($data['tanggal']));
                if(!empty($data['tanggal_akhir']) && $data['tanggal_akhir'] != '0000-00-00' && $data['tanggal_akhir'] != $data['tanggal']) {
                    $tgl_display .= ' - ' . date('d M Y', strtotime($data['tanggal_akhir']));
                }

                // LOGIKA JAM MULAI & SELESAI
                $jam_display = date('H:i', strtotime($data['jam_mulai']));
                if(!empty($data['jam_selesai']) && $data['jam_selesai'] != '00:00:00') {
                    $jam_display .= ' - ' . date('H:i', strtotime($data['jam_selesai']));
                }
            ?>
            
            <div class="receipt-item">
                <span class="receipt-label">Pelaksanaan</span>
                <span class="receipt-val"><?= $tgl_display ?> <br> Pukul <?= $jam_display ?></span>
            </div>
            <div class="receipt-item">
                <span class="receipt-label">Lokasi</span>
                <span class="receipt-val"><?= htmlspecialchars($data['lokasi']) ?></span>
            </div>
            
            <div class="divider"></div>

            <div class="receipt-item">
                <span class="receipt-label">Metode Pembayaran</span>
                <span class="receipt-val"><?= $data['metode_bayar'] ?? '-' ?></span>
            </div>
            <div class="receipt-item">
                <span class="receipt-label">Jumlah Tiket</span>
                <span class="receipt-val"><?= $data['jumlah'] ?>x</span>
            </div>

            <div class="total-box d-flex justify-content-between align-items-center mt-3">
                <span class="fw-bold">TOTAL</span>
                <span class="total-text">
                    <?= ($data['total_harga'] == 0) ? 'GRATIS' : 'Rp ' . number_format($data['total_harga']) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- TOMBOL AKSI (Akan hilang saat diprint) -->
    <div class="action-buttons no-print">
        <a href="history.php" class="btn-back">Kembali</a>
        <button onclick="window.print()" class="btn-print">🖨️ Cetak Struk</button>
    </div>

    <script>
        // Generate QR Code sesuai kode unik tiket
        new QRCode(document.getElementById("qrcode"), {
            text: "<?= $data['kode_unik'] ?>",
            width: 120,
            height: 120,
            colorDark : "#0F172A",
            colorLight : "#ffffff"
        });
    </script>
</body>
</html>