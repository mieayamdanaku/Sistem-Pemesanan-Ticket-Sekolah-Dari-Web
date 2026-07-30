<?php
session_start();
require '../koneksi.php';

/** @var mysqli $conn */
if(!isset($_SESSION['siswa'])){ 
    header("Location: ../login.php"); 
    exit; 
}

$id_siswa = $_SESSION['siswa'];
$today = date('Y-m-d');

/* TIKET AKTIF */
$q_aktif = mysqli_query($conn, "SELECT tiket.*, events.judul, events.tanggal, events.tanggal_akhir, events.lokasi, events.gambar 
    FROM tiket 
    JOIN events ON tiket.event_id = events.id 
    WHERE tiket.siswa_id = '$id_siswa' 
    AND (
        CASE 
            WHEN events.tanggal_akhir IS NOT NULL 
            AND events.tanggal_akhir != '0000-00-00' 
            THEN events.tanggal_akhir 
            ELSE events.tanggal 
        END
    ) >= '$today'
    ORDER BY tiket.id DESC");

/* RIWAYAT TIKET */
$q_history = mysqli_query($conn, "SELECT tiket.*, events.judul, events.tanggal, events.tanggal_akhir, events.lokasi, events.gambar 
    FROM tiket 
    JOIN events ON tiket.event_id = events.id 
    WHERE tiket.siswa_id = '$id_siswa' 
    AND (
        CASE 
            WHEN events.tanggal_akhir IS NOT NULL 
            AND events.tanggal_akhir != '0000-00-00' 
            THEN events.tanggal_akhir 
            ELSE events.tanggal 
        END
    ) < '$today'
    ORDER BY tiket.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tiket Saya - E-Tiket</title>
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

        /* HEADER */

        .page-title{
            font-weight:800;
            color:var(--dark);
        }

        .page-subtitle{
            color:#64748B;
        }

        /* TAB */

        .nav-pills{
            gap:10px;
        }

        .nav-pills .nav-link{
            border-radius:16px;
            font-weight:700;
            color:#64748B;
            padding:12px 24px;
            background:white;
            border:1px solid rgba(0,0,0,0.04);
            transition:.3s;
        }

        .nav-pills .nav-link:hover{
            transform:translateY(-2px);
        }

        .nav-pills .nav-link.active{
            background:linear-gradient(135deg,var(--primary),#2563EB);
            box-shadow:0 10px 25px rgba(37,99,235,.25);
            color:white;
        }

        /* CARD */

        .card-tiket{
            border:none;
            border-radius:30px;
            overflow:hidden;
            background:white;
            transition:.35s;
            cursor:pointer;
            box-shadow:0 10px 30px rgba(0,0,0,0.05);
        }

        .card-tiket:hover{
            transform:translateY(-10px);
            box-shadow:0 25px 50px rgba(30,58,138,0.12);
        }

        .img-event{
            width:150px;
            height:150px;
            object-fit:cover;
            border-radius:22px;
        }

        .badge-active{
            background:rgba(34,197,94,.12);
            color:#16A34A;
            padding:8px 16px;
            border-radius:999px;
            font-size:.72rem;
            font-weight:700;
            letter-spacing:.5px;
        }

        .badge-expired{
            background:rgba(100,116,139,.12);
            color:#64748B;
            padding:8px 16px;
            border-radius:999px;
            font-size:.72rem;
            font-weight:700;
            letter-spacing:.5px;
        }

        .event-title{
            font-weight:800;
            color:var(--dark);
        }

        .event-location{
            color:#64748B;
            font-size:.92rem;
        }

        .ticket-info{
            font-weight:700;
            color:var(--primary);
        }

        .ticket-action{
            color:#94A3B8;
            font-size:.85rem;
        }

        .expired-mode{
            filter:grayscale(1);
            opacity:.75;
            cursor:default;
            box-shadow:none;
        }

        .expired-mode:hover{
            transform:none;
            box-shadow:none;
        }

        /* EMPTY STATE */

        .empty-state{
            background:white;
            padding:60px 30px;
            border-radius:28px;
            text-align:center;
            color:#64748B;
            box-shadow:0 10px 30px rgba(0,0,0,0.04);
        }

        /* MODAL */

        .modal-content{
            border-radius:34px!important;
            overflow:hidden;
        }

        .ticket-modal-header{
            background:linear-gradient(135deg,var(--primary),#2563EB);
            color:white;
            padding:30px;
        }

        .ticket-title{
            font-weight:800;
            margin-bottom:8px;
        }

        .ticket-location{
            opacity:.85;
            font-size:.95rem;
        }

        .qr-wrapper{
            background:white;
            border-radius:24px;
            padding:20px;
            border:1px solid #E2E8F0;
            box-shadow:0 10px 25px rgba(0,0,0,0.05);
        }

        .kode-box{
            background:#F8FAFC;
            border-radius:22px;
            padding:18px;
        }

        .kode-unik-text{
            font-family:'Courier New',Courier,monospace;
            letter-spacing:3px;
            color:var(--primary);
            font-weight:800;
        }

        .btn-dark{
            border:none;
            border-radius:999px;
            font-weight:700;
            background:linear-gradient(135deg,var(--dark),#334155);
        }

        .btn-dark:hover{
            transform:translateY(-2px);
        }

        /* RESPONSIVE */

        @media(max-width:768px){

            .card-tiket{
                flex-direction:column!important;
                text-align:center;
            }

            .img-event{
                width:100%;
                height:220px;
            }
        }

    </style>

</head>

<body>

    <!-- NAVBAR -->

    <nav class="navbar px-4 py-3 sticky-top">

        <div class="container-fluid justify-content-start gap-3">

            <button class="btn btn-light border-0 shadow-sm"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#sidebarSiswa">

                ☰

            </button>

            <a class="navbar-brand m-0">
                Tiket Saya
            </a>

        </div>

    </nav>

    <?php include 'sidebar.php'; ?>

    <div class="container mt-5 mb-5">

        <!-- HEADER -->
        <!-- TAB -->

        <ul class="nav nav-pills mb-4"
            id="pills-tab"
            role="tablist">

            <li class="nav-item" role="presentation">

                <button class="nav-link active"
                        id="pills-aktif-tab"
                        data-bs-toggle="pill"
                        data-bs-target="#pills-aktif"
                        type="button"
                        role="tab">

                    Tiket Aktif

                </button>

            </li>

            <li class="nav-item" role="presentation">

                <button class="nav-link"
                        id="pills-history-tab"
                        data-bs-toggle="pill"
                        data-bs-target="#pills-history"
                        type="button"
                        role="tab">

                    Tiket EXPIRED
                </button>

            </li>

        </ul>

        <!-- CONTENT -->

        <div class="tab-content" id="pills-tabContent">

            <!-- AKTIF -->

            <div class="tab-pane fade show active"
                 id="pills-aktif"
                 role="tabpanel">

                <div class="row">

                    <?php if(mysqli_num_rows($q_aktif) > 0): ?>

                        <?php while($t = mysqli_fetch_assoc($q_aktif)): ?>

                        <div class="col-md-6 mb-4">

                            <div class="card card-tiket p-3 d-flex flex-row align-items-center gap-3"

                                 onclick="bukaTiket(
                                    '<?= $t['judul'] ?>',
                                    '<?= $t['kode_unik'] ?>',
                                    '<?= $t['jumlah'] ?>',
                                    '<?= $t['lokasi'] ?>'
                                 )">

                                <img src="../assets/img/<?= $t['gambar'] ?>"
                                     class="img-event">

                                <div class="flex-grow-1">

                                    <span class="badge-active">
                                        ACTIVE
                                    </span>

                                    <h5 class="event-title mt-3 mb-2">
                                        <?= $t['judul'] ?>
                                    </h5>

                                    <p class="event-location mb-3">
                                        📍 <?= $t['lokasi'] ?>
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center">

                                        <span class="ticket-info">
                                            <?= $t['jumlah'] ?> Tiket
                                        </span>

                                        <span class="ticket-action">
                                            Klik Detail →
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <div class="col-12">

                            <div class="empty-state">

                                Belum ada tiket aktif untuk saat ini.

                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

            <!-- HISTORY -->

            <div class="tab-pane fade"
                 id="pills-history"
                 role="tabpanel">

                <div class="row">

                    <?php if(mysqli_num_rows($q_history) > 0): ?>

                        <?php while($th = mysqli_fetch_assoc($q_history)): ?>

                        <div class="col-md-6 mb-4">

                            <div class="card card-tiket expired-mode p-3 border d-flex flex-row align-items-center gap-3">

                                <img src="../assets/img/<?= $th['gambar'] ?>"
                                     class="img-event">

                                <div class="flex-grow-1">

                                    <span class="badge-expired">
                                        EXPIRED
                                    </span>

                                    <h5 class="event-title mt-3 mb-2">
                                        <?= $th['judul'] ?>
                                    </h5>

                                    <p class="text-muted small mb-1">
                                        Selesai pada:
                                    </p>

                                    <p class="fw-bold small mb-0">
                                        <?= date('d M Y', strtotime($th['tanggal_akhir'] ?: $th['tanggal'])) ?>
                                    </p>

                                </div>

                            </div>

                        </div>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <div class="col-12">

                            <div class="empty-state">

                                Belum ada riwayat tiket.

                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>


    <!-- MODAL -->

    <div class="modal fade"
         id="modalTiket"
         tabindex="-1"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-sm">

            <div class="modal-content border-0 shadow-lg">

                <div class="ticket-modal-header text-center">

                    <h5 class="ticket-title"
                        id="mJudul">
                    </h5>

                    <p class="ticket-location m-0"
                       id="mLokasi">
                    </p>

                </div>

                <div class="p-4 text-center">

                    <div class="qr-wrapper mb-4">

                        <div id="qrcode"
                             class="d-flex justify-content-center">
                        </div>

                    </div>

                    <div class="kode-box mb-3">

                        <div class="text-muted small fw-bold mb-2">
                            KODE UNIK
                        </div>

                        <h4 class="kode-unik-text m-0"
                            id="mKode">
                        </h4>

                    </div>

                    <div class="fw-bold text-dark small"
                         id="mJumlah">
                    </div>

                    <button type="button"
                            class="btn btn-dark w-100 mt-4 py-3 shadow"
                            data-bs-dismiss="modal">

                        Tutup Tiket

                    </button>

                </div>

            </div>

        </div>

    </div>


    <script src="../assets/js/bootstrap.bundle.min.js"></script>

    <script src="../assets/js/qrcode.min.js"></script>

    <script>

        let qrContainer = document.getElementById("qrcode");

        let qrcode = new QRCode(qrContainer, {
            width:180,
            height:180,
            colorDark:"#1E293B",
            colorLight:"#FFFFFF",
            correctLevel:QRCode.CorrectLevel.H
        });

        function bukaTiket(judul, kode, jumlah, lokasi){

            document.getElementById('mJudul').innerText = judul;

            document.getElementById('mKode').innerText = kode;

            document.getElementById('mJumlah').innerText =
                "Jumlah Tiket: " + jumlah + " Tiket";

            document.getElementById('mLokasi').innerText =
                "📍 " + lokasi;

            qrcode.clear();

            qrcode.makeCode(kode);

            let modal = new bootstrap.Modal(
                document.getElementById('modalTiket')
            );

            modal.show();
        }

    </script>

</body>
</html>