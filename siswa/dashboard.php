<?php
session_start();
require '../koneksi.php';

/** @var mysqli $conn */
if(!isset($_SESSION['siswa'])){ 
    header("Location: ../login.php"); 
    exit; 
}

$id_siswa = $_SESSION['siswa'];

$q_siswa = mysqli_query($conn, "SELECT nama FROM siswa WHERE id='$id_siswa'");
$nama_siswa = mysqli_fetch_assoc($q_siswa)['nama'];

$today = date('Y-m-d');

/* EVENT SOROTAN */
$q_sorotan = mysqli_query($conn, "SELECT * FROM events 
    WHERE sorotan='yes' 
    AND (
        CASE 
            WHEN tanggal_akhir IS NOT NULL 
            AND tanggal_akhir != '0000-00-00' 
            THEN tanggal_akhir 
            ELSE tanggal 
        END
    ) >= '$today' 
    ORDER BY id DESC LIMIT 5");

$sorotan_data = [];

while($row = mysqli_fetch_assoc($q_sorotan)){
    $sorotan_data[] = $row;
}

/* SEMUA EVENT */
$events = mysqli_query($conn, "SELECT * FROM events 
    WHERE (
        CASE 
            WHEN tanggal_akhir IS NOT NULL 
            AND tanggal_akhir != '0000-00-00' 
            THEN tanggal_akhir 
            ELSE tanggal 
        END
    ) >= '$today'
    ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman siswa</title>
    <meta http-equiv="refresh" content="15">

    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/css2.css" rel="stylesheet">

    <style>
        :root{
            --primary:#1E3A8A;
            --secondary:#DC2626;
            --light:#F8FAFC;
            --dark:#0F172A;
            --soft:#E2E8F0;
        }

        *{
            font-family:'Plus Jakarta Sans',sans-serif;
        }

        body{
            background:linear-gradient(to bottom,#F8FAFC,#EEF2FF);
            color:var(--dark);
        }

        /* NAVBAR */
        .navbar{
            background:rgba(255,255,255,0.93);
            backdrop-filter:blur(14px);
            box-shadow:0 4px 20px rgba(0,0,0,0.05);
            border-bottom:1px solid rgba(0,0,0,0.04);
        }

        .navbar-brand{
            color:var(--primary)!important;
            font-weight:800;
            letter-spacing:.3px;
        }

        .logo-container img{
            height:52px;
            width:auto;
            object-fit:contain;
        }

        /* HERO */
        #heroCarousel{
            border-radius:36px;
            overflow:hidden;
            box-shadow:0 25px 60px rgba(30,58,138,0.18);
            margin-bottom:60px;
        }

        .carousel-item{
            height:540px;
        }

        .hero-img{
            width:100%;
            height:100%;
            object-fit:cover;
            transform:scale(1);
            transition:transform 8s ease;
            filter:brightness(0.72);
        }

        .carousel-item.active .hero-img{
            transform:scale(1.08);
        }

        .hero-overlay{
            position:absolute;
            inset:0;
            display:flex;
            align-items:end;
            padding:60px;
            background:
            linear-gradient(
                to top,
                rgba(15,23,42,0.96),
                rgba(15,23,42,0.45),
                transparent
            );
            color:white;
        }

        .hero-content{
            max-width:720px;
        }

        .badge-unggulan{
            background:linear-gradient(135deg,var(--secondary),#EF4444);
            border:none;
            padding:10px 18px;
            border-radius:999px;
            font-size:.8rem;
            letter-spacing:.5px;
            margin-bottom:18px;
            display:inline-block;
            font-weight:700;
        }

        .hero-title{
            font-size:4rem;
            font-weight:800;
            line-height:1;
            margin-bottom:18px;
            text-transform:uppercase;
        }

        .hero-info{
            font-size:1rem;
            opacity:.85;
            margin-bottom:18px;
        }

        .hero-desc{
            font-size:1.05rem;
            line-height:1.8;
            opacity:.88;
            max-width:650px;
            margin-bottom:28px;
        }

        /* BUTTON */
        .btn-primary{
            background:linear-gradient(135deg,var(--primary),#2563EB);
            border:none;
            border-radius:999px;
            font-weight:700;
            transition:.3s;
        }

        .btn-primary:hover:not(:disabled){
            transform:translateY(-3px);
            box-shadow:0 12px 30px rgba(37,99,235,.35);
        }

        .btn-outline-primary{
            border:2px solid var(--primary);
            color:var(--primary);
            border-radius:999px;
            font-weight:700;
            transition:.3s;
        }

        .btn-outline-primary:hover:not(:disabled){
            background:var(--primary);
            color:white;
            transform:translateY(-2px);
        }

        /* SECTION */
        .section-title{
            font-weight:800;
            margin-bottom:28px;
            color:var(--dark);
        }

        /* CARD */
        .card-event{
            border:none;
            border-radius:28px;
            overflow:hidden;
            background:white;
            transition:.35s;
            box-shadow:0 10px 30px rgba(0,0,0,0.05);
        }

        .card-event:hover{
            transform:translateY(-12px);
            box-shadow:0 25px 50px rgba(30,58,138,0.12);
        }

        .card-event img{
            transition:.5s;
        }

        .card-event:hover img{
            transform:scale(1.05);
        }

        .badge-date{
            background:rgba(37,99,235,0.12);
            color:#2563EB;
            font-weight:700;
            font-size:.78rem;
        }

        .event-desc{
            color:#64748B;
            line-height:1.7;
            font-size:.92rem;
            margin-top:10px;
            margin-bottom:20px;
        }

        /* MODAL */
        .modal-content{
            border-radius:30px!important;
        }

        .bg-light{
            background:#F8FAFC!important;
        }

        /* CAROUSEL */
        .carousel-indicators [data-bs-target]{
            width:12px;
            height:12px;
            border-radius:50%;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon{
            background-color:rgba(255,255,255,.2);
            border-radius:50%;
            padding:22px;
            backdrop-filter:blur(10px);
        }

        /* RESPONSIVE */
        @media(max-width:768px){
            .hero-title{ font-size:2.2rem; }
            .hero-overlay{ padding:30px; }
            .carousel-item{ height:430px; }
            .hero-desc{ font-size:.92rem; }
        }
    </style>
</head>

<body>

    <nav class="navbar px-4 py-3 sticky-top">
        <div class="container-fluid justify-content-start gap-3">
            <button class="btn btn-light border-0 shadow-sm"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#sidebarSiswa">
                ☰
            </button>

            <a class="navbar-brand m-0">
             Sistem Pemesanan Tiket Event Sekolah Berbasis Web
            </a>

            <div class="ms-auto d-flex align-items-center">
                <div class="logo-container">
                    <img src="../assets/img/antartika.png" alt="Logo">
                </div>
            </div>
        </div>
    </nav>

    <?php include 'sidebar.php'; ?>

    <div class="container mt-5 mb-5">

        <?php if(count($sorotan_data) > 0): ?>
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php foreach($sorotan_data as $index => $s): ?>
                    <button type="button"
                            data-bs-target="#heroCarousel"
                            data-bs-slide-to="<?= $index ?>"
                            class="<?= ($index == 0) ? 'active' : '' ?>">
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="carousel-inner">
                <?php foreach($sorotan_data as $index => $s): 
                    $tgl_display_s = date('d M Y', strtotime($s['tanggal']));
                    if(!empty($s['tanggal_akhir']) && $s['tanggal_akhir'] != '0000-00-00' && $s['tanggal_akhir'] != $s['tanggal']) {
                        $tgl_display_s .= ' - ' . date('d M Y', strtotime($s['tanggal_akhir']));
                    }

                    $jam_display_s = date('H:i', strtotime($s['jam_mulai']));
                    if(!empty($s['jam_selesai']) && $s['jam_selesai'] != '00:00:00') {
                        $jam_display_s .= ' - ' . date('H:i', strtotime($s['jam_selesai']));
                    }
                ?>

                <div class="carousel-item <?= ($index == 0) ? 'active' : '' ?>" data-bs-interval="4000">
                    <img src="../assets/img/<?= $s['gambar'] ?>" class="hero-img">
                    <div class="hero-overlay">
                        <div class="hero-content">
                            <span class="badge-unggulan">Trending Event</span>
                            <h1 class="hero-title"><?= $s['judul'] ?></h1>
                            <p class="hero-info">
                                📍 <?= $s['lokasi'] ?> &nbsp;&nbsp;|&nbsp;&nbsp;
                                📅 <?= $tgl_display_s ?> &nbsp;&nbsp;|&nbsp;&nbsp;
                                🕒 <?= $jam_display_s ?> 
                            </p>
                            <p class="hero-desc">
                                <?= substr(strip_tags($s['deskripsi']),0,180) ?>...
                            </p>

                            <?php if($s['slot_tiket'] > 0): ?>
                                <button class="btn btn-primary px-5 py-3 shadow-lg"
                                        onclick="bukaModalPesan('<?= $s['id'] ?>', '<?= htmlspecialchars($s['judul'], ENT_QUOTES) ?>', '<?= $s['harga'] ?>', '<?= $s['slot_tiket'] ?>')">
                                    Dapatkan Tiket Sekarang
                                </button>
                            <?php else: ?>
                                <button class="btn btn-secondary px-5 py-3 shadow-lg" disabled>
                                    Maaf, Tiket Telah Habis
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
        <?php endif; ?>

        <h4 class="section-title">Jelajahi Event Sekolah Lainnya</h4>
        <div class="row">
            <?php while($e = mysqli_fetch_assoc($events)): 
                $tgl_display_e = date('d M Y', strtotime($e['tanggal']));
                if(!empty($e['tanggal_akhir']) && $e['tanggal_akhir'] != '0000-00-00' && $e['tanggal_akhir'] != $e['tanggal']) {
                    $tgl_display_e .= ' - ' . date('d M Y', strtotime($e['tanggal_akhir']));
                }

                $jam_display_e = date('H:i', strtotime($e['jam_mulai']));
                if(!empty($e['jam_selesai']) && $e['jam_selesai'] != '00:00:00') {
                    $jam_display_e .= ' - ' . date('H:i', strtotime($e['jam_selesai']));
                }
            ?>

            <div class="col-md-4 mb-4">
                <div class="card card-event h-100">
                    <img src="../assets/img/<?= $e['gambar'] ?>" class="card-img-top" style="height:220px; object-fit:cover;">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="badge badge-date px-3 py-2 rounded-pill mb-3 align-self-start">
                            📅 <?= $tgl_display_e ?>
                        </div>

                        <h5 class="fw-bold text-dark mb-1"><?= $e['judul'] ?></h5>
                        <p class="text-muted small mb-1">📍 <?= $e['lokasi'] ?></p>
                        <p class="text-primary small fw-bold mb-2">🕒 <?= $jam_display_e ?> </p>
                        <p class="event-desc">
                            <?= substr(strip_tags($e['deskripsi']),0,90) ?>...
                        </p>

                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold text-primary fs-5">
                                    <?= ($e['harga'] == 0) ? '<span class="text-success">Gratis</span>' : 'Rp' . number_format($e['harga']) ?>
                                </span>
                                <span class="small text-muted">Slot: <?= $e['slot_tiket'] ?></span>
                            </div>

                            <?php if($e['slot_tiket'] > 0): ?>
                                <button class="btn btn-outline-primary w-100 py-2"
                                        onclick="bukaModalPesan('<?= $e['id'] ?>', '<?= htmlspecialchars($e['judul'], ENT_QUOTES) ?>', '<?= $e['harga'] ?>', '<?= $e['slot_tiket'] ?>')">
                                    Pesan Tiket
                                </button>
                            <?php else: ?>
                                <button class="btn btn-light text-muted w-100 py-2 border" disabled>
                                    Maaf, Tiket Telah Habis
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="modal fade" id="modalPesan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="fw-bold m-0 text-dark">Konfirmasi Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="proses_beli.php" method="POST">
                    <div class="modal-body px-4">
                        <input type="hidden" name="event_id" id="eventId">
                        <input type="hidden" name="harga_satuan" id="hargaSatuan">
                        <h4 class="fw-bold mb-4" id="namaEvent"></h4>

                        <div class="bg-light p-3 rounded-4 mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-secondary">Jumlah Tiket</span>
                                <div class="input-group" style="width:140px;">
                                    <button class="btn btn-white border fw-bold shadow-sm" type="button" onclick="ubahJumlah(-1)">-</button>
                                    <input type="number" name="jumlah" id="jumlahTiket" class="form-control text-center fw-bold border-0 bg-white" value="1" readonly>
                                    <button class="btn btn-white border fw-bold shadow-sm" type="button" onclick="ubahJumlah(1)">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between px-2">
                            <span class="text-muted fs-5">Total Pembayaran</span>
                            <h3 class="fw-bold text-primary m-0" id="totalHarga">Rp0</h3>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4">
                        <button type="submit" name="bayar" class="btn btn-primary w-100 py-3 shadow">Konfirmasi & Bayar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>

    <script>
        let maxSlot = 0;
        let hargaAwal = 0;

        function bukaModalPesan(id, nama, harga, slot){
            maxSlot = parseInt(slot);
            hargaAwal = parseInt(harga);

            if(maxSlot <= 0){
                alert("Maaf, tiket habis terjual!");
                return;
            }

            document.getElementById('eventId').value = id;
            document.getElementById('namaEvent').innerText = nama;
            document.getElementById('hargaSatuan').value = harga;
            document.getElementById('jumlahTiket').value = 1;

            updateTotal();

            new bootstrap.Modal(document.getElementById('modalPesan')).show();
        }

        function ubahJumlah(n){
            let input = document.getElementById('jumlahTiket');
            let res = parseInt(input.value) + n;

            if(res >= 1 && res <= maxSlot){
                input.value = res;
                updateTotal();
            }
        }

        function updateTotal(){
            let total = parseInt(document.getElementById('jumlahTiket').value) * hargaAwal;
            let displayTotal = document.getElementById('totalHarga');

            if(total === 0) {
                displayTotal.innerText = 'Gratis';
                displayTotal.classList.replace('text-primary', 'text-success');
            } else {
                displayTotal.innerText = 'Rp ' + total.toLocaleString('id-ID');
                displayTotal.classList.replace('text-success', 'text-primary');
            }
        }
    </script>
</body>
</html>