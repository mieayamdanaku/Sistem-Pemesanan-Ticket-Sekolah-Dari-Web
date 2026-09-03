<?php
session_start();
require '../koneksi.php';

/** @var mysqli $conn */

// PROTEKSI LOGIN
if(!isset($_SESSION['siswa'])){
    header("Location: ../login.php");
    exit;
}

$id_siswa = $_SESSION['siswa'];

/* UPDATE FOTO */
if(isset($_FILES['foto'])){

    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];

    $ext = strtolower(pathinfo($foto, PATHINFO_EXTENSION));

    if(in_array($ext, ['jpg','jpeg','png'])){

        $nama_foto_baru =
            "siswa_" .
            $id_siswa .
            "_" .
            time() .
            "." .
            $ext;

        if(move_uploaded_file($tmp, '../assets/img/' . $nama_foto_baru)){

            // HAPUS FOTO LAMA

            $q_lama = mysqli_query(
                $conn,
                "SELECT foto FROM siswa WHERE id='$id_siswa'"
            );

            $foto_lama = mysqli_fetch_assoc($q_lama)['foto'];

            if(
                !empty($foto_lama)
                &&
                file_exists('../assets/img/' . $foto_lama)
            ){
                unlink('../assets/img/' . $foto_lama);
            }

            // UPDATE DATABASE

            $update = mysqli_prepare(
                $conn,
                "UPDATE siswa SET foto=? WHERE id=?"
            );

            mysqli_stmt_bind_param(
                $update,
                "si",
                $nama_foto_baru,
                $id_siswa
            );

            mysqli_stmt_execute($update);

            header("Location: profile.php");
            exit;
        }
    }
}

/* LOGIKA UPDATE PASSWORD (DITAMBAHKAN BARU) */
$pesan_password = "";
if(isset($_POST['update_password'])){
    $pass_lama = $_POST['pass_lama'];
    $pass_baru = $_POST['pass_baru'];
    $pass_konfirm = $_POST['pass_konfirm'];

    // Ambil password lama dari database
    $q_pass = mysqli_query($conn, "SELECT password FROM siswa WHERE id='$id_siswa'");
    $data_pass = mysqli_fetch_assoc($q_pass);

    // Cek kecocokan password lama
    if(password_verify($pass_lama, $data_pass['password'])){
        // Cek apakah password baru & konfirmasi sama
        if($pass_baru === $pass_konfirm){
            // Hash password baru & simpan
            $hash_baru = password_hash($pass_baru, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE siswa SET password='$hash_baru' WHERE id='$id_siswa'");
            $pesan_password = "<script>alert('Berhasil: Password Anda telah diperbarui!');</script>";
        } else {
            $pesan_password = "<script>alert('Gagal: Password baru dan konfirmasi tidak cocok!');</script>";
        }
    } else {
        $pesan_password = "<script>alert('Gagal: Password lama yang Anda masukkan salah!');</script>";
    }
}

/* AMBIL DATA SISWA */

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM siswa WHERE id=?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_siswa
);

mysqli_stmt_execute($stmt);

$data_siswa =
    mysqli_fetch_assoc(
        mysqli_stmt_get_result($stmt)
    );

$nama = $data_siswa['nama'];
$nisn = $data_siswa['nisn'];
$gender = $data_siswa['gender'] ?? 'Belum Diatur';
$foto_profil = $data_siswa['foto'];

// Panggil alert jika ada proses ganti password
echo $pesan_password;
?>

<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
              <meta http-equiv="refresh" content="15">

    <title>Profil Saya </title>

    <link href="../assets/css/bootstrap.min.css"
          rel="stylesheet">

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
            background:
                linear-gradient(
                    to bottom,
                    #F8FAFC,
                    #EEF2FF
                );

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

        /* PAGE HEADER */

        .page-title{
            font-weight:800;
            color:var(--dark);
        }

        .page-subtitle{
            color:#64748B;
            margin-top:8px;
        }

        /* PROFILE CARD */

        .profile-card{
            width:100%;
            max-width:980px;
            margin:50px auto;
            background:white;
            border-radius:36px;
            overflow:hidden;

            box-shadow:
                0 25px 60px rgba(30,58,138,0.10);

            display:flex;
            min-height:520px;
        }

        /* LEFT SIDE */

        .profile-left{
            width:38%;
            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    #2563EB
                );

            position:relative;

            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;

            padding:40px;

            color:white;

            overflow:hidden;
        }

        .profile-left::before{
            content:'';

            position:absolute;

            width:260px;
            height:260px;

            background:rgba(255,255,255,.08);

            border-radius:50%;

            top:-90px;
            right:-90px;
        }

        .profile-left::after{
            content:'';

            position:absolute;

            width:180px;
            height:180px;

            background:rgba(255,255,255,.06);

            border-radius:50%;

            bottom:-70px;
            left:-60px;
        }

        /* AVATAR */

        .avatar-container{
            position:relative;
            width:150px;
            height:150px;
            margin-bottom:22px;
            z-index:2;
        }

        .avatar-content{
            width:100%;
            height:100%;

            border-radius:50%;

            object-fit:cover;

            border:5px solid rgba(255,255,255,.9);

            background:white;

            display:flex;
            align-items:center;
            justify-content:center;

            font-size:3.2rem;
            font-weight:800;

            color:var(--primary);

            box-shadow:
                0 15px 35px rgba(0,0,0,.18);
        }

        /* EDIT BUTTON */

        .edit-btn{
            position:absolute;
            bottom:5px;
            right:5px;

            width:42px;
            height:42px;

            border-radius:50%;

            background:white;

            display:flex;
            align-items:center;
            justify-content:center;

            cursor:pointer;

            font-size:1rem;

            border:3px solid #2563EB;

            color:#2563EB;

            transition:.3s;
        }

        .edit-btn:hover{
            transform:scale(1.08);
        }

        /* INFO */

        .student-badge{
            background:rgba(255,255,255,.18);

            padding:8px 18px;

            border-radius:999px;

            font-size:.78rem;

            font-weight:700;

            letter-spacing:.6px;

            text-transform:uppercase;

            margin-bottom:18px;

            z-index:2;
        }

        .student-name{
            font-size:1.8rem;
            font-weight:800;
            text-align:center;
            z-index:2;
        }

        .student-sub{
            opacity:.82;
            font-size:.95rem;
            text-align:center;
            margin-top:6px;
            z-index:2;
        }

        /* RIGHT SIDE */

        .profile-right{
            width:62%;
            padding:50px;
            display:flex;
            flex-direction:column;
            justify-content:center;
        }

        .info-box{
            background:#F8FAFC;
            border-radius:24px;
            padding:24px;
            margin-bottom:18px;
            border:1px solid #E2E8F0;
            transition:.3s;
        }

        .info-box:hover{
            transform:translateY(-3px);

            box-shadow:
                0 12px 30px rgba(0,0,0,0.05);
        }

        .info-label{
            font-size:.78rem;
            color:#94A3B8;
            font-weight:800;
            letter-spacing:.8px;
            text-transform:uppercase;
            margin-bottom:10px;
        }

        .info-value{
            font-size:1.2rem;
            color:var(--dark);
            font-weight:800;
        }

        /* MODAL KHUSUS */
        .modal-content{ border-radius:30px!important; }

        /* RESPONSIVE */

        @media(max-width:992px){

            .profile-card{
                flex-direction:column;
            }

            .profile-left,
            .profile-right{
                width:100%;
            }

            .profile-right{
                padding:35px 25px;
            }
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

                <span class="fs-4">☰</span>

            </button>

            <a class="navbar-brand m-0">
                Profil Siswa
            </a>

        </div>

    </nav>

    <?php include 'sidebar.php'; ?>

    <div class="container">

        <div class="profile-card">

            <div class="profile-left">

                <div class="avatar-container">

                    <?php if(!empty($foto_profil)): ?>

                        <img src="../assets/img/<?= $foto_profil ?>"
                             class="avatar-content">

                    <?php else: ?>

                        <div class="avatar-content">

                            <?= strtoupper(substr($nama,0,1)) ?>

                        </div>

                    <?php endif; ?>

                    <label for="inputFoto"
                           class="edit-btn">

                        ✎

                    </label>

                    <form id="formFoto"
                          method="POST"
                          enctype="multipart/form-data"
                          style="display:none;">

                        <input type="file"
                               name="foto"
                               id="inputFoto"
                               accept=".jpg,.jpeg,.png"
                               onchange="document.getElementById('formFoto').submit()">

                    </form>

                </div>

                <div class="student-badge">
             Siswa Antartika
                </div>

                <div class="student-name">
                    <?= htmlspecialchars($nama) ?>
                </div>

                <div class="student-sub">
                    Portal Event Sekolah Antartika
                </div>

            </div>

            <div class="profile-right">

                <div class="info-box">

                    <div class="info-label">
                        Nama Lengkap
                    </div>

                    <div class="info-value">
                        <?= htmlspecialchars($nama) ?>
                    </div>

                </div>

                <div class="info-box">

                    <div class="info-label">
                        Nomor Induk Siswa Nasional
                    </div>

                    <div class="info-value">
                        <?= htmlspecialchars($nisn) ?>
                    </div>

                </div>

                <div class="info-box">

                    <div class="info-label">
                        Jenis Kelamin
                    </div>

                    <div class="info-value">
                        <?= htmlspecialchars($gender) ?>
                    </div>

                </div>

                <button type="button" class="btn btn-outline-primary fw-bold rounded-pill w-100 py-3 mt-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPassword">
                    Ubah Password Akun
                </button>

            </div>

        </div>

    </div>

    <div class="modal fade" id="modalPassword" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="fw-bold m-0 text-dark">Ubah Password Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body px-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">Password Lama</label>
                            <input type="password" name="pass_lama" class="form-control rounded-4 py-2 bg-light border-0" required placeholder="Masukkan password lama">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">Password Baru</label>
                            <input type="password" name="pass_baru" class="form-control rounded-4 py-2 bg-light border-0" required placeholder="Masukkan password baru">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary small">Konfirmasi Password Baru</label>
                            <input type="password" name="pass_konfirm" class="form-control rounded-4 py-2 bg-light border-0" required placeholder="Ulangi password baru">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4">
                        <button type="submit" name="update_password" class="btn btn-primary w-100 py-3 shadow rounded-pill">Simpan Password Baru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>