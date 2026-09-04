<?php
session_start();
require '../koneksi.php';
/** @var mysqli $conn */

// Proteksi Admin
if(!isset($_SESSION['admin'])){ header("Location: ../login.php"); exit; }

$pesan = "";

// Logika Tambah Siswa
if(isset($_POST['tambah_siswa'])){
    $nisn = mysqli_real_escape_string($conn, $_POST['nisn']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']); // Menangkap input gender
    
    // Password default disamakan dengan NISN siswa
    $password_hash = password_hash($nisn, PASSWORD_DEFAULT); 

    $cek = mysqli_query($conn, "SELECT * FROM siswa WHERE nisn='$nisn'");
    if(mysqli_num_rows($cek) > 0){
        $pesan = "<div class='alert alert-danger border-0 shadow-sm rounded-4 mb-4'>Siswa dengan NISN tersebut sudah terdaftar!</div>";
    } else {
        // Insert data lengkap: NISN, Nama, Gender, dan Password (NISN)
        $insert = mysqli_query($conn, "INSERT INTO siswa (nisn, nama, gender, password) VALUES ('$nisn', '$nama', '$gender', '$password_hash')");
        if($insert) {
            $pesan = "<div class='alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center'><span class='fs-4 me-3'>✅</span><div><strong>Berhasil!</strong> Data siswa ditambahkan ke sistem. Password default adalah NISN siswa.</div></div>";
        }
    }
}

// Logika Edit Siswa
if(isset($_POST['edit_siswa'])){
    $id_edit = $_POST['id_siswa'];
    $nisn_baru = mysqli_real_escape_string($conn, $_POST['nisn_baru']);
    $nama_baru = mysqli_real_escape_string($conn, $_POST['nama_baru']);
    $gender_baru = mysqli_real_escape_string($conn, $_POST['gender_baru']); // Menangkap edit gender
    $password_baru = $_POST['password_baru'];

    // Cek apakah NISN baru bentrok dengan siswa lain
    $cek_nisn = mysqli_query($conn, "SELECT id FROM siswa WHERE nisn='$nisn_baru' AND id != '$id_edit'");
    
    if(mysqli_num_rows($cek_nisn) > 0){
        $pesan = "<div class='alert alert-danger border-0 shadow-sm rounded-4 mb-4'>NISN tersebut sudah dipakai oleh siswa lain!</div>";
    } else {
        // Jika password diisi, update dengan password baru. Jika kosong, biarkan password lama.
        if(!empty($password_baru)){
            $pass_hash = password_hash($password_baru, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE siswa SET nisn='$nisn_baru', nama='$nama_baru', gender='$gender_baru', password='$pass_hash' WHERE id='$id_edit'");
        } else {
            mysqli_query($conn, "UPDATE siswa SET nisn='$nisn_baru', nama='$nama_baru', gender='$gender_baru' WHERE id='$id_edit'");
        }
        $pesan = "<div class='alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center'><span class='fs-4 me-3'>✅</span><div><strong>Berhasil!</strong> Data siswa berhasil diperbarui.</div></div>";
    }
}

// Logika Hapus Siswa
if(isset($_GET['hapus'])){
    $id_hapus = $_GET['hapus'];
    // Bersihkan dependensi tiket terlebih dahulu agar tidak kena error constraint
    mysqli_query($conn, "DELETE FROM tiket WHERE siswa_id='$id_hapus'"); 
    mysqli_query($conn, "DELETE FROM siswa WHERE id='$id_hapus'");
    header("Location: tambah_siswa.php");
    exit;
}

// Ambil Data Seluruh Siswa
$siswa = mysqli_query($conn, "SELECT * FROM siswa ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Panel - Manajemen Siswa</title>
    
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
                <h4 class="fw-800 m-0">Manajemen Data Siswa</h4>
                <p class="text-muted small m-0">Daftarkan siswa atau kelola akun siswa.</p>
            </div>
        </div>

        <?= $pesan ?>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card card-pro p-4">
                    <h5 class="fw-800 mb-4 text-dark">Registrasi Siswa/Siswi</h5>
                    <form method="POST" autocomplete="off">
                        <div class="mb-4">
                            <label class="form-label">NISN SISWA (MAX 6 DIGIT)</label>
                            <input type="text" name="nisn" class="form-control" placeholder="Contoh: 123456" maxlength="6" autocomplete="off" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">NAMA LENGKAP </label>
                            <input type="text" name="nama" class="form-control" placeholder="....." autocomplete="off" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">JENIS KELAMIN</label>
                            <select name="gender" class="form-control" required>
                                <option value="" disabled selected>Pilih Jenis Kelamin...</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <div class="alert alert-light border small text-muted">
                                💡 <strong>Catatan:</strong> Password akan secara otomatis sama dengan NISN yang didaftarkan,siswa bisa merubah password di menu profilnya.
                            </div>
                        </div>
                        <button type="submit" name="tambah_siswa" class="btn btn-gradient w-100 shadow">DAFTARKAN SISWA</button>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-pro p-4">
                    <h5 class="fw-800 mb-4 text-dark">Database Siswa</h5>
                    <div class="table-responsive">
                        <table class="table table-pro align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th class="text-center">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($siswa) > 0): ?>
                                    <?php while($s = mysqli_fetch_assoc($siswa)): ?>
                                    <tr>
                                        <td class="fw-bold text-primary"><?= htmlspecialchars($s['nisn']) ?></td>
                                        <td class="fw-600 text-dark"><?= htmlspecialchars($s['nama']) ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $s['id'] ?>">Edit</button>
                                            <a href="?hapus=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" onclick="return confirm('Hapus siswa ini? Seluruh riwayat tiketnya juga akan terhapus.')">Hapus</a>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="modalEdit<?= $s['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
                                                <div class="modal-header border-0 px-4 pt-4">
                                                    <h5 class="fw-bold m-0">Edit Data Siswa</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body px-4 text-start">
                                                        <input type="hidden" name="id_siswa" value="<?= $s['id'] ?>">
                                                        <div class="mb-3">
                                                            <label class="form-label">NISN Siswa</label>
                                                            <input type="text" name="nisn_baru" class="form-control" value="<?= htmlspecialchars($s['nisn']) ?>" maxlength="6" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Lengkap</label>
                                                            <input type="text" name="nama_baru" class="form-control" value="<?= htmlspecialchars($s['nama']) ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Jenis Kelamin</label>
                                                            <select name="gender_baru" class="form-control" required>
                                                                <option value="Laki-laki" <?= (isset($s['gender']) && $s['gender'] == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                                                                <option value="Perempuan" <?= (isset($s['gender']) && $s['gender'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label">Ganti Password (Opsional)</label>
                                                            <input type="text" name="password_baru" class="form-control" placeholder="Ketik password baru disini...">
                                                            <small class="text-muted mt-1 d-block">Biarkan kosong jika tidak ingin mengubah password siswa ini.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 p-4">
                                                        <button type="submit" name="edit_siswa" class="btn btn-primary w-100 py-3 shadow rounded-pill fw-bold">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted fw-bold">Belum ada siswa terdaftar.</td>
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