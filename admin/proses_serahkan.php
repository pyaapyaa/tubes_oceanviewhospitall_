<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

// Validasi parameter ID dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
            alert('⚠️ ID pendaftaran tidak ditemukan atau tidak valid.');
            window.location='apotek.php';
          </script>";
    exit;
}

$id = (int)$_GET['id'];

// 📍 LANGKAH 1 — Ambil resep dari tabel pemeriksaan
$data = mysqli_query($conn, "
    SELECT resep
    FROM pemeriksaan
    WHERE id_daftar='$id'
");

$hasil = mysqli_fetch_assoc($data);
$resep = trim($hasil['resep']);

// 📍 LANGKAH 2 — Tambahkan kode pengurangan stok obat secara dinamis beserta validasi
if ($resep != "" && $resep != "-") {
    // Pisahkan setiap baris resep berdasarkan enter (\n)
    $daftar_obat = explode("\n", $resep);

    foreach ($daftar_obat as $item) {
        $item = trim($item);

        if ($item == "") {
            continue;
        }

        // Pisahkan nama obat dan jumlah berdasarkan karakter "|"
        list($nama_obat, $jumlah) = explode("|", $item);

        $nama_obat = mysqli_real_escape_string($conn, trim($nama_obat));
        $jumlah = (int) trim($jumlah);

        // Cek stok obat terlebih dahulu
        $cek = mysqli_query($conn, "
            SELECT stok
            FROM obat
            WHERE nama_obat='$nama_obat'
        ");

        $data_obat = mysqli_fetch_assoc($cek);

        // Validasi: Jika stok obat di database lebih kecil dari jumlah yang diresepkan
        if ($data_obat['stok'] < $jumlah) {
            echo "<script>
                alert('⚠️ Stok $nama_obat tidak mencukupi! (Sisa stok: " . $data_obat['stok'] . ")');
                window.location='apotek.php';
            </script>";
            exit;
        }

        // Kurangi stok obat jika stok mencukupi
        mysqli_query($conn, "
            UPDATE obat
            SET stok = stok - $jumlah
            WHERE nama_obat = '$nama_obat'
        ");
    }
}

// Update status pendaftaran menjadi 'Selesai' setelah obat diserahkan dan stok berkurang
$query_update = mysqli_query($conn, "
    UPDATE pendaftaran 
    SET status = 'Selesai' 
    WHERE id_daftar = '$id'
");

if ($query_update) {
    echo "<script>
            alert('💊 Obat berhasil diserahkan dan stok telah dipotong. Status pendaftaran: Selesai.');
            window.location='apotek.php';
          </script>";
} else {
    echo "<script>
            alert('⚠️ Gagal memperbarui status. Silakan coba lagi. Error: " . mysqli_error($conn) . "');
            window.location='apotek.php';
          </script>";
}
exit;
?>