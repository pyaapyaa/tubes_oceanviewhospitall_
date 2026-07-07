<?php

$host = "sql106.infinityfree.com";
$user = "if0_42357148";
$pass = "1VaffiN9wQJk";
$db   = "if0_42357148_oceanviewhospital";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal!");
}

?>