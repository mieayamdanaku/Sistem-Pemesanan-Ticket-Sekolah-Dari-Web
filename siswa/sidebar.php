<style>

    :root{
        --primary:#1E3A8A;
        --secondary:#DC2626;
        --light:#F8FAFC;
        --dark:#0F172A;
        --soft:#E2E8F0;
    }

    /* OFFCANVAS */

    .offcanvas{
        border:none;
        width:320px!important;
        background:rgba(255,255,255,0.96);
        backdrop-filter:blur(18px);
        box-shadow:12px 0 40px rgba(0,0,0,0.08);
    }

    .offcanvas-header{
        padding:24px;
        border-bottom:1px solid rgba(0,0,0,0.05);
        background:linear-gradient(
            135deg,
            rgba(30,58,138,.06),
            rgba(220,38,38,.04)
        );
    }

    .offcanvas-title{
        font-weight:800;
        color:var(--primary)!important;
        font-size:1.25rem;
        letter-spacing:.3px;
    }

    .btn-close{
        background-size:14px;
        opacity:.7;
    }

    .btn-close:hover{
        opacity:1;
    }

    .offcanvas-body{
        padding:24px;
    }

    /* PROFILE BOX */

    .profile-box{
        background:linear-gradient(
            135deg,
            var(--primary),
            #2563EB
        );

        border-radius:26px;

        padding:22px;

        color:white;

        margin-bottom:28px;

        position:relative;

        overflow:hidden;

        box-shadow:
            0 15px 35px rgba(37,99,235,.25);
    }

    .profile-box::before{
        content:'';

        position:absolute;

        width:180px;
        height:180px;

        background:rgba(255,255,255,.08);

        border-radius:50%;

        top:-80px;
        right:-60px;
    }

    /* AVATAR */

    .sidebar-avatar{

        width:72px;
        height:72px;

        border-radius:50%;

        object-fit:cover;

        border:3px solid rgba(255,255,255,.9);

        margin-bottom:16px;

        position:relative;
        z-index:2;
    }

    .sidebar-avatar-text{

        background:rgba(255,255,255,.18);

        display:flex;
        align-items:center;
        justify-content:center;

        font-size:1.6rem;
        font-weight:800;

        color:white;
    }

    /* NAME */

    .profile-name{

        font-weight:800;

        font-size:1.15rem;

        position:relative;
        z-index:2;
    }

    /* MENU */

    .menu-title{
        font-size:.78rem;
        font-weight:800;
        letter-spacing:1px;
        color:#94A3B8;
        margin-bottom:14px;
        text-transform:uppercase;
    }

    .menu-link{
        display:flex;
        align-items:center;
        gap:16px;
        padding:15px 18px;
        color:#475569;
        text-decoration:none;
        font-weight:700;
        border-radius:18px;
        margin-bottom:10px;
        transition:.3s;
        position:relative;
        overflow:hidden;
    }

    .menu-link::before{
        content:'';
        position:absolute;
        inset:0;
        background:linear-gradient(
            135deg,
            rgba(37,99,235,.08),
            rgba(30,58,138,.04)
        );
        opacity:0;
        transition:.3s;
    }

    .menu-link:hover::before{
        opacity:1;
    }

    .menu-link:hover{
        transform:translateX(4px);
        color:var(--primary);
    }

    .menu-link.active{
        background:linear-gradient(
            135deg,
            var(--primary),
            #2563EB
        );

        color:white;

        box-shadow:
            0 10px 25px rgba(37,99,235,.28);
    }

    .menu-link.active .menu-icon{
        background:rgba(255,255,255,.18);
        color:white;
    }

    .menu-icon{
        width:42px;
        height:42px;
        border-radius:14px;
        background:#F1F5F9;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.1rem;
        transition:.3s;
        position:relative;
        z-index:2;
    }

    .menu-text{
        position:relative;
        z-index:2;
    }

    /* DIVIDER */

    .sidebar-divider{
        border-top:1px dashed rgba(148,163,184,.35);
        margin:24px 0;
    }

    /* LOGOUT BUTTON */

    .btn-logout{
        background:linear-gradient(
            135deg,
            var(--secondary),
            #EF4444
        );

        border:none;

        border-radius:18px;

        font-weight:700;

        padding:14px;

        transition:.3s;

        color:white;

        text-decoration:none;

        text-align:center;

        box-shadow:
            0 10px 25px rgba(220,38,38,.22);
    }

    .btn-logout:hover{
        transform:translateY(-3px);

        color:white;

        box-shadow:
            0 16px 35px rgba(220,38,38,.28);
    }

</style>

<div class="offcanvas offcanvas-start"
     tabindex="-1"
     id="sidebarSiswa">

    <!-- HEADER -->

    <div class="offcanvas-header">

        <h5 class="offcanvas-title">
            Menu
        </h5>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="offcanvas">
        </button>

    </div>

    <div class="offcanvas-body d-flex flex-column">

        <?php

            $page = basename($_SERVER['PHP_SELF']);

            // AMBIL DATA SISWA
            $id_siswa_sidebar = $_SESSION['siswa'] ?? null;

            $foto_sidebar = '';
            $nama_sidebar = 'Siswa';

            if($id_siswa_sidebar){

                $query_sidebar = mysqli_query(
                    $conn,
                    "SELECT nama, foto
                     FROM siswa
                     WHERE id='$id_siswa_sidebar'"
                );

                if(mysqli_num_rows($query_sidebar) > 0){

                    $data_sidebar =
                        mysqli_fetch_assoc($query_sidebar);

                    $nama_sidebar =
                        $data_sidebar['nama'];

                    $foto_sidebar =
                        $data_sidebar['foto'];
                }
            }

        ?>

        <!-- PROFILE CARD -->

        <div class="profile-box">

            <!-- FOTO -->

            <?php if(!empty($foto_sidebar)): ?>

                <img src="../assets/img/<?= $foto_sidebar ?>"
                     class="sidebar-avatar">

            <?php else: ?>

                <div class="sidebar-avatar sidebar-avatar-text">

                    <?= strtoupper(substr($nama_sidebar,0,1)) ?>

                </div>

            <?php endif; ?>

            <!-- NAMA -->

            <div class="profile-name">

                Haloo, <?= htmlspecialchars($nama_sidebar) ?>

            </div>

        </div>


        <a href="dashboard.php"
           class="menu-link <?= ($page == 'dashboard.php') ? 'active' : '' ?>">

            <div class="menu-icon">
                🏠
            </div>

            <div class="menu-text">
                Beranda
            </div>

        </a>

        <a href="tiket_saya.php"
           class="menu-link <?= ($page == 'tiket_saya.php') ? 'active' : '' ?>">

            <div class="menu-icon">
                🎫
            </div>

            <div class="menu-text">
                Tiket Saya
            </div>

        </a>

        <a href="profile.php"
           class="menu-link <?= ($page == 'profile.php') ? 'active' : '' ?>">

            <div class="menu-icon">
                👤
            </div>

            <div class="menu-text">
                Profil Saya
            </div>

        </a>

        <!-- DIVIDER -->

        <div class="sidebar-divider"></div>

        <!-- FOOTER -->

        <div class="mt-auto">

            <a href="../logout.php"
               class="btn-logout d-block w-100">

                Logout

            </a>

        </div>

    </div>

</div>