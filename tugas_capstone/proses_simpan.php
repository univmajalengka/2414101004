<?php
include 'koneksi.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil dan bersihkan data POST
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $hp = mysqli_real_escape_string($koneksi, $_POST['hp']);
    $tgl_pesan = mysqli_real_escape_string($koneksi, $_POST['tgl_pesan']);
    $waktu_hari = (int)$_POST['waktu_perjalanan'];
    $jumlah_peserta = (int)$_POST['jumlah_peserta'];
    $harga_paket = (float)$_POST['hidden_harga_paket']; 
    $jumlah_tagihan = (float)$_POST['hidden_jumlah_tagihan']; 

    // 2. Cek Layanan (Set 'Y' atau 'N')
    $penginapan = isset($_POST['layanan']) && in_array('Penginapan', $_POST['layanan']) ? 'Y' : 'N';
    $transportasi = isset($_POST['layanan']) && in_array('Transportasi', $_POST['layanan']) ? 'Y' : 'N';
    $service_makan = isset($_POST['layanan']) && in_array('Service/Makan', $_POST['layanan']) ? 'Y' : 'N';
        
    // 3. Query INSERT data
    $sql = "INSERT INTO pesanan (nama_pemesan, nomor_hp, tanggal_pesan, waktu_hari, jumlah_peserta, 
                penginapan, transportasi, service_makan, harga_paket, jumlah_tagihan)
            VALUES ('$nama', '$hp', '$tgl_pesan', $waktu_hari, $jumlah_peserta, 
                    '$penginapan', '$transportasi', '$service_makan', $harga_paket, $jumlah_tagihan)";

    if (mysqli_query($koneksi, $sql)) {
        // Redirect ke halaman daftar pesanan setelah berhasil
        header("Location: modifikasi_pesanan.php?status=success");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($koneksi);
    }

    mysqli_close($koneksi);
} else {
    // Jika diakses tidak melalui metode POST
    header("Location: pemesanan.php");
    exit();
}
?>