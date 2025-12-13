<?php
include 'koneksi.php';

// 1. Ambil ID dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Jika ID tidak ada, arahkan kembali
    header("Location: modifikasi_pesanan.php");
    exit();
}
$id_pesanan = $_GET['id'];

// 2. Ambil data pesanan lama dari database
// CATATAN: Pastikan nama kolom di query ini sudah benar: id_pesanan
$query_data = "SELECT * FROM pesanan WHERE id_pesanan = '$id_pesanan'";
$result_data = mysqli_query($koneksi, $query_data);

if (mysqli_num_rows($result_data) === 0) {
    die("Data pesanan tidak ditemukan.");
}

$data_lama = mysqli_fetch_assoc($result_data);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pemesanan Paket Wisata - Situ Cipanten</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    
    <section id="pemesanan-form" class="container mx-auto py-10 px-4">
        <h1 class="text-3xl sm:text-4xl font-bold text-teal-700 mb-8 text-center">Form Edit Pesanan (ID: <?= $id_pesanan ?>)</h1>
        
        <div class="max-w-4xl mx-auto bg-white p-6 sm:p-8 rounded-xl shadow-2xl">
            
            <form id="formPemesanan" method="POST" action="proses_edit.php" onsubmit="return validateForm()"> 

                <input type="hidden" name="id_pesanan" value="<?= htmlspecialchars($id_pesanan) ?>">

                <div id="validation-message" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 hidden" role="alert">
                    <p>⚠️ Mohon lengkapi semua data wajib pada formulir.</p>
                </div>

                <div class="mb-4">
                    <label for="nama" class="block text-gray-700 font-semibold mb-2">Nama Pemesan</label>
                    <input type="text" id="nama" name="nama_pemesan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required value="<?= htmlspecialchars($data_lama['nama_pemesan']) ?>">
                </div>
                
                <div class="mb-4">
                    <label for="hp" class="block text-gray-700 font-semibold mb-2">Nomor HP/Telp</label>
                    <input type="tel" id="hp" name="nomor_hp" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required value="<?= htmlspecialchars($data_lama['nomor_hp']) ?>">
                </div>
                
                <div class="mb-4">
                    <label for="tgl_pesan" class="block text-gray-700 font-semibold mb-2">Tanggal Pesan</label>
                    <input type="date" id="tgl_pesan" name="tanggal_pesan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required value="<?= htmlspecialchars($data_lama['tanggal_pesan']) ?>">
                </div>
                
                <div class="mb-6">
                    <label for="waktu_perjalanan" class="block text-gray-700 font-semibold mb-2">Waktu Pelaksanaan Perjalanan (Hari)</label>
                    <input type="number" id="waktu_perjalanan" name="waktu_hari" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required value="<?= htmlspecialchars($data_lama['waktu_hari']) ?>">
                </div>

                <div class="mb-6 border p-4 rounded-lg bg-gray-50">
                    <p class="block text-gray-700 font-semibold mb-3">Pelayanan Paket Perjalanan (Pilih salah satu atau lebih)</p>
                    
                    <div class="space-y-2">
                        <label class="flex items-center text-gray-800">
                            <input type="checkbox" id="penginapan" name="layanan[]" value="Penginapan" class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500" data-harga="1000000" <?= ($data_lama['penginapan'] == 'Y') ? 'checked' : '' ?>>
                            <span class="ml-2">Penginapan (Rp 1.000.000)</span>
                        </label>
                        <label class="flex items-center text-gray-800">
                            <input type="checkbox" id="transportasi" name="layanan[]" value="Transportasi" class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500" data-harga="1200000" <?= ($data_lama['transportasi'] == 'Y') ? 'checked' : '' ?>>
                            <span class="ml-2">Transportasi (Rp 1.200.000)</span>
                        </label>
                        <label class="flex items-center text-gray-800">
                            <input type="checkbox" id="makan" name="layanan[]" value="Service/Makan" class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500" data-harga="500000" <?= ($data_lama['service_makan'] == 'Y') ? 'checked' : '' ?>>
                            <span class="ml-2">Service/Makan (Rp 500.000)</span>
                        </label>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="jumlah_peserta" class="block text-gray-700 font-semibold mb-2">Jumlah Peserta</label>
                    <input type="number" id="jumlah_peserta" name="jumlah_peserta" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required value="<?= htmlspecialchars($data_lama['jumlah_peserta']) ?>">
                </div>

                <div class="mb-4">
                    <label for="harga_paket" class="block text-gray-700 font-semibold mb-2">Harga Paket Perjalanan (Total Layanan)</label>
                    <input type="text" id="harga_paket" name="harga_paket_display" class="w-full px-4 py-2 bg-gray-200 border border-gray-300 rounded-lg" readonly value="<?= 'Rp ' . number_format($data_lama['harga_paket'], 0, ',', '.') ?>">
                    <input type="hidden" id="hidden_harga_paket" name="hidden_harga_paket" value="<?= htmlspecialchars($data_lama['harga_paket']) ?>"> 
                </div>
                
                <div class="mb-6">
                    <label for="jumlah_tagihan" class="block text-gray-700 font-semibold mb-2">Jumlah Tagihan</label>
                    <input type="text" id="jumlah_tagihan" name="jumlah_tagihan_display" class="w-full px-4 py-2 bg-gray-200 border border-gray-300 rounded-lg" readonly value="<?= 'Rp ' . number_format($data_lama['jumlah_tagihan'], 0, ',', '.') ?>">
                    <input type="hidden" id="hidden_jumlah_tagihan" name="hidden_jumlah_tagihan" value="<?= htmlspecialchars($data_lama['jumlah_tagihan']) ?>">
                </div>

                <div class="flex flex-col space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
                    <!-- <button type="button" onclick="hitungTagihan()" class="w-full sm:w-auto bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition shadow-md">Hitung Ulang Tagihan</button> -->
                    <button type="submit" class="w-full sm:w-auto bg-teal-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-teal-700 transition shadow-md">Simpan Perubahan</button>
                    <button type="reset" class="w-full sm:w-auto bg-gray-400 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-500 transition shadow-md">Reset Form</button>
                </div>
            </form>
        </div>
    </section>
    
    <script>
        // ... (Kode JavaScript hitungTagihan dan validateForm sama, tidak diubah) ...
        // Fungsi untuk memformat angka ke Rupiah
        function formatRupiah(angka, prefix) {
            let number_string = angka.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah ? 'Rp ' + rupiah : 'Rp 0';
        }

        // Fungsi utama untuk menghitung Harga Paket dan Jumlah Tagihan
        function hitungTagihan() {
            // Mengambil nilai dari input dengan ID yang sesuai
            const waktuPerjalanan = parseInt(document.getElementById('waktu_perjalanan').value) || 0;
            const jumlahPeserta = parseInt(document.getElementById('jumlah_peserta').value) || 0;
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="layanan[]"]');
            
            let hargaPaket = 0;
            
            // 1. Hitung Harga Paket Perjalanan (Total Layanan yang dipilih)
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    hargaPaket += parseInt(checkbox.getAttribute('data-harga')); 
                }
            });

            // 2. Hitung Jumlah Tagihan
            const jumlahTagihan = waktuPerjalanan * jumlahPeserta * hargaPaket;

            // 3. Update tampilan dan hidden input
            document.getElementById('harga_paket').value = formatRupiah(hargaPaket);
            document.getElementById('hidden_harga_paket').value = hargaPaket;
            
            document.getElementById('jumlah_tagihan').value = formatRupiah(jumlahTagihan);
            document.getElementById('hidden_jumlah_tagihan').value = jumlahTagihan;
        }

        // Fungsi untuk validasi semua field wajib terisi sebelum submit
        function validateForm() {
            const requiredInputs = document.querySelectorAll('input[required]');
            let allValid = true;

            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    allValid = false;
                    input.classList.add('border-red-500', 'ring-1', 'ring-red-500'); 
                } else {
                    input.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
                }
            });
            
            // Logika validasi harus hitung
            if (document.getElementById('hidden_harga_paket').value == 0) {
                const isServiceSelected = document.querySelectorAll('input[type="checkbox"][name="layanan[]"]:checked').length > 0;
                
                if (allValid && isServiceSelected) {
                    document.getElementById('validation-message').innerHTML = '⚠️ Mohon klik tombol **Hitung Ulang Tagihan** terlebih dahulu untuk mengkonfirmasi tagihan.';
                    document.getElementById('validation-message').classList.remove('hidden');
                    allValid = false;
                }
            }

            const validationMsg = document.getElementById('validation-message');
            if (!allValid) {
                validationMsg.classList.remove('hidden'); 
                if (validationMsg.innerHTML.includes('lengkapi')) {
                    validationMsg.innerHTML = '⚠️ Mohon lengkapi semua data wajib pada formulir.';
                }
                return false;
            } else {
                validationMsg.classList.add('hidden');
                return true;
            }
        }
        
        // Event Listener untuk Perhitungan Otomatis saat ada perubahan
        document.addEventListener('DOMContentLoaded', () => {
            // CATATAN: ID pada JS sudah benar (waktu_perjalanan) karena ini ID, bukan name
            const inputsToListen = ['waktu_perjalanan', 'jumlah_peserta'];
            inputsToListen.forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('change', hitungTagihan);
                    element.addEventListener('input', hitungTagihan);
                }
            });
            
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="layanan[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', hitungTagihan);
            });
        });
    </script>
</body>
</html>