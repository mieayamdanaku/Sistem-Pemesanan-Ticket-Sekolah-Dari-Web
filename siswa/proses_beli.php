<?php
session_start();
require '../koneksi.php';

/** @var mysqli $conn */

// VALIDASI AKSES
if(!isset($_POST['bayar'])){
    header("Location: dashboard.php");
    exit;
}

$id_siswa = $_SESSION['siswa'];

$id_event = $_POST['event_id'];
$jumlah = $_POST['jumlah'];
$harga_satuan = $_POST['harga_satuan'];

$total = $jumlah * $harga_satuan;

// GENERATE KODE UNIK
$kode_unik =
    strtoupper(
        substr(
            md5(time() . $id_siswa),
            0,
            8
        )
    );

// AMBIL DATA EVENT
$q_event = mysqli_query(
    $conn,
    "SELECT * FROM events WHERE id='$id_event'"
);

$event = mysqli_fetch_assoc($q_event);

// SIMPAN TIKET
$query = mysqli_prepare(
    $conn,
    "INSERT INTO tiket
    (siswa_id, event_id, jumlah, total_harga, kode_unik)
    VALUES (?, ?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $query,
    "iiiis",
    $id_siswa,
    $id_event,
    $jumlah,
    $total,
    $kode_unik
);

$success = false;

if(mysqli_stmt_execute($query)){

    // KURANGI SLOT TIKET

    mysqli_query(
        $conn,
        "UPDATE events
         SET slot_tiket = slot_tiket - $jumlah
         WHERE id='$id_event'"
    );

    $success = true;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Status Pembayaran</title>
    <meta http-equiv="refresh" content="15">
    <link href="../assets/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="../assets/css/css2.css" rel="stylesheet">
    
    <script src="../assets/js/qrcode.min.js"></script>

    <style>

        :root{
            --primary:#1E3A8A;
            --secondary:#DC2626;
            --light:#F8FAFC;
            --dark:#0F172A;
            --soft:#E2E8F0;
            --success:#16A34A;
        }

        *{
            font-family:'Plus Jakarta Sans',sans-serif;
        }

        body{
            min-height:100vh;

            display:flex;
            justify-content:center;
            align-items:center;

            padding:30px;

            background:
                linear-gradient(
                    135deg,
                    #EEF2FF,
                    #F8FAFC
                );
        }

        .payment-card{

            width:100%;
            max-width:520px;

            background:white;

            border-radius:38px;

            overflow:hidden;

            box-shadow:
                0 30px 70px rgba(30,58,138,0.12);

            position:relative;
        }

        .payment-top{

            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    #2563EB
                );

            padding:45px 35px;

            color:white;

            text-align:center;

            position:relative;

            overflow:hidden;
        }

        .payment-top::before{

            content:'';

            position:absolute;

            width:220px;
            height:220px;

            border-radius:50%;

            background:rgba(255,255,255,.08);

            top:-90px;
            right:-70px;
        }

        .success-icon{

            width:90px;
            height:90px;

            border-radius:50%;

            background:rgba(255,255,255,.18);

            display:flex;
            align-items:center;
            justify-content:center;

            margin:0 auto 22px;

            font-size:2.8rem;

            backdrop-filter:blur(10px);

            position:relative;
            z-index:2;
        }

        .payment-title{

            font-size:2rem;
            font-weight:800;

            margin-bottom:10px;

            position:relative;
            z-index:2;
        }

        .payment-sub{

            opacity:.88;
            font-size:.95rem;

            position:relative;
            z-index:2;
        }

        .payment-body{
            padding:35px;
        }

        .detail-box{

            background:#F8FAFC;

            border:1px solid #E2E8F0;

            border-radius:24px;

            padding:22px;

            margin-bottom:18px;
        }

        .detail-row{

            display:flex;
            justify-content:space-between;
            align-items:center;

            margin-bottom:16px;
        }

        .detail-row:last-child{
            margin-bottom:0;
        }

        .detail-label{

            color:#64748B;
            font-size:.92rem;
            font-weight:600;
        }

        .detail-value{

            color:var(--dark);
            font-weight:800;
            text-align:right;
        }

        .kode-box{

            margin-top:24px;

            background:
                linear-gradient(
                    135deg,
                    rgba(37,99,235,.08),
                    rgba(30,58,138,.04)
                );

            border-radius:24px;

            padding:24px;

            text-align:center;
        }

        .kode-label{

            font-size:.75rem;

            font-weight:800;

            color:#94A3B8;

            letter-spacing:1px;

            text-transform:uppercase;

            margin-bottom:10px;
        }

        .kode-value{

            font-size:2rem;

            font-weight:800;

            color:var(--primary);

            letter-spacing:5px;

            font-family:'Courier New',monospace;
        }

        .success-text{

            color:#16A34A;

            font-weight:700;

            margin-top:18px;
            margin-bottom:10px; /* Ditambah biar gak nempel tombol */

            text-align:center;
        }

        .btn-ticket{

            width:100%;

            border:none;

            border-radius:20px;

            padding:16px;

            margin-top:10px;

            font-weight:700;

            font-size:1rem;

            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    #2563EB
                );

            color:white;

            transition:.3s;

            box-shadow:
                0 14px 30px rgba(37,99,235,.25);

            text-decoration:none;

            display:block;

            text-align:center;
        }

        .btn-ticket:hover{

            transform:translateY(-3px);

            color:white;

            box-shadow:
                0 20px 40px rgba(37,99,235,.32);
        }
        
        /* CLASS BARU UNTUK TOMBOL KEMBALI KE DASHBOARD */
        .btn-dashboard{
            width:100%;
            border:2px solid var(--primary);
            border-radius:20px;
            padding:14px;
            margin-top:20px;
            font-weight:700;
            font-size:1rem;
            background:white;
            color:var(--primary);
            transition:.3s;
            text-decoration:none;
            display:block;
            text-align:center;
        }

        .btn-dashboard:hover{
            background:var(--primary);
            color:white;
        }

        .failed-box{

            padding:50px 35px;

            text-align:center;
        }

        .failed-icon{

            width:90px;
            height:90px;

            border-radius:50%;

            background:#FEE2E2;

            color:#DC2626;

            display:flex;
            align-items:center;
            justify-content:center;

            margin:0 auto 24px;

            font-size:2.8rem;
        }

        .failed-title{

            font-size:1.8rem;

            font-weight:800;

            margin-bottom:10px;

            color:#0F172A;
        }

        .failed-sub{

            color:#64748B;

            margin-bottom:30px;
        }

        .btn-back{

            display:inline-block;

            padding:14px 28px;

            border-radius:18px;

            text-decoration:none;

            font-weight:700;

            background:#0F172A;

            color:white;

            transition:.3s;
        }

        .btn-back:hover{

            transform:translateY(-3px);

            color:white;
        }

    </style>

</head>

<body>

<?php if($success): ?>

    <div class="payment-card">

        <div class="payment-top">

            <div class="success-icon">
                ✓
            </div>

            <div class="payment-title">
                Pembayaran Berhasil
            </div>

            <div class="payment-sub">
                Tiket event berhasil dipesan dan sudah masuk ke akun kamu.
            </div>

        </div>

        <div class="payment-body">

            <div class="detail-box">

                <div class="detail-row">

                    <div class="detail-label">
                        Event
                    </div>

                    <div class="detail-value">
                        <?= htmlspecialchars($event['judul']) ?>
                    </div>

                </div>

                <div class="detail-row">

                    <div class="detail-label">
                        Jumlah Tiket
                    </div>

                    <div class="detail-value">
                        <?= $jumlah ?> Tiket
                    </div>

                </div>

                <div class="detail-row">

                    <div class="detail-label">
                        Total Pembayaran
                    </div>

                    <div class="detail-value">
                        Rp<?= number_format($total) ?>
                    </div>

                </div>

            </div>

            <div class="kode-box">

                <div class="kode-label">
                    QRIS TIKET
                </div>

                <div id="qrcode" style="display: flex; justify-content: center; padding: 10px;"></div>

            </div>

            <div class="success-text">
                Perlihatkan Qris Ini Kepada Penyelenggara Event
            </div>
            
            <a href="dashboard.php" class="btn-dashboard">
                Kembali ke Dashboard
            </a>

            <a href="tiket_saya.php" class="btn-ticket">
                Lihat Tiket Saya
            </a>

        </div>

    </div>

<?php else: ?>

    <div class="payment-card">

        <div class="failed-box">

            <div class="failed-icon">
                ✕
            </div>

            <div class="failed-title">
                Pembayaran Gagal
            </div>

            <div class="failed-sub">
                Terjadi kesalahan saat memproses tiket event.
            </div>

            <a href="dashboard.php"
               class="btn-back">

                Kembali ke Dashboard

            </a>

        </div>

    </div>

<?php endif; ?>

<script>
    <?php if($success): ?>
    new QRCode(document.getElementById("qrcode"), {
        text: "<?= $kode_unik ?>",
        width: 160,
        height: 160
    });
    <?php endif; ?>
</script>

</body>
</html>