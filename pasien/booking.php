<?php

session_start();



if (!isset($_SESSION['id_user']) || $_SESSION['role'] != "Pasien") {

    header("Location: ../auth/login.php");

    exit;

}



include '../config/koneksi.php';

include '../includes/header.php';



$id_user = $_SESSION['id_user'];



// mengambil data pasien berdasarkan user login

$pasien = mysqli_query($conn, "

SELECT *

FROM pasien

WHERE id_user='$id_user'

");



$data_pasien = mysqli_fetch_assoc($pasien);



// mengambil daftar dokter

$dokter = mysqli_query($conn,"

SELECT *

FROM dokter

ORDER BY nama_dokter ASC

");



// =========================================================================

// PROSES SIMPAN DATA BOOKING BEROBAT

// =========================================================================

if(isset($_POST['booking'])){



    $id_pasien = $data_pasien['id_pasien']; // Diambil langsung dari data sesi pasien login

    $id_dokter = $_POST['id_dokter'];

    $tanggal   = $_POST['tanggal_booking'];

    $jam       = $_POST['jam_booking'];

    $keluhan   = mysqli_real_escape_string($conn, $_POST['keluhan']);



    // membuat kode booking otomatis

    $kode_booking = "BK" . date("YmdHis");



    // membuat nomor antrean berdasarkan tanggal booking

    $q = mysqli_query($conn,"

    SELECT MAX(nomor_antrian) AS terakhir 

    FROM booking 

    WHERE tanggal_booking='$tanggal'

    ");



    $d = mysqli_fetch_assoc($q);



    if($d['terakhir']){

        $urut = (int)substr($d['terakhir'], 1);

        $urut++;

    }else{

        $urut = 1;

    }



    $nomor_antrian = "A".str_pad($urut, 3, "0", STR_PAD_LEFT);



    // Proses Simpan ke Database

    mysqli_query($conn,"

        INSERT INTO booking

        (

            kode_booking,

            nomor_antrian,

            id_pasien,

            id_dokter,

            tanggal_booking,

            jam_booking,

            keluhan,

            status

        )

        VALUES

        (

            '$kode_booking',

            '$nomor_antrian',

            '$id_pasien',

            '$id_dokter',

            '$tanggal',

            '$jam',

            '$keluhan',

            'Menunggu Check-in'

        )

    ");



    // =========================================================================

    // UPDATE: MENGAMBIL ID BARU DAN REDIRECT KE HALAMAN CETAK

    // =========================================================================

    $id_booking = mysqli_insert_id($conn);



    echo "

    <script>

        alert('Booking berhasil dibuat');

        window.location='cetak_booking.php?id=$id_booking';

    </script>";

    exit;

}

?>



<div class="container mt-4">



    <div class="card shadow">



        <div class="card-header bg-primary text-white">

            <h4 class="mb-0">📝 Booking Berobat Online</h4>

        </div>



        <div class="card-body">



            <form method="POST">



                <div class="mb-3">

                    <label class="form-label fw-bold">

                        Nama Pasien

                    </label>



                    <input

                        type="text"

                        class="form-control"

                        value="<?= htmlspecialchars($data_pasien['nama'] ?? ''); ?>"

                        readonly>

                </div>



                <div class="mb-3">

                    <label class="form-label fw-bold">

                        Pilih Dokter

                    </label>



                    <select

                        name="id_dokter"

                        class="form-select"

                        required>



                        <option value="">-- Pilih Dokter --</option>



                        <?php while($d = mysqli_fetch_assoc($dokter)){ ?>



                            <option value="<?= $d['id_dokter']; ?>">

                                <?= htmlspecialchars($d['nama_dokter']); ?>

                            </option>



                        <?php } ?>



                    </select>

                </div>



                <div class="mb-3">

                    <label class="form-label fw-bold">

                        Tanggal Booking

                    </label>



                    <input

                        type="date"

                        name="tanggal_booking"

                        class="form-control"

                        min="<?= date('Y-m-d'); ?>"

                        required>

                </div>



                <div class="mb-3">

                    <label class="form-label fw-bold">

                        Jam Booking

                    </label>



                    <input

                        type="time"

                        name="jam_booking"

                        class="form-control"

                        required>

                </div>



                <div class="mb-3">

                    <label class="form-label fw-bold">

                        Keluhan

                    </label>



                    <textarea

                        name="keluhan"

                        class="form-control"

                        rows="4"

                        required></textarea>

                </div>



                <button

                    type="submit"

                    name="booking"

                    class="btn btn-primary">

                    Booking Sekarang

                </button>



            </form>



        </div>



    </div>



</div>



<?php include '../includes/footer.php'; ?>