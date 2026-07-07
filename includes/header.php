<?php
include_once __DIR__ . '/../config/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Mengambil nama file halaman yang sedang diakses saat ini
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ocean View Hospital</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        /* Struktur Sidebar */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #1e293b; /* Biru Gelap Profesional */
            color: #ffffff;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }
        .sidebar-brand {
            padding: 20px;
            font-size: 1.1rem;
            font-weight: 700;
            background-color: #0f172a;
        }
        .sidebar-menu {
            padding: 15px 0;
        }
        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.2s;
        }
        .sidebar-item:hover, .sidebar-item.active {
            color: #ffffff;
            background-color: #334155;
            border-left: 4px solid #3b82f6;
        }
        .sidebar-icon {
            margin-right: 12px;
            font-size: 1.2rem;
        }
        /* Struktur Konten Utama */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }
        /* Topbar Navigasi Atas */
        .topbar {
            height: 70px;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            padding: 0 30px;
        }
        .content-body {
            padding: 30px;
        }
    </style>
</head>

<body>

<?php if(isset($_SESSION['id_user'])) { ?>
    <!-- TAMPILAN JIKA SUDAH LOGIN (SIDEBAR UTAMA) -->
    <div class="sidebar">
        <div class="sidebar-brand text-center">
            🏥 Ocean View Hospital
        </div>
        <div class="sidebar-menu">
            
            <!-- ========================================================================= -->
            <!-- MENU DASHBOARD UTAMA (Disesuaikan Per Role) -->
            <!-- ========================================================================= -->
            <?php if($_SESSION['role'] == "Admin"){ ?>
                <a href="<?= BASE_URL ?>admin/dashboard.php" class="sidebar-item <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">📊</span> Dashboard
                </a>
            <?php } ?>

            <?php if($_SESSION['role'] == "Dokter"){ ?>
                <a href="<?= BASE_URL ?>admin/dashboard.php" class="sidebar-item <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">📊</span> Dashboard
                </a>
            <?php } ?>

            <!-- Menu Khusus Role Pasien -->
            <?php if($_SESSION['role'] == "Pasien"){ ?>
                <a href="<?= BASE_URL ?>pasien/dashboard_pasien.php" class="sidebar-item <?= ($current_page == 'dashboard_pasien.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">📊</span> Dashboard
                </a>
                <a href="<?= BASE_URL ?>pasien/booking.php" class="sidebar-item <?= ($current_page == 'booking.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">📝</span> Booking Berobat
                </a>
                <a href="<?= BASE_URL ?>pasien/riwayat_booking.php" class="sidebar-item <?= ($current_page == 'riwayat_booking.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">📋</span> Riwayat Booking
                </a>
            <?php } ?>

            <!-- ========================================================================= -->
            <!-- 👨‍💼 MENU KHAS ADMIN (Manajemen Data Master) -->
            <!-- ========================================================================= -->
            <?php if($_SESSION['role'] == "Admin"){ ?>
                <a href="<?= BASE_URL ?>admin/dokter.php" class="sidebar-item <?= ($current_page == 'dokter.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">👨‍⚕️</span> Data Dokter
                </a>
                <a href="<?= BASE_URL ?>admin/pasien.php" class="sidebar-item <?= ($current_page == 'pasien.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">🏥</span> Data Pasien
                </a>
                <a href="<?= BASE_URL ?>admin/pendaftaran.php" class="sidebar-item <?= ($current_page == 'pendaftaran.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">📋</span> Pendaftaran
                </a>
                <a href="<?= BASE_URL ?>admin/booking.php" class="sidebar-item <?= ($current_page == 'booking.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">📝</span> Booking Online
                </a>
                <!-- MENU ADMIN: Berhasil diarahkan ke admin/obat.php -->
                <a href="<?= BASE_URL ?>admin/obat.php" class="sidebar-item <?= ($current_page == 'obat.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">💊</span> Katalog Obat
                </a>
            <?php } ?>

            <!-- ========================================================================= -->
            <!-- 👨‍⚕️ MENU KHAS DOKTER -->
            <!-- ========================================================================= -->
            <?php if($_SESSION['role'] == "Dokter"){ ?>
                <a href="<?= BASE_URL ?>admin/pendaftaran.php" class="sidebar-item <?= ($current_page == 'pendaftaran.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">🩺</span> Pemeriksaan
                </a>
            <?php } ?>
            
            <!-- ========================================================================= -->
            <!-- 💊 MENU KHUSUS APOTEKER -->
            <!-- ========================================================================= -->
            <?php if($_SESSION['role'] == "Apoteker"){ ?>
                <a href="<?= BASE_URL ?>admin/dashboard.php" class="sidebar-item <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">📊</span> Dashboard
                </a>

                <a href="<?= BASE_URL ?>admin/apotek.php" class="sidebar-item <?= ($current_page == 'apotek.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">💊</span> Antrean Resep
                </a>

                <!-- MENU APOTEKER: Berhasil diarahkan ke admin/obat.php -->
                <a href="<?= BASE_URL ?>admin/obat.php" class="sidebar-item <?= ($current_page == 'obat.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">📦</span> Katalog Obat
                </a>

                <a href="<?= BASE_URL ?>admin/stok_obat.php" class="sidebar-item <?= ($current_page == 'stok_obat.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">📋</span> Kelola Stok
                </a>
            <?php } ?>
            
            <!-- ========================================================================= -->
            <!-- 📄 MENU LOG / RIWAYAT & AKUN -->
            <!-- ========================================================================= -->
            <!-- Menu Riwayat (Hanya untuk internal RS / Non-Pasien) -->
            <?php if($_SESSION['role'] !== "Pasien"){ ?>
                <a href="<?= BASE_URL ?>admin/riwayat.php" class="sidebar-item <?= ($current_page == 'riwayat.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">📄</span> Riwayat
                </a>
            <?php } ?>
            
            <!-- Menu Pengaturan Akun Sistem (Mutlak Admin) -->
            <?php if($_SESSION['role'] == "Admin"){ ?>
                <a href="<?= BASE_URL ?>admin/users.php" class="sidebar-item <?= ($current_page == 'users.php') ? 'active' : '' ?>">
                    <span class="sidebar-icon">👤</span> Data User
                </a>
            <?php } ?>
            
            <div class="px-4 my-3"><hr class="text-secondary"></div>
            
            <a href="<?= BASE_URL ?>auth/logout.php" class="sidebar-item text-danger">
                <span class="sidebar-icon">🚪</span> Logout
            </a>
        </div>
    </div>

    <!-- Area Utama di Sebelah Kanan Sidebar -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-muted">Sistem Informasi Rumah Sakit</h5>
            <div class="d-flex align-items-center">
                <span class="badge bg-light text-dark border p-2 px-3 fs-6">
                    👤 <?= htmlspecialchars($_SESSION['nama'] ?? 'User'); ?> (<?= htmlspecialchars($_SESSION['role'] ?? 'Guest'); ?>)
                </span>
            </div>
        </div>
        
        <!-- Pembuka wadah isi halaman (Penutupnya ada di footer.php) -->
        <div class="content-body">

<?php } else { ?>
    <!-- TAMPILAN JIKA BELUM LOGIN (NAVBAR BIASA UNTUK HALAMAN DEPAN) -->
    <header class="p-3 bg-white shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <h3 class="mb-0 fw-bold text-primary">🏥 Ocean View Hospital</h3>
            <nav class="d-flex align-items-center">
                <a href="<?= BASE_URL ?>index.php" class="me-4 text-decoration-none text-dark fw-medium">Home</a>
                <a href="<?= BASE_URL ?>dokter.php" class="me-4 text-decoration-none text-dark fw-medium">Doctor</a>
                <a href="<?= BASE_URL ?>auth/login.php" class="btn btn-primary px-4 btn-sm rounded-pill">Login</a>
            </nav>
        </div>
    </header>
    <div class="container mt-4">
<?php } ?>