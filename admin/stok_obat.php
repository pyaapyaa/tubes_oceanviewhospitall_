<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != "Apoteker") {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

// Update stok
if(isset($_POST['update'])){

    $id_obat = $_POST['id_obat'];
    $stok    = $_POST['stok'];

    mysqli_query($conn,"
        UPDATE obat
        SET stok='$stok'
        WHERE id_obat='$id_obat'
    ");

    echo "<script>
        alert('Stok berhasil diperbarui');
        window.location='stok_obat.php';
    </script>";
    exit;
}

$data = mysqli_query($conn,"SELECT * FROM obat ORDER BY nama_obat ASC");

include '../includes/header.php';
?>

<div class="container-fluid">

    <h3 class="mb-4 fw-bold">
        📦 Kelola Stok Obat
    </h3>

    <div class="card shadow">

        <div class="card-header bg-primary text-white">
            Daftar Stok Obat
        </div>

        <div class="card-body">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-light">

                    <tr>

                        <th width="60">No</th>

                        <th>Nama Obat</th>

                        <th width="180">Stok</th>

                        <th width="150">Status</th>

                        <th width="170">Aksi</th>

                    </tr>

                </thead>

                <tbody>

                <?php
                $no=1;
                while($d=mysqli_fetch_assoc($data)){
                ?>

                <tr>

                    <td><?= $no++; ?></td>

                    <td><?= $d['nama_obat']; ?></td>

                    <form method="POST">

                        <input
                        type="hidden"
                        name="id_obat"
                        value="<?= $d['id_obat']; ?>">

                        <td>

                            <input
                            type="number"
                            name="stok"
                            value="<?= $d['stok']; ?>"
                            class="form-control">

                        </td>

                        <td>

                            <?php

                            if($d['stok']<=10){

                                echo "<span class='badge bg-danger'>Kritis</span>";

                            }elseif($d['stok']<=25){

                                echo "<span class='badge bg-warning text-dark'>Menipis</span>";

                            }else{

                                echo "<span class='badge bg-success'>Aman</span>";

                            }

                            ?>

                        </td>

                        <td>

                            <button
                            class="btn btn-success btn-sm"
                            name="update">

                                💾 Simpan

                            </button>

                        </td>

                    </form>

                </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php
include '../includes/footer.php';
?>