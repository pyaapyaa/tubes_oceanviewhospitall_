<?php
include 'config/koneksi.php';
include 'includes/header.php';

$query = mysqli_query($conn, "SELECT * FROM dokter ORDER BY nama_dokter ASC");
?>

<div class="container py-5">

    <div class="text-center mb-5">

        <h1 class="fw-bold">Tim Dokter Kami</h1>

        <p class="text-muted">
            Dokter profesional yang siap memberikan pelayanan kesehatan terbaik bagi Anda.
        </p>

    </div>

    <div class="row">

        <?php while($row = mysqli_fetch_assoc($query)){ ?>

        <div class="col-md-4 mb-4">

            <div class="card shadow h-100">

                <div class="card-body text-center">

                    <div style="font-size:60px;">👨‍⚕️</div>

                    <h4><?= $row['nama_dokter']; ?></h4>

                    <span class="badge bg-primary mb-3">
                        <?= $row['spesialis']; ?>
                    </span>

                    <p>

                        <strong>Jadwal Praktik</strong><br>

                        <?= $row['jadwal']; ?>

                    </p>

                    <hr>

                    <p>

                        <strong>No. HP</strong><br>

                        <?= $row['no_hp']; ?>

                    </p>

                </div>

            </div>

        </div>

        <?php } ?>

    </div>

</div>

<?php
include 'includes/footer.php';
?>