<?php
session_start();
require '../koneksi.php';
/** @var mysqli $conn */

if(!isset($_SESSION['admin'])){ header("Location: ../login.php"); exit; }

// 1. Ambil data lama berdasarkan ID dari URL
if(!isset($_GET['id'])){ header("Location: kelola_event.php"); exit; }
$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM events WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

$pesan = "";

// 2. Logika Update Data
if(isset($_POST['simpan'])){
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    
    // Logika Keamanan: Maksa angka positif, dan jika kosong = 0 (Gratis)
    $harga = !empty($_POST['harga']) ? abs((int)$_POST['harga']) : 0;
    $slot = abs((int)$_POST['slot']);
    
    $lokasi = $_POST['lokasi'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = !empty($_POST['jam_selesai']) ? $_POST['jam_selesai'] : NULL;
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_akhir = !empty($_POST['tanggal_akhir']) ? $_POST['tanggal_akhir'] : NULL;
    $sorotan = $_POST['sorotan'];

    // Cek apakah admin mengunggah gambar baru
    if($_FILES['gambar']['name'] != ""){
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        $nama_gambar_baru = time() . "_" . $gambar;
        
        if(file_exists("../assets/img/".$data['gambar'])) unlink("../assets/img/".$data['gambar']);
        move_uploaded_file($tmp, '../assets/img/' . $nama_gambar_baru);
        
        // Update Query (With Image) - 12 Parameter
        $update_query = "UPDATE events SET judul=?, deskripsi=?, gambar=?, harga=?, slot_tiket=?, lokasi=?, jam_mulai=?, jam_selesai=?, tanggal=?, tanggal_akhir=?, sorotan=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "sssiissssssi", $judul, $deskripsi, $nama_gambar_baru, $harga, $slot, $lokasi, $jam_mulai, $jam_selesai, $tanggal_mulai, $tanggal_akhir, $sorotan, $id);
    } else {
        // Update Query (No Image) - 11 Parameter
        $update_query = "UPDATE events SET judul=?, deskripsi=?, harga=?, slot_tiket=?, lokasi=?, jam_mulai=?, jam_selesai=?, tanggal=?, tanggal_akhir=?, sorotan=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $update_query);
        // Tipe data: 11 parameter -> ssiissssssi (s,s,i,i,s,s,s,s,s,s,i)
        mysqli_stmt_bind_param($stmt, "ssiissssssi", $judul, $deskripsi, $harga, $slot, $lokasi, $jam_mulai, $jam_selesai, $tanggal_mulai, $tanggal_akhir, $sorotan, $id);
    }

    if(mysqli_stmt_execute($stmt)){
        header("Location: kelola_event.php");
        exit;
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal memperbarui data: " . mysqli_stmt_error($stmt) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Event - Admin</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/css2.css" rel="stylesheet">
    <link href="../assets/css/flatpickr.min.css" rel="stylesheet">    <style>
        
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F8FAFC; }
        .navbar { background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .readonly-picker { cursor: pointer !important; background-color: #ffffff !important; }
    </style>
</head>
<body>

    <nav class="navbar px-4 py-3 sticky-top">
        <div class="container-fluid justify-content-start gap-3">
            <button class="btn btn-light border-0 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">☰</button>
            <a href="kelola_event.php" class="text-decoration-none fw-bold text-dark">← Batal & Kembali</a>
        </div>
    </nav>

    <?php include 'sidebar.php'; ?>

    <div class="container mt-5 mb-5">
        <?= $pesan ?>
        <div class="card p-4 mx-auto" style="max-width: 900px;">
            <h3 class="fw-bold mb-4">✏️ Edit Event</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-7">
                        <div class="mb-3">
                            <label class="fw-bold small text-muted">Judul Event</label>
                            <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($data['judul']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold small text-muted">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="8" required><?= htmlspecialchars($data['deskripsi']) ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label class="fw-bold small text-muted">Ganti Gambar <span class="fw-normal">(Opsional)</span></label>
                            <input type="file" name="gambar" class="form-control">
                            <small class="text-info">Biarkan kosong jika tidak ingin mengganti gambar.</small>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="fw-bold small text-muted">Harga (Rp)</label>
                                <input type="number" name="harga" class="form-control" value="<?= $data['harga'] ?>" placeholder="0 (Gratis)" min="0" oninput="this.value = Math.abs(this.value)">
                            </div>
                            <div class="col-6">
                                <label class="fw-bold small text-muted">Sisa Slot Tiket</label>
                                <input type="number" name="slot" class="form-control" value="<?= $data['slot_tiket'] ?>" min="1" oninput="this.value = Math.abs(this.value)" required>
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="fw-bold small text-muted">Jam Mulai</label>
                                <input type="text" name="jam_mulai" id="j_mulai" class="form-control readonly-picker" value="<?= $data['jam_mulai'] ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="fw-bold small text-muted">Jam Selesai</label>
                                <input type="text" name="jam_selesai" id="j_selesai" class="form-control readonly-picker" value="<?= $data['jam_selesai'] ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold small text-muted">Lokasi</label>
                            <input type="text" name="lokasi" class="form-control" value="<?= htmlspecialchars($data['lokasi']) ?>" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="fw-bold small text-muted">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control" value="<?= $data['tanggal'] ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="fw-bold small text-muted">Tanggal Selesai</label>
                                <input type="date" name="tanggal_akhir" class="form-control" value="<?= $data['tanggal_akhir'] ?>">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="fw-bold small text-muted">SET SEBAGAI SOROTAN EVENT ?</label>
                            <select name="sorotan" class="form-select">
                                <option value="no" <?= ($data['sorotan'] == 'no') ? 'selected' : '' ?>>Tidak</option>
                                <option value="yes" <?= ($data['sorotan'] == 'yes') ? 'selected' : '' ?>>Ya</option>
                            </select>
                        </div>
                        <button type="submit" name="simpan" class="btn btn-warning w-100 fw-bold py-3 rounded-pill">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
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