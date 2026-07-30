<style>
    /* CSS Khusus Sidebar agar konsisten di semua halaman */
    .menu-link { 
        display: flex; 
        align-items: center; 
        gap: 12px; 
        padding: 12px 20px; 
        color: #475569; 
        text-decoration: none; 
        font-weight: 600; 
        border-radius: 12px; 
        margin-bottom: 8px; 
        transition: 0.3s; 
    }
    .menu-link:hover { 
        background: #EFF6FF; 
        color: #2563EB; 
    }
    .menu-link.active { 
        background: #2563EB; 
        color: white; 
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
    .offcanvas { 
        border-radius: 0 24px 24px 0; 
        border: none; 
        box-shadow: 10px 0 30px rgba(0,0,0,0.05);
    }
</style>

<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold text-primary">Menu Admin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        <?php $current = basename($_SERVER['PHP_SELF']); ?>

        <a href="dashboard.php" class="menu-link <?= ($current == 'dashboard.php') ? 'active' : '' ?>">
            <span>🏠</span> Dashboard Pemesanan
        </a>
        <a href="tambah_event.php" class="menu-link <?= ($current == 'tambah_event.php') ? 'active' : '' ?>">
            <span>➕</span> Tambah Event Baru
        </a>
        <a href="kelola_event.php" class="menu-link <?= ($current == 'kelola_event.php' || $current == 'edit_event.php') ? 'active' : '' ?>">
            <span>✏️</span> Kelola & Edit Event
        </a>
        <a href="tambah_siswa.php" class="menu-link <?= ($current == 'tambah_siswa.php') ? 'active' : '' ?>">
            <span>👤</span> Kelola Akun Siswa
        </a>
        
        <hr class="text-muted opacity-25">
        
        <a href="kelola_admin.php" class="menu-link <?= ($current == 'kelola_admin.php') ? 'active' : '' ?>">
            <span>⚙️</span> Kelola Admin
        </a>

        
        <div class="mt-auto mb-3">
            <a href="../logout.php" class="btn btn-danger w-100 rounded-pill fw-bold py-2 shadow-sm">
                Logout
            </a>
        </div>
    </div>
</div>