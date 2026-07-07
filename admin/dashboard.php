<?php
session_start();

// 1. Proteksi Login Umum
if (!isset($_SESSION['id_user']) || !isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';
include '../includes/header.php';

// --- AMBIL DATA STATISTIK (Dijalankan sesuai kebutuhan optimasi query) ---
$role_user = $_SESSION['role'];

// Statistik Aktivitas Pelayanan (Dibutuhkan hampir semua role untuk indikator antrean)
$menungguPeriksa = mysqli_num_rows(mysqli_query($conn, "SELECT id_daftar FROM pendaftaran WHERE status='Menunggu Pemeriksaan'"));
$menungguObat    = mysqli_num_rows(mysqli_query($conn, "SELECT id_daftar FROM pendaftaran WHERE status='Menunggu Obat'"));
$selesai         = mysqli_num_rows(mysqli_query($conn, "SELECT id_daftar FROM pendaftaran WHERE status='Selesai'"));

// Statistik Khusus Admin / Apoteker
$jumlahObat   = mysqli_num_rows(mysqli_query($conn, "SELECT id_obat FROM obat"));
$stokMenipis  = mysqli_num_rows(mysqli_query($conn, "SELECT id_obat FROM obat WHERE stok <= 10"));

// Statistik Khusus Admin Master
if ($role_user === "Admin") {
    $jumlahDokter      = mysqli_num_rows(mysqli_query($conn, "SELECT id_dokter FROM dokter"));
    $jumlahPasien      = mysqli_num_rows(mysqli_query($conn, "SELECT id_pasien FROM pasien"));
    $jumlahUser        = mysqli_num_rows(mysqli_query($conn, "SELECT id_user FROM users"));
    $jumlahPendaftaran = mysqli_num_rows(mysqli_query($conn, "SELECT id_daftar FROM pendaftaran"));
}
?>

<!-- Custom CSS bawaan untuk keselarasan visual modern -->
<style>
    .custom-alert {
        background-color: #e3f2fd;
        border-left: 5px solid #0d6efd;
        border-radius: 10px;
    }
    .custom-card {
        border: none;
        border-radius: 15px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .custom-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .icon-wrapper {
        font-size: 55px;
        margin-bottom: 15px;
    }
    .btn-custom {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
    }
</style>

<div class="container mt-5">

    <!-- Header Judul Dinamis -->
    <h2 class="fw-bold mb-4">📊 Dashboard <?= htmlspecialchars($role_user); ?></h2>

    <!-- Alert Informasi Akun Login -->
    <div class="alert custom-alert shadow-sm p-4 mb-4">
        <h4 class="fw-bold text-primary mb-2">
            👋 Selamat Datang, <span class="text-dark"><?= htmlspecialchars($_SESSION['nama']); ?></span>
        </h4>
        <p class="mb-0 text-muted">
            <strong class="text-dark">Username :</strong> <?= htmlspecialchars($_SESSION['username']); ?>
            &nbsp;&nbsp;<span class="text-secondary">|</span>&nbsp;&nbsp;
            <strong class="text-dark">Hak Akses :</strong> <span class="badge bg-primary fs-6"><?= htmlspecialchars($role_user); ?></span>
        </p>
    </div>


    <!-- =================================================================================== -->
    <!-- TAMPILAN BLOK 1: KHUSUS UTK ADMIN -->
    <!-- =================================================================================== -->
    <?php if ($role_user === "Admin") { ?>
        
        <h4 class="fw-bold mb-3 text-secondary">🗂️ Manajemen Data Master</h4>
        
        <div class="row mb-4">
            <!-- Data Dokter -->
            <div class="col-md-4 mb-4">
                <div class="card custom-card shadow-sm h-100">
                    <div class="card-body d-flex flex-column align-items-center text-center p-4">
                        <div class="icon-wrapper">👨‍⚕️</div>
                        <h4 class="fw-bold text-dark">Data Dokter</h4>
                        <p class="text-muted flex-grow-1">Kelola seluruh data dokter rumah sakit yang bertugas.</p>
                        <h5 class="text-primary fw-bold mb-3">Total : <?= $jumlahDokter; ?></h5>
                        <a href="dokter.php" class="btn btn-primary btn-custom w-100">Kelola Dokter</a>
                    </div>
                </div>
            </div>

            <!-- Data Pasien -->
            <div class="col-md-4 mb-4">
                <div class="card custom-card shadow-sm h-100">
                    <div class="card-body d-flex flex-column align-items-center text-center p-4">
                        <div class="icon-wrapper">🏥</div>
                        <h4 class="fw-bold text-dark">Data Pasien</h4>
                        <p class="text-muted flex-grow-1">Kelola seluruh data medis pasien yang terdaftar di sistem.</p>
                        <h5 class="text-info fw-bold mb-3">Total : <?= $jumlahPasien; ?></h5>
                        <a href="pasien.php" class="btn btn-info text-white btn-custom w-100">Kelola Pasien</a>
                    </div>
                </div>
            </div>

            <!-- Pendaftaran Berobat -->
            <div class="col-md-4 mb-4">
                <div class="card custom-card shadow-sm h-100">
                    <div class="card-body d-flex flex-column align-items-center text-center p-4">
                        <div class="icon-wrapper">📋</div>
                        <h4 class="fw-bold text-dark">Pendaftaran</h4>
                        <p class="text-muted flex-grow-1">Registrasi entri data antrean pendaftaran pasien baru.</p>
                        <h5 class="text-warning fw-bold mb-3">Total Riwayat: <?= $jumlahPendaftaran; ?></h5>
                        <a href="pendaftaran.php" class="btn btn-warning text-white btn-custom w-100">Kelola Pendaftaran</a>
                    </div>
                </div>
            </div>

            <!-- Data Obat -->
            <div class="col-md-4 mb-4">
                <div class="card custom-card shadow-sm h-100">
                    <div class="card-body d-flex flex-column align-items-center text-center p-4">
                        <div class="icon-wrapper">💊</div>
                        <h4 class="fw-bold text-dark">Data Obat</h4>
                        <p class="text-muted flex-grow-1">Kelola sediaan, katalog, dan stok obat-obatan apotek.</p>
                        <h5 class="text-danger fw-bold mb-3">Total Jenis: <?= $jumlahObat; ?></h5>
                        <a href="obat.php" class="btn btn-danger btn-custom w-100">Kelola Obat</a>
                    </div>
                </div>
            </div>

            <!-- Stok Menipis -->
            <div class="col-md-4 mb-4">
                <div class="card custom-card shadow-sm h-100 border border-warning">
                    <div class="card-body d-flex flex-column align-items-center text-center p-4">
                        <div class="icon-wrapper">⚠️</div>
                        <h4 class="fw-bold text-warning">Stok Menipis</h4>
                        <p class="text-muted flex-grow-1">Pemberitahuan item obat darurat dengan sisa kuota stok <= 10.</p>
                        <h5 class="text-warning fw-bold mb-3"><?= $stokMenipis; ?> Produk</h5>
                        <a href="obat.php" class="btn btn-warning text-white btn-custom w-100">Periksa Inventaris</a>
                    </div>
                </div>
            </div>

            <!-- Data User -->
            <div class="col-md-4 mb-4">
                <div class="card custom-card shadow-sm h-100">
                    <div class="card-body d-flex flex-column align-items-center text-center p-4">
                        <div class="icon-wrapper">👤</div>
                        <h4 class="fw-bold text-dark">Data User</h4>
                        <p class="text-muted flex-grow-1">Atur regulasi akun login user (Admin, Dokter, Apoteker).</p>
                        <h5 class="text-success fw-bold mb-3">Total Akun: <?= $jumlahUser; ?></h5>
                        <a href="users.php" class="btn btn-success btn-custom w-100">Kelola User</a>
                    </div>
                </div>
            </div>
        </div>


    <!-- =================================================================================== -->
    <!-- TAMPILAN BLOK 2: KHUSUS UTK DOKTER -->
    <!-- =================================================================================== -->
    <?php } elseif ($role_user === "Dokter") { ?>

        <h4 class="fw-bold mb-3 text-secondary">⚡ Panel Tindakan Dokter</h4>
        
        <div class="row mb-4">
            <!-- Widget Antrean Utama -->
            <div class="col-md-6 mb-4">
                <div class="card custom-card shadow-sm h-100 border-start border-warning border-4">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <span class="fs-1">⏳</span>
                            <h3 class="fw-bold mt-2 text-dark"><?= $menungguPeriksa; ?> Pasien</h3>
                            <p class="text-muted">Jumlah pasien aktif yang sedang berada dalam daftar tunggu konsultasi Anda hari ini.</p>
                        </div>
                        <a href="pendaftaran.php" class="btn btn-warning text-dark btn-custom w-100 mt-3">
                            🔎 Mulai Pemeriksaan / Periksa Antrean
                        </a>
                    </div>
                </div>
            </div>

            <!-- Info Tambahan Riwayat -->
            <div class="col-md-6 mb-4">
                <div class="card custom-card shadow-sm h-100 border-start border-success border-4">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <span class="fs-1">✅</span>
                            <h3 class="fw-bold mt-2 text-dark"><?= $selesai; ?> Selesai</h3>
                            <p class="text-muted">Total penanganan rekam medis pasien yang sukses dirujuk keluar dari ruang pemeriksaan.</p>
                        </div>
                        <a href="riwayat.php" class="btn btn-success btn-custom w-100 mt-3">
                            📋 Lihat Riwayat Pasien
                        </a>
                    </div>
                </div>
            </div>
        </div>


    <!-- =================================================================================== -->
    <!-- TAMPILAN BLOK 3: KHUSUS UTK APOTEKER (TOMBOL UPDATE FIX) -->
    <!-- =================================================================================== -->
    <?php } elseif ($role_user === "Apoteker") { ?>

        <h4 class="fw-bold mb-3 text-secondary">⚡ Panel Logistik & Obat</h4>

        <div class="row mb-4">
            <!-- Widget Pasien Menunggu Obat -->
            <div class="col-md-4 mb-4">
                <div class="card custom-card shadow-sm h-100 border-top border-info border-4">
                    <div class="card-body text-center p-4 d-flex flex-column">
                        <div class="icon-wrapper">💊</div>
                        <h4 class="fw-bold text-dark">Antrean Resep</h4>
                        <p class="text-muted flex-grow-1">Pasien yang telah diperiksa dokter dan menunggu konformasi penyerahan obat.</p>
                        <h3 class="text-info fw-bold mb-3"><?= $menungguObat; ?> Pasien</h3>
                        <a href="pendaftaran.php" class="btn btn-info text-white btn-custom w-100">Proses Resep Obat</a>
                    </div>
                </div>
            </div>

            <!-- Widget Inventaris Obat -->
            <div class="col-md-4 mb-4">
                <div class="card custom-card shadow-sm h-100 border-top border-primary border-4">
                    <div class="card-body text-center p-4 d-flex flex-column">
                        <div class="icon-wrapper">📦</div>
                        <h4 class="fw-bold text-dark">Total Stok Obat</h4>
                        <p class="text-muted flex-grow-1">Jumlah katalog sediaan item obat yang terdaftar aktif dalam apotek.</p>
                        <h3 class="text-primary fw-bold mb-3"><?= $jumlahObat; ?> Jenis</h3>
                        <!-- FIX PERBAIKAN TOMBOL KATALOG OBAT APOTEKER -->
                        <a href="obat.php" class="btn btn-primary btn-custom w-100">
                            Katalog Obat
                        </a>
                    </div>
                </div>
            </div>

            <!-- Widget Warning Stok Menipis -->
            <div class="col-md-4 mb-4">
                <div class="card custom-card shadow-sm h-100 border-top border-danger border-4">
                    <div class="card-body text-center p-4 d-flex flex-column">
                        <div class="icon-wrapper">🚨</div>
                        <h4 class="fw-bold text-danger">Kritis / Menipis</h4>
                        <p class="text-muted flex-grow-1">Pemberitahuan restock untuk item obat dengan kapasitas stok kritis (<= 10).</p>
                        <h3 class="text-danger fw-bold mb-3"><?= $stokMenipis; ?> Obat</h3>
                        <!-- FIX PERBAIKAN TOMBOL KELOLA STOK APOTEKER -->
                        <a href="stok_obat.php" class="btn btn-danger btn-custom w-100">
                            Kelola Stok
                        </a>
                    </div>
                </div>
            </div>
        </div>

    <?php } ?>


    <!-- =================================================================================== -->
    <!-- BAGIAN FOOTER DASHBOARD: RINGKASAN AKTIVITAS UMUM (Dipakai Bersama) -->
    <!-- =================================================================================== -->
    <div class="card shadow mb-5" style="border: none; border-radius: 15px; overflow: hidden;">
        <div class="card-header bg-dark text-white fw-bold py-3">
            🔄 Live Monitor Alur Pelayanan Rumah Sakit
        </div>
        <div class="card-body p-4">
            <div class="row text-center">
                <div class="col-md-4 mb-2">
                    <h2 class="text-warning fw-bold"><?= $menungguPeriksa; ?></h2>
                    <p class="mb-0 text-muted">🟡 Menunggu Pemeriksaan (Dokter)</p>
                </div>
                <div class="col-md-4 mb-2">
                    <h2 class="text-info fw-bold"><?= $menungguObat; ?></h2>
                    <p class="mb-0 text-muted">🔵 Menunggu Obat (Apotek)</p>
                </div>
                <div class="col-md-4 mb-2">
                    <h2 class="text-success fw-bold"><?= $selesai; ?></h2>
                    <p class="mb-0 text-muted">🟢 Selesai Terlayani</p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
include '../includes/footer.php';
?>