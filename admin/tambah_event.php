<?php
session_start();
require '../koneksi.php';
/** @var mysqli $conn */

// Proteksi Admin
if(!isset($_SESSION['admin'])){ header("Location: ../login.php"); exit; }

$pesan = "";

if(isset($_POST['publikasikan'])){
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    
    // Logika Baru: Jika harga tidak diisi atau kosong, otomatis set jadi 0 (Gratis)
    $harga = !empty($_POST['harga']) ? abs((int)$_POST['harga']) : 0;
    $slot = abs((int)$_POST['slot']);
    
    $lokasi = $_POST['lokasi'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = !empty($_POST['jam_selesai']) ? $_POST['jam_selesai'] : NULL;
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_akhir = !empty($_POST['tanggal_akhir']) ? $_POST['tanggal_akhir'] : NULL;
    $sorotan = $_POST['sorotan'];

    // Process Gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $nama_gambar_baru = time() . "_" . $gambar;

    if(move_uploaded_file($tmp, '../assets/img/' . $nama_gambar_baru)){
        $stmt = mysqli_prepare($conn, "INSERT INTO events (judul, deskripsi, gambar, harga, slot_tiket, lokasi, jam_mulai, jam_selesai, tanggal, tanggal_akhir, sorotan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssiissssss", $judul, $deskripsi, $nama_gambar_baru, $harga, $slot, $lokasi, $jam_mulai, $jam_selesai, $tanggal_mulai, $tanggal_akhir, $sorotan);
        
        if(mysqli_stmt_execute($stmt)){
            $pesan = "
            <div class='alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center mb-4'>
                <span class='fs-4 me-3'>✅</span>
                <div><strong>Berhasil!</strong> Event sudah dipublikasikan.</div>
            </div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Panel - Tambah Event</title>
    
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/css2.css" rel="stylesheet">
    <link href="../assets/css/flatpickr.min.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F8FAFC; color: #1E293B; }
        .navbar { background: white; border-bottom: 1px solid #E2E8F0; }
        .card-pro { border: none; border-radius: 24px; background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
        .form-label { font-weight: 700; color: #64748B; font-size: 0.85rem; margin-bottom: 10px; }
        .form-control, .form-select { 
            border-radius: 16px; padding: 14px 20px; border: 1px solid #E2E8F0; background: #FBFDFF; transition: 0.2s;
        }
        .form-control:focus { box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); border-color: #2563EB; background: white; }
        .btn-gradient { 
            background: linear-gradient(135deg, #2563EB, #1D4ED8); border: none; 
            border-radius: 20px; padding: 18px; font-weight: 800; color: white; transition: 0.3s;
        }
        .btn-gradient:hover { transform: translateY(-3px); box-shadow: 0 12px 24px rgba(37, 99, 235, 0.3); color: white; }
        .readonly-picker { cursor: pointer !important; background-color: #ffffff !important; }
    </style>
</head>
<body>

    <nav class="navbar px-4 py-3 sticky-top">
        <div class="container-fluid justify-content-start gap-3">
            <button class="btn btn-light border-0 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
                ☰ Menu
            </button>
            <a class="navbar-brand fw-bold text-primary m-0">Administrator Panel</a>
        </div>
    </nav>

    <?php include 'sidebar.php'; ?>

    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-800 m-0">Tambah Event</h4>
                        <p class="text-muted small m-0">Masukkan Event Baru Yang Akan Dibuat</p>
                    </div>
                </div>

                <?= $pesan ?>
                
                <div class="card card-pro p-4 p-md-5">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row g-5">
                            <div class="col-md-7">
                                <div class="mb-4">
                                    <label class="form-label">JUDUL EVENT</label>
                                    <input type="text" name="judul" class="form-control" placeholder="Nama acara..." required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">DESKRIPSI ACARA</label>
                                    <textarea name="deskripsi" class="form-control" rows="11" placeholder="Detail..." required></textarea>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">LOKASI PELAKSANAAN</label>
                                    <input type="text" name="lokasi" class="form-control" placeholder="Tempat..." required>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="mb-4">
                                    <label class="form-label">POSTER / GAMBAR</label>
                                    <input type="file" name="gambar" class="form-control" required>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <label class="form-label">HARGA (RP)</label>
                                        <input type="number" name="harga" class="form-control" placeholder="0 (Kosongkan jika gratis)" min="0" oninput="this.value = Math.abs(this.value)">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">SLOT TIKET</label>
                                        <input type="number" name="slot" class="form-control" placeholder="0" min="1" oninput="this.value = Math.abs(this.value)" required>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <label class="form-label">JAM MULAI</label>
                                        <input type="text" name="jam_mulai" id="j_mulai" class="form-control readonly-picker" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">JAM SELESAI</label>
                                        <input type="text" name="jam_selesai" id="j_selesai" class="form-control readonly-picker">
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <label class="form-label">TGL MULAI</label>
                                        <input type="date" name="tanggal_mulai" class="form-control" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">TGL AKHIR</label>
                                        <input type="date" name="tanggal_akhir" class="form-control">
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label">SET SEBAGAI SOROTAN EVENT ?</label>
                                    <select name="sorotan" class="form-select">
                                        <option value="no">Tidak</option>
                                        <option value="yes">Ya</option>
                                    </select>
                                </div>

                                <button type="submit" name="publikasikan" class="btn btn-gradient w-100 shadow">
                                    TAMBAHKAN EVENT
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/flatpickr.js"></script>
    <script>
        const fpConfig = {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            allowInput: false
        };
        flatpickr("#j_mulai", fpConfig);
        flatpickr("#j_selesai", fpConfig);
    </script>
</body>
</html>