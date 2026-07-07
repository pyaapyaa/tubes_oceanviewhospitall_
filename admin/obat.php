<?php
session_start();

// 1. Proteksi Hak Akses (Bisa diakses oleh Admin atau Apoteker)
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== "Admin" && $_SESSION['role'] !== "Apoteker")) {
    include '../includes/header.php'; // Memuat CSS/Bootstrap agar tampilan error rapi
    echo "<div class='container mt-5'>
            <div class='alert alert-danger shadow p-4 text-center' style='border-radius: 15px;'>
                <div class='fs-1 mb-2'>⚠️</div>
                <h4 class='fw-bold text-danger mb-2'>Akses Ditolak</h4>
                <p class='mb-3 text-muted'>Halaman ini dilindungi dan hanya dapat diakses oleh akun dengan role <strong>Admin</strong> atau <strong>Apoteker</strong>.</p>
                <a href='../auth/login.php' class='btn btn-danger px-4' style='border-radius: 8px;'>Kembali ke Login</a>
            </div>
         </div>";
    include '../includes/footer.php';
    exit; // Menghentikan sisa kode di bawah agar data tidak bocor
}

// 2. Proteksi Login Umum
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';
include '../includes/header.php';

// LANGKAH 1: Query Database ke tabel obat
$query = mysqli_query($conn, "SELECT * FROM obat ORDER BY id_obat DESC");
?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <!-- LANGKAH 2: Judul Halaman dinamis berdasarkan Role -->
        <h2><?= ($_SESSION['role'] == "Apoteker") ? "Kelola Obat" : "Katalog Obat"; ?></h2>

        <div class="d-flex">

            <!-- LANGKAH 3: Search ID dan Placeholder -->
            <input type="text"
                   id="searchObat"
                   class="form-control me-2"
                   placeholder="Cari obat..."
                   style="width:220px;">

            <!-- LANGKAH 4: Tombol Tambah Data (Hanya untuk Apoteker) -->
            <?php if($_SESSION['role'] == "Apoteker"){ ?>
                <a href="tambah_obat.php"
                   class="btn btn-success text-nowrap">
                    + Tambah Obat
                </a>
            <?php } ?>

        </div>

    </div>

    <!-- LANGKAH 7: ID Tabel menjadi tabelObat -->
    <table class="table table-bordered table-striped table-hover" id="tabelObat">

        <thead class="table-primary">

            <tr>
                <th>No</th>
                <!-- LANGKAH 5: Header Tabel -->
                <th>Nama Obat</th>
                <th>Jenis</th>
                <th>Stok</th>
                <th>Harga</th>
                <?php if($_SESSION['role'] == "Apoteker"){ ?>
                    <th width="170">Aksi</th>
                <?php } ?>
            </tr>

        </thead>

        <tbody>

        <?php
        $no = 1;
        while($row = mysqli_fetch_assoc($query)){
        ?>

        <tr>

            <td><?= $no++; ?></td>

            <!-- LANGKAH 5: Data Kolom dan Format Harga -->
            <td><?= htmlspecialchars($row['nama_obat']); ?></td>
            <td><?= htmlspecialchars($row['jenis']); ?></td>
            <td><?= htmlspecialchars($row['stok']); ?></td>
            <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>

            <!-- Aksi Edit & Hapus (Hanya untuk Apoteker) -->
            <?php if($_SESSION['role'] == "Apoteker"){ ?>
                <td>

                    <a href="edit_obat.php?id=<?= $row['id_obat']; ?>"
                       class="btn btn-warning btn-sm">
                        Edit
                    </a>

                    <a href="hapus_obat.php?id=<?= $row['id_obat']; ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                        Hapus
                    </a>

                </td>
            <?php } ?>

        </tr>

        <?php
        }
        ?>

        </tbody>

    </table>

</div>

<script>
// LANGKAH 6: JavaScript untuk searchObat dan tabelObat
document.getElementById("searchObat").addEventListener("keyup", function(){

    let keyword = this.value.toLowerCase();
    let rows = document.querySelectorAll("#tabelObat tbody tr");

    rows.forEach(function(row){

        let text = row.textContent.toLowerCase();

        if(text.indexOf(keyword) > -1){
            row.style.display="";
        }else{
            row.style.display="none";
        }

    });

});
</script>

<?php
include '../includes/footer.php';
?>