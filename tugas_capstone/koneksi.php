<?php
$host = "localhost";
$user = "root"; // Ganti jika username Anda berbeda
$password = ""; // Ganti jika password Anda berbeda
$database = "db_cipanten_wisata";

// Buat koneksi
$koneksi = mysqli_connect($host, $user, $password, $database);

// Cek koneksi
if (mysqli_connect_errno()){
	echo "Koneksi database gagal: " . mysqli_connect_error();
    // Hentikan script jika koneksi gagal
    die(); 
}
?>