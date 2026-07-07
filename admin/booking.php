<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] != "Admin") {
    header("Location: dashboard.php");
    exit;
}

include '../config/koneksi.php';
include '../includes/header.php';

$query = mysqli_query($conn,"
SELECT
    booking.*,
    pasien.nama,
    dokter.nama_dokter
FROM booking
JOIN pasien ON booking.id_pasien = pasien.id_pasien
JOIN dokter ON booking.id_dokter = dokter.id_dokter
ORDER BY booking.id_booking DESC
");
?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">Booking Online Pasien</h3>

        <input type="text"
               id="searchBooking"
               class="form-control"
               placeholder="Cari pasien..."
               style="width:250px;">
    </div>

    <div class="card shadow">

        <div class="card-header bg-primary text-white fw-bold">
            Data Booking Online
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-striped align-middle" id="tabelBooking">

                    <thead class="table-primary text-center">
                        <tr>
                            <th>No</th>
                            <th>Kode Booking</th>
                            <th>Pasien</th>
                            <th>Dokter</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Status</th>
                            <th width="140">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php
                    $no=1;

                    while($row=mysqli_fetch_assoc($query)){
                    ?>

                    <tr>

                        <td class="text-center"><?= $no++; ?></td>

                        <td><?= htmlspecialchars($row['kode_booking']); ?></td>

                        <td><?= htmlspecialchars($row['nama']); ?></td>

                        <td><?= htmlspecialchars($row['nama_dokter']); ?></td>

                        <td class="text-center">
                            <?= date('d-m-Y',strtotime($row['tanggal_booking'])); ?>
                        </td>

                        <td class="text-center">
                            <?= htmlspecialchars($row['jam_booking']); ?>
                        </td>

                        <td class="text-center">

                            <?php
                            if($row['status'] == "Menunggu Check-in"){
                                echo '<span class="badge bg-warning text-dark">Menunggu Check-in</span>';
                            }
                            elseif($row['status'] == "Sudah Check-in"){
                                echo '<span class="badge bg-info">Sudah Check-in</span>';
                            }
                            elseif($row['status'] == "Selesai"){
                                echo '<span class="badge bg-success">Selesai</span>';
                            }
                            else{
                                echo '<span class="badge bg-danger">'.htmlspecialchars($row['status']).'</span>';
                            }
                            ?>

                        </td>

                        <td class="text-center">

                            <?php
                            // Jika status masih menunggu, tampilkan tombol Check-in yang aktif menuju checkin_booking.php
                            if($row['status'] == "Menunggu Check-in"){
                            ?>

                                <a href="checkin_booking.php?id=<?= $row['id_booking']; ?>"
                                   class="btn btn-success btn-sm w-100"
                                   onclick="return confirm('Yakin pasien sudah datang dan ingin melakukan check-in?')">
                                    Check-in
                                </a>

                            <?php }else{ ?>

                                <span class="text-muted">-</span>

                            <?php } ?>

                        </td>

                    </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<script>
document.getElementById("searchBooking").addEventListener("keyup",function(){
    let keyword=this.value.toLowerCase();
    let rows=document.querySelectorAll("#tabelBooking tbody tr");
    rows.forEach(function(row){
        let text=row.textContent.toLowerCase();
        row.style.display=text.includes(keyword)?"":"none";
    });
});
</script>

<?php include '../includes/footer.php'; ?>