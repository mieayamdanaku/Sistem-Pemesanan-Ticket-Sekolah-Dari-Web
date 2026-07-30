<?php
session_start();
require '../koneksi.php';
/** @var mysqli $conn */

// Proteksi Admin
if(!isset($_SESSION['admin'])){ header("Location: ../login.php"); exit; }

$pesan = "";
$mode_edit = false;
$edit_data = [];

// 1. Logika Ambil Data untuk Mode Edit (Jika tombol edit diklik)
if(isset($_GET['edit'])){
    $id_edit = mysqli_real_escape_string($conn, $_GET['edit']);
    $q_edit = mysqli_query($conn, "SELECT * FROM admin WHERE id='$id_edit'");
    if(mysqli_num_rows($q_edit) > 0) {
        $mode_edit = true;
        $edit_data = mysqli_fetch_assoc($q_edit);
    }
}

// 2. Logika Hapus Admin
if(isset($_GET['hapus'])){
    $id_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);
    $delete = mysqli_query($conn, "DELETE FROM admin WHERE id='$id_hapus'");
    if($delete) {
        header("Location: kelola_admin.php?status=hapus_sukses");
        exit;
    }
}

// Redirect Feedback Notification
if(isset($_GET['status'])) {
    if($_GET['status'] == 'hapus_sukses') {
        $pesan = "<div class='alert alert-success border-0 shadow-sm rounded-4 mb-4'>✅ Akun admin berhasil dihapus!</div>";
    } elseif($_GET['status'] == 'update_sukses') {
        $pesan = "<div class='alert alert-success border-0 shadow-sm rounded-4 mb-4'>✅ Data admin berhasil diperbarui!</div>";
    }
}

// 3. Logika Proses Form (Tambah ATAU Update)
if(isset($_POST['proses_admin'])){
    $action = $_POST['action'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    
    if($action == 'tambah') {
        // PROSES TAMBAH BARU
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $cek = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");
        
        if(mysqli_num_rows($cek) > 0){
            $pesan = "<div class='alert alert-danger border-0 shadow-sm rounded-4 mb-4'>Username sudah digunakan!</div>";
        } else {
            $insert = mysqli_query($conn, "INSERT INTO admin (username, password, nama) VALUES ('$username', '$password', '$nama')");
            if($insert) $pesan = "<div class='alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center'><span class='fs-4 me-3'>✅</span><div><strong>Berhasil!</strong> Akun admin baru berhasil ditambahkan.</div></div>";
        }
    } elseif($action == 'edit') {
        // PROSES UPDATE/EDIT
        $id_update = mysqli_real_escape_string($conn, $_POST['id_admin']);
        
        // Cek jika username kembar dengan akun lain
        $cek_username = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username' AND id != '$id_update'");
        if(mysqli_num_rows($cek_username) > 0) {
            $pesan = "<div class='alert alert-danger border-0 shadow-sm rounded-4 mb-4'>Username sudah dipakai oleh admin lain!</div>";
        } else {
            // Jika kolom password diisi, ganti password baru. Jika kosong, pertahankan yang lama
            if(!empty($_POST['password'])) {
                $password_baru = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $update = mysqli_query($conn, "UPDATE admin SET nama='$nama', username='$username', password='$password_baru' WHERE id='$id_update'");
            } else {
                $update = mysqli_query($conn, "UPDATE admin SET nama='$nama', username='$username' WHERE id='$id_update'");
            }
            
            if($update) {
                header("Location: kelola_admin.php?status=update_sukses");
                exit;
            }
        }
    }
}

// Ambil Seluruh Data Admin untuk Tabel
$admins = mysqli_query($conn, "SELECT * FROM admin ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="refresh" content="15">
    <title>Administrator Panel - Kelola Admin</title>
    
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
        
        .table-pro thead th { background: #F8FAFC; color: #64748B; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; border-bottom: 2px solid #E2E8F0; padding: 16px; }
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
                <h4 class="fw-800 m-0">Kelola Data Administrator</h4>
                <p class="text-muted small m-0">Daftarkan Admin Baru atau kelola akun Admin</p>
            </div>
        </div>

        <?= $pesan ?>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card card-pro p-4">
                    <h5 class="fw-800 mb-4 text-dark"><?= $mode_edit ? '✏️ Perbarui Admin' : ' Daftarkan Admin' ?></h5>
                    
                    <form method="POST" autocomplete="off">
                        <input type="hidden" name="action" value="<?= $mode_edit ? 'edit' : 'tambah' ?>">
                        <?php if($mode_edit): ?>
                            <input type="hidden" name="id_admin" value="<?= $edit_data['id'] ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label">NAMA </label>
                            <input type="text" name="nama" class="form-control" placeholder="...." value="<?= $mode_edit ? htmlspecialchars($edit_data['nama']) : '' ?>" autocomplete="off" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">USERNAME</label>
                            <input type="text" name="username" class="form-control" placeholder="....." value="<?= $mode_edit ? htmlspecialchars($edit_data['username']) : '' ?>" autocomplete="off" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">PASSWORD</label>
                            <input type="password" name="password" class="form-control" placeholder="<?= $mode_edit ? 'Isi jika ingin ganti password...' : '••••••••' ?>" autocomplete="new-password" <?= $mode_edit ? '' : 'required' ?>>
                            <?php if($mode_edit): ?>
                                <small class="text-muted d-block mt-2 px-1">Biarkan kosong jika tidak ingin mengubah password lama.</small>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" name="proses_admin" class="btn btn-gradient w-100 shadow">
                            <?= $mode_edit ? 'SIMPAN PERUBAHAN' : 'SIMPAN' ?>
                        </button>

                        <?php if($mode_edit): ?>
                            <a href="kelola_admin.php" class="btn btn-light w-100 rounded-pill py-2 mt-2 border fw-bold text-muted small">BATAL EDIT</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-pro p-4">
                    <h5 class="fw-800 mb-4 text-dark">Daftar Admin</h5>
                    <div class="table-responsive">
                        <table class="table table-pro align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($admins)): ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama']) ?></td>
                                    <td class="text-muted">@<?= htmlspecialchars($row['username']) ?></td>
                                    <td class="text-center">
                                        <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning text-white rounded-pill px-3 fw-bold me-1">Edit</a>
                                        <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" onclick="return confirm('Hapus akses admin ini?')">Hapus</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
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