<?php
session_start();
require 'koneksi.php';
/** @var mysqli $conn */

if (!isset($_SESSION['admin']) && !isset($_SESSION['petugas'])) {
    header('Location: login.php');
    exit;
}

$format = $_GET['format'] ?? 'pdf';
$view = $_GET['view'] ?? 'semua';
$filters = [
    'semua' => ['', 'Semua Riwayat Pemesanan'],
    'hari' => ['WHERE DATE(tiket.waktu_pesan) = CURDATE()', 'Riwayat Pemesanan Hari Ini'],
    'minggu' => ['WHERE tiket.waktu_pesan >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)', 'Riwayat Pemesanan 7 Hari Terakhir'],
    'bulan' => ['WHERE tiket.waktu_pesan >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)', 'Riwayat Pemesanan 30 Hari Terakhir'],
];

if (!isset($filters[$view])) {
    $view = 'semua';
}

[$where, $judul] = $filters[$view];
$query = "SELECT tiket.kode_unik, siswa.nama AS nama_siswa, events.judul AS nama_event,
                 tiket.jumlah, tiket.total_harga, tiket.metode_bayar, tiket.waktu_pesan
          FROM tiket
          JOIN siswa ON tiket.siswa_id = siswa.id
          JOIN events ON tiket.event_id = events.id
          $where
          ORDER BY tiket.waktu_pesan DESC";
$result = mysqli_query($conn, $query);
if (!$result) {
    http_response_code(500);
    exit('Gagal mengambil data rekap.');
}

$rows = [];
$total_tiket = 0;
$total_pendapatan = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
    $total_tiket += (int) $row['jumlah'];
    $total_pendapatan += (int) $row['total_harga'];
}

if ($format === 'excel') {
    $filename = 'rekap-pemesanan-' . $view . '-' . date('Ymd-His') . '.xls';
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    ?>
    <meta charset="UTF-8">
    <table border="1">
        <tr><th colspan="8">Rekap Pemesanan Tiket</th></tr>
        <tr><th colspan="8"><?= htmlspecialchars($judul) ?></th></tr>
        <tr>
            <th>No</th><th>Kode Tiket</th><th>Nama Siswa</th><th>Event</th>
            <th>Jumlah Tiket</th><th>Total Harga</th><th>Metode Bayar</th><th>Waktu Pemesanan</th>
        </tr>
        <?php foreach ($rows as $index => $row): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($row['kode_unik'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
            <td><?= htmlspecialchars($row['nama_event']) ?></td>
            <td><?= (int) $row['jumlah'] ?></td>
            <td><?= (int) $row['total_harga'] ?></td>
            <td><?= htmlspecialchars($row['metode_bayar'] ?? '-') ?></td>
            <td><?= htmlspecialchars(date('d-m-Y H:i', strtotime($row['waktu_pesan']))) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr><th colspan="4">Total</th><th><?= $total_tiket ?></th><th><?= $total_pendapatan ?></th><th colspan="2"></th></tr>
    </table>
    <?php
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Pemesanan Tiket</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; margin: 32px; }
        h1 { margin-bottom: 4px; }
        .meta { color: #555; margin-bottom: 24px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background: #e9ecef; }
        .number { text-align: right; }
        .summary { margin: 18px 0; font-weight: bold; }
        .actions { margin-bottom: 20px; }
        .actions button { padding: 8px 14px; cursor: pointer; }
        @media print {
            .actions { display: none; }
            body { margin: 10mm; }
            h1 { font-size: 18px; }
            table { font-size: 10px; }
        }
    </style>
</head>
<body>
    <div class="actions"><button type="button" onclick="window.print()">Cetak / Simpan sebagai PDF</button></div>
    <h1>Rekap Pemesanan Tiket</h1>
    <div class="meta"><?= htmlspecialchars($judul) ?> | Dicetak <?= date('d-m-Y H:i') ?></div>
    <div class="summary">Total tiket: <?= number_format($total_tiket) ?> &nbsp; | &nbsp; Total pendapatan: Rp <?= number_format($total_pendapatan) ?></div>
    <table>
        <thead>
            <tr><th>No</th><th>Kode Tiket</th><th>Nama Siswa</th><th>Event</th><th>Jumlah</th><th>Total Harga</th><th>Metode Bayar</th><th>Waktu</th></tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $index => $row): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($row['kode_unik'] ?? '-') ?></td>
                <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                <td><?= htmlspecialchars($row['nama_event']) ?></td>
                <td class="number"><?= (int) $row['jumlah'] ?></td>
                <td class="number">Rp <?= number_format((int) $row['total_harga']) ?></td>
                <td><?= htmlspecialchars($row['metode_bayar'] ?? '-') ?></td>
                <td><?= htmlspecialchars(date('d-m-Y H:i', strtotime($row['waktu_pesan']))) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="8" style="text-align:center">Belum ada transaksi pada periode ini.</td></tr><?php endif; ?>
        </tbody>
    </table>
    <script>window.addEventListener('load', function () { window.print(); });</script>
</body>
</html>
