<?php
session_start();

// 1. Proteksi Hak Akses (Hanya untuk Admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "Admin") {
    include '../includes/header.php'; // Memuat CSS/Bootstrap agar tampilan error rapi
    echo "<div class='container mt-5'>
            <div class='alert alert-danger shadow p-4 text-center' style='border-radius: 15px;'>
                <div class='fs-1 mb-2'>⚠️</div>
                <h4 class='fw-bold text-danger mb-2'>Akses Ditolak</h4>
                <p class='mb-3 text-muted'>Halaman ini dilindungi dan hanya dapat diakses oleh akun dengan role <strong>Admin</strong>.</p>
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

$query = mysqli_query($conn, "SELECT * FROM pasien ORDER BY id_pasien DESC");
?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <!-- Mengubah judul menjadi Master Data Pasien -->
        <h2>Master Data Pasien</h2>

        <div class="d-flex">

            <!-- Tombol + Tambah Pasien telah dihapus karena registrasi dialihkan ke menu Pendaftaran -->
            <input
                type="text"
                id="searchPasien"
                class="form-control"
                placeholder="Cari pasien..."
                style="width:260px;">

        </div>

    </div>

    <table class="table table-bordered table-striped table-hover" id="tabelPasien">

        <thead class="table-primary">

            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Umur</th>
                <th>Jenis Kelamin</th>
                <th>Alamat</th>
                <th>Keluhan</th>
                <th width="170">Aksi</th>
            </tr>

        </thead>

        <tbody>

        <?php
        $no = 1;

        while ($row = mysqli_fetch_assoc($query)) {
        ?>

            <tr>

                <td><?= $no++; ?></td>

                <td><?= htmlspecialchars($row['nama']); ?></td>

                <td><?= htmlspecialchars($row['umur']); ?></td>

                <td><?= htmlspecialchars($row['jenis_kelamin']); ?></td>

                <td><?= htmlspecialchars($row['alamat']); ?></td>

                <td><?= htmlspecialchars($row['keluhan']); ?></td>

                <td>

                    <a href="edit_pasien.php?id=<?= $row['id_pasien']; ?>" class="btn btn-warning btn-sm">
                        Edit
                    </a>

                    <a href="hapus_pasien.php?id=<?= $row['id_pasien']; ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                        Hapus
                    </a>

                </td>

            </tr>

        <?php
        }
        ?>

        </tbody>

    </table>

</div>

<script>
document.getElementById("searchPasien").addEventListener("keyup", function () {

    let keyword = this.value.toLowerCase();

    let rows = document.querySelectorAll("#tabelPasien tbody tr");

    rows.forEach(function (row) {

        let text = row.textContent.toLowerCase();

        if (text.includes(keyword)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }

    });

});
</script>

<?php
include '../includes/footer.php';
?>