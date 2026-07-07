<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';
include '../includes/header.php';

$query = mysqli_query($conn, "SELECT * FROM dokter ORDER BY id_dokter DESC");
?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h2>Data Dokter</h2>

        <div class="d-flex">

            <input type="text"
                   id="searchDokter"
                   class="form-control me-2"
                   placeholder="Cari dokter..."
                   style="width:220px;">

            <a href="tambah_dokter.php"
               class="btn btn-success text-nowrap">

                + Tambah Dokter

            </a>

        </div>

    </div>

    <table class="table table-bordered table-striped table-hover" id="tabelDokter">

        <thead class="table-primary">

            <tr>

                <th>No</th>

                <th>Nama Dokter</th>

                <th>Spesialis</th>

                <th>Jadwal Praktik</th>

                <th>No HP</th>

                <th width="170">Aksi</th>

            </tr>

        </thead>

        <tbody>

        <?php

        $no = 1;

        while($row = mysqli_fetch_assoc($query)){

        ?>

        <tr>

            <td><?= $no++; ?></td>

            <td><?= $row['nama_dokter']; ?></td>

            <td><?= $row['spesialis']; ?></td>

            <td><?= $row['jadwal']; ?></td>

            <td><?= $row['no_hp']; ?></td>

            <td>

                <a href="edit_dokter.php?id=<?= $row['id_dokter']; ?>"
                   class="btn btn-warning btn-sm">

                    Edit

                </a>

                <a href="hapus_dokter.php?id=<?= $row['id_dokter']; ?>"
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

document.getElementById("searchDokter").addEventListener("keyup", function(){

    let keyword = this.value.toLowerCase();

    let rows = document.querySelectorAll("#tabelDokter tbody tr");

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