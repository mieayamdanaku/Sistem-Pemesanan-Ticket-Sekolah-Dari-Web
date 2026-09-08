<?php
session_start();
require '../koneksi.php';
/** @var mysqli $conn */

// Proteksi Admin
if(!isset($_SESSION['admin'])){ header("Location: ../login.php"); exit; }

/* 
  CATATAN STRUKTUR DATABASE:
  Jika tabel petugas di database Anda belum memiliki kolom 'nama', jalankan query SQL berikut di phpMyAdmin Anda:
  ALTER TABLE petugas ADD COLUMN nama VARCHAR(255) NOT NULL AFTER username;
*/

$pesan = "";

// Logika Tambah Petugas
if(isset($_POST['tambah_petugas'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $password_raw = $_POST['password'];
    
    $password_hash = password_hash($password_raw, PASSWORD_DEFAULT); 

    $cek = mysqli_query($conn, "SELECT * FROM petugas WHERE username='$username'");
    if(mysqli_num_rows($cek) > 0){
        $pesan = "<div class='alert alert-danger border-0 shadow-sm rounded-4 mb-4'>Petugas dengan username tersebut sudah terdaftar!</div>";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO petugas (username, nama, password) VALUES ('$username', '$nama', '$password_hash')");
        if($insert) {
            $pesan = "<div class='alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center'><span class='fs-4 me-3'>✅</span><div><strong>Berhasil!</strong> Data petugas baru ditambahkan ke sistem.</div></div>";
        } else {
            $pesan = "<div class='alert alert-danger border-0 shadow-sm rounded-4 mb-4'>Gagal menyimpan ke database. Pastikan kolom 'nama' sudah ditambahkan ke tabel petugas.</div>";
        }
    }
}

// Logika Edit Petugas
if(isset($_POST['edit_petugas'])){
    $id_edit = $_POST['id_petugas'];
    $username_baru = mysqli_real_escape_string($conn, $_POST['username_baru']);
    $nama_baru = mysqli_real_escape_string($conn, $_POST['nama_baru']);
    $password_baru = $_POST['password_baru'];

    // Cek apakah username baru bentrok dengan petugas lain
    $cek_username = mysqli_query($conn, "SELECT id FROM petugas WHERE username='$username_baru' AND id != '$id_edit'");
    
    if(mysqli_num_rows($cek_username) > 0){
        $pesan = "<div class='alert alert-danger border-0 shadow-sm rounded-4 mb-4'>Username tersebut sudah dipakai oleh petugas lain!</div>";
    } else {
        // Jika password diisi, update dengan password baru. Jika kosong, biarkan password lama.
        if(!empty($password_baru)){
            $pass_hash = password_hash($password_baru, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE petugas SET username='$username_baru', nama='$nama_baru', password='$pass_hash' WHERE id='$id_edit'");
        } else {
            mysqli_query($conn, "UPDATE petugas SET username='$username_baru', nama='$nama_baru' WHERE id='$id_edit'");
        }
        $pesan = "<div class='alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center'><span class='fs-4 me-3'>✅</span><div><strong>Berhasil!</strong> Data petugas berhasil diperbarui.</div></div>";
    }
}

// Logika Hapus Petugas
if(isset($_GET['hapus'])){
    $id_hapus = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM petugas WHERE id='$id_hapus'");
    header("Location: tambah_petugas.php");
    exit;
}

// Ambil Data Seluruh Petugas
$petugas = mysqli_query($conn, "SELECT * FROM petugas ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="60">
    <title>Administrator Panel - Manajemen Petugas</title>
    
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/css2.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F8FAFC; color: #1E293B; }
        
        /* Navbar Mirroring */
        .navbar { background: white; border-bottom: 1px solid #E2E8F0; }
        .navbar-brand { color: #2563EB !important; font-weight: 800; font-size: 1.25rem; }
        .btn-menu { background: #F8FAFC; border: none; padding: 8px 12px; border-radius: 8px; transition: 0.2s; }
        .btn-menu:hover { background: #E2E8F0; }

        /* Card Pro Style */
        .card-pro { border: none; border-radius: 24px; background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
        
        .form-label { font-weight: 700; color: #64748B; font-size: 0.85rem; margin-bottom: 10px; }
        .form-control { 
            border-radius: 16px; padding: 14px 20px; border: 1px solid #E2E8F0; background: #FBFDFF; transition: 0.2s;
        }
        .form-control:focus { box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); border-color: #2563EB; background: white; }
        
        .table-pro thead th { background: #F8FAFC; color: #64748B; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; padding: 16px; border-bottom: 2px solid #E2E8F0; }
        .table-pro tbody td { padding: 16px; border-bottom: 1px solid #F1F5F9; }
        
        .btn-gradient { 
            background: linear-gradient(135deg, #2563EB, #1D4ED8); border: none; 
            border-radius: 20px; padding: 16px; font-weight: 800; color: white; transition: 0.3s;
        }
        .btn-gradient:hover { transform: translateY(-3px); box-shadow: 0 12px 24px rgba(37, 99, 235, 0.3); color: white; }
    </style>
</head>
<body>

    <nav class="navbar px-4 py-3 sticky-top">
        <div class="container-fluid justify-content-start gap-3">
            <button class="btn-menu shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
                <span class="fw-bold">☰ Menu</span>
            </button>
            <a class="navbar-brand m-0">Administrator Panel</a>
        </div>
    </nav>

    <?php include 'sidebar.php'; ?>

    <div class="container mt-4 mb-5">
        
        <div class="mb-4 d-flex align-items-center">
            <div>
                <h4 class="fw-800 m-0">Manajemen Data Petugas Event</h4>
                <p class="text-muted small m-0">Daftarkan akun petugas baru atau kelola akun petugas yang ada.</p>
            </div>
        </div>

        <?= $pesan ?>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card card-pro p-4">
                    <h5 class="fw-800 mb-4 text-dark">Registrasi Petugas</h5>
                    <form method="POST" autocomplete="off">
                        <div class="mb-4">
                            <label class="form-label">USERNAME PETUGAS</label>
                            <input type="text" name="username" class="form-control" placeholder="Contoh: petugas_event1" autocomplete="off" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">NAMA LENGKAP</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama lengkap petugas..." autocomplete="off" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">PASSWORD</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" autocomplete="new-password" required>
                        </div>
                        <div class="mb-4">
                            <div class="alert alert-light border small text-muted">
                                💡 <strong>Catatan:</strong> Akun petugas yang didaftarkan akan digunakan untuk login ke panel pengelolaan event.
                            </div>
                        </div>
                        <button type="submit" name="tambah_petugas" class="btn btn-gradient w-100 shadow">DAFTARKAN PETUGAS</button>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-pro p-4">
                    <h5 class="fw-800 mb-4 text-dark">Database Petugas</h5>
                    <div class="table-responsive">
                        <table class="table table-pro align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Nama Petugas</th>
                                    <th class="text-center">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($petugas && mysqli_num_rows($petugas) > 0): ?>
                                    <?php while($p = mysqli_fetch_assoc($petugas)): ?>
                                    <tr>
                                        <td class="fw-bold text-primary"><?= htmlspecialchars($p['username']) ?></td>
                                        <td class="fw-600 text-dark"><?= htmlspecialchars($p['nama'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $p['id'] ?>">Edit</button>
                                            <a href="?hapus=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" onclick="return confirm('Hapus petugas ini?')">Hapus</a>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="modalEdit<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
                                                <div class="modal-header border-0 px-4 pt-4">
                                                    <h5 class="fw-bold m-0">Edit Data Petugas</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body px-4 text-start">
                                                        <input type="hidden" name="id_petugas" value="<?= $p['id'] ?>">
                                                        <div class="mb-3">
                                                            <label class="form-label">Username Petugas</label>
                                                            <input type="text" name="username_baru" class="form-control" value="<?= htmlspecialchars($p['username']) ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Lengkap</label>
                                                            <input type="text" name="nama_baru" class="form-control" value="<?= htmlspecialchars($p['nama'] ?? '') ?>" required>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label">Ganti Password (Opsional)</label>
                                                            <input type="password" name="password_baru" class="form-control" placeholder="Ketik password baru disini...">
                                                            <small class="text-muted mt-1 d-block">Biarkan kosong jika tidak ingin mengubah password petugas ini.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 p-4">
                                                        <button type="submit" name="edit_petugas" class="btn btn-primary w-100 py-3 shadow rounded-pill fw-bold">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted fw-bold">Belum ada petugas terdaftar.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>