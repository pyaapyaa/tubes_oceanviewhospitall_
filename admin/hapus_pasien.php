<?php
session_start();

// 1. Proteksi Login Umum
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Proteksi Hak Akses (Hanya Admin yang boleh menghapus data pasien)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "Admin") {
    include '../includes/header.php'; // Memuat CSS Bootstrap agar tampilan rapi
    echo "<div class='container mt-5'>
            <div class='alert alert-danger shadow p-4 text-center' style='border-radius: 15px;'>
                <div class='fs-1 mb-2'>⚠️</div>
                <h4 class='fw-bold text-danger mb-2'>Akses Ditolak</h4>
                <p class='mb-3 text-muted'>Halaman ini dilindungi. Hanya akun dengan role <strong>Admin</strong> yang memiliki otoritas menghapus data pasien.</p>
                <a href='pasien.php' class='btn btn-danger px-4' style='border-radius: 8px;'>Kembali</a>
            </div>
         </div>";
    include '../includes/footer.php';
    exit;
}

include '../config/koneksi.php';

// Mengambil ID pasien dari URL dan mengamankannya dari SQL Injection
$id = mysqli_real_escape_string($conn, $_GET['id']);

// =========================================================================
// PERBAIKAN LOGIKA: PROSES DELETION CASCADE (Penghapusan Berantai)
// =========================================================================

// 1. Hapus data pemeriksaan terlebih dahulu (karena bergantung pada id_daftar)
mysqli_query($conn, "
DELETE pemeriksaan FROM pemeriksaan
JOIN pendaftaran ON pemeriksaan.id_daftar = pendaftaran.id_daftar
WHERE pendaftaran.id_pasien = '$id'
");

// 2. Hapus data pendaftaran berobat yang terikat dengan pasien ini
mysqli_query($conn, "
DELETE FROM pendaftaran WHERE id_pasien='$id'
");

// 3. Hapus data utama pasien
mysqli_query($conn, "
DELETE FROM pasien WHERE id_pasien='$id'
");

// =========================================================================

echo "<script>
        alert('Data pasien dan seluruh riwayat rekam medis terkait berhasil dihapus.');
        window.location='pasien.php';
      </script>";
?>