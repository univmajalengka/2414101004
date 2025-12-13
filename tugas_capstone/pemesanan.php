<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Paket Wisata - Situ Cipanten</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <header class="bg-teal-800 text-white shadow-lg fixed top-0 w-full z-50">
    <div class="container mx-auto flex justify-between items-center p-4">
        <div class="text-3xl font-bold">Situ Cipanten</div>

        <nav class="hidden md:flex space-x-6 text-base">
        <a href="index.php" class="hover:text-teal-200 transition duration-300"
            >Beranda</a
          >
          <a href="index.php" class="hover:text-teal-200 transition duration-300"
            >About</a
          >
          <a
            href="index.php"
            class="hover:text-teal-200 transition duration-300"
            >Fasilitas Wisata</a
          >
          <a href="index.php" class="hover:text-teal-200 transition duration-300"
            >Paket Wisata</a
          >
          <a
            href="pemesanan.php"
            class="hover:text-teal-200 transition duration-300"
            >Pemesanan</a
          >
          <a
            href="modifikasi_pesanan.php"
            class="hover:text-teal-200 transition duration-300"
            >Modifikasi Pesanan
          </a>
          <a href="index.php" class="hover:text-teal-200 transition duration-300"
            >Galery</a
          >
        </nav>

        <button id="menu-button" class="md:hidden p-2 focus:outline-none">
          <svg
            class="w-8 h-8"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"
            ></path>
          </svg>
        </button>
      </div>

      <nav
        id="mobile-menu"
        class="hidden md:hidden bg-teal-700 w-full transition-all duration-300"
      >
        <a
          href="#hero"
          class="block py-3 px-4 text-center border-b border-teal-600 hover:bg-teal-600"
          >Beranda</a
        >
        <a
          href="#about"
          class="block py-3 px-4 text-center border-b border-teal-600 hover:bg-teal-600"
          >About</a
        >
        <a
          href="#fasilitas"
          class="block py-3 px-4 text-center border-b border-teal-600 hover:bg-teal-600"
          >Fasilitas Wisata</a
        >
        <a
          href="#paket"
          class="block py-3 px-4 text-center border-b border-teal-600 hover:bg-teal-600"
          >Paket Wisata</a
        >
        <a
          href="#pemesanan"
          class="block py-3 px-4 text-center border-b border-teal-600 hover:bg-teal-600"
          >Pemesanan</a
        >
        <a href="#galery" class="block py-3 px-4 text-center hover:bg-teal-600"
          >Galery</a
        >
      </nav>
    </header>
    
    <section id="pemesanan-form" class="container mx-auto py-20 px-4 scroll-mt-[90px] pt-[90px]">
        <h1 class="text-4xl font-bold text-teal-700 mb-8 text-center">Form Pemesanan Paket Wisata</h1>
        
        <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-2xl">
            <form id="formPemesanan" method="POST" action="proses_simpan.php" onsubmit="return validateForm()"> 

                <div id="validation-message" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 hidden" role="alert">
                    <p>⚠️ Mohon lengkapi semua data wajib pada formulir.</p>
                </div>

                <div class="mb-4">
                    <label for="nama" class="block text-gray-700 font-semibold mb-2">Nama Pemesan</label>
                    <input type="text" id="nama" name="nama" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required>
                </div>
                
                <div class="mb-4">
                    <label for="hp" class="block text-gray-700 font-semibold mb-2">Nomor HP/Telp</label>
                    <input type="tel" id="hp" name="hp" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required>
                </div>
                
                <div class="mb-4">
                    <label for="tgl_pesan" class="block text-gray-700 font-semibold mb-2">Tanggal Pesan</label>
                    <input type="date" id="tgl_pesan" name="tgl_pesan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required>
                </div>
                
                <div class="mb-6">
                    <label for="waktu_perjalanan" class="block text-gray-700 font-semibold mb-2">Waktu Pelaksanaan Perjalanan (Hari)</label>
                    <input type="number" id="waktu_perjalanan" name="waktu_perjalanan" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required>
                </div>

                <div class="mb-6 border p-4 rounded-lg bg-gray-50">
                    <p class="block text-gray-700 font-semibold mb-3">Pelayanan Paket Perjalanan (Pilih salah satu atau lebih)</p>
                    
                    <div class="space-y-2">
                        <label class="flex items-center text-gray-800">
                            <input type="checkbox" id="penginapan" name="layanan[]" value="Penginapan" class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500" data-harga="1000000">
                            <span class="ml-2">Penginapan (Rp 1.000.000)</span>
                        </label>
                        <label class="flex items-center text-gray-800">
                            <input type="checkbox" id="transportasi" name="layanan[]" value="Transportasi" class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500" data-harga="1200000">
                            <span class="ml-2">Transportasi (Rp 1.200.000)</span>
                        </label>
                        <label class="flex items-center text-gray-800">
                            <input type="checkbox" id="makan" name="layanan[]" value="Service/Makan" class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500" data-harga="500000">
                            <span class="ml-2">Service/Makan (Rp 500.000)</span>
                        </label>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="jumlah_peserta" class="block text-gray-700 font-semibold mb-2">Jumlah Peserta</label>
                    <input type="number" id="jumlah_peserta" name="jumlah_peserta" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required>
                </div>

                <div class="mb-4">
                    <label for="harga_paket" class="block text-gray-700 font-semibold mb-2">Harga Paket Perjalanan (Total Layanan)</label>
                    <input type="text" id="harga_paket" name="harga_paket_display" class="w-full px-4 py-2 bg-gray-200 border border-gray-300 rounded-lg" readonly value="Rp 0">
                    <input type="hidden" id="hidden_harga_paket" name="hidden_harga_paket" value="0"> 
                </div>
                
                <div class="mb-6">
                    <label for="jumlah_tagihan" class="block text-gray-700 font-semibold mb-2">Jumlah Tagihan</label>
                    <input type="text" id="jumlah_tagihan" name="jumlah_tagihan_display" class="w-full px-4 py-2 bg-gray-200 border border-gray-300 rounded-lg" readonly value="Rp 0">
                    <input type="hidden" id="hidden_jumlah_tagihan" name="hidden_jumlah_tagihan" value="0">
                </div>

                <div class="flex space-x-4">
                    <!-- <button type="button" onclick="hitungTagihan()" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition shadow-md">Hitung</button> -->
                    <button type="submit" class="bg-teal-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-teal-700 transition shadow-md">Simpan</button>
                    <button type="reset" class="bg-red-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-600 transition shadow-md">Reset</button>
                </div>
            </form>
        </div>
    </section>
    
    <script>
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
            return rupiah ? 'Rp ' + rupiah : 'Rp 0'; // Mengubah dari '' menjadi 'Rp 0' agar lebih bersih
        }

        // Fungsi utama untuk menghitung Harga Paket dan Jumlah Tagihan
        function hitungTagihan() {
            // Ambil input dari form. Gunakan || 0 untuk memastikan nilai adalah angka
            const waktuPerjalanan = parseInt(document.getElementById('waktu_perjalanan').value) || 0;
            const jumlahPeserta = parseInt(document.getElementById('jumlah_peserta').value) || 0;
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="layanan[]"]');
            
            let hargaPaket = 0;
            
            // 1. Hitung Harga Paket Perjalanan (Total Layanan yang dipilih) [cite: 57]
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    // Ambil harga dari atribut data-harga
                    // Penginapan: 1.000.000 [cite: 22, 53], Transportasi: 1.200.000 [cite: 23, 54], Makan: 500.000 [cite: 24, 56]
                    hargaPaket += parseInt(checkbox.getAttribute('data-harga')); 
                }
            });

            // 2. Hitung Jumlah Tagihan
            // Rumus: Waktu Perjalanan (Hari) x Jumlah Peserta x Harga Paket Perjalanan [cite: 28, 58]
            const jumlahTagihan = waktuPerjalanan * jumlahPeserta * hargaPaket;

            // 3. Update tampilan (dengan format Rupiah) dan hidden fields (nilai murni untuk PHP)
            document.getElementById('harga_paket').value = formatRupiah(hargaPaket);
            document.getElementById('hidden_harga_paket').value = hargaPaket; // Nilai murni untuk PHP
            
            document.getElementById('jumlah_tagihan').value = formatRupiah(jumlahTagihan);
            document.getElementById('hidden_jumlah_tagihan').value = jumlahTagihan; // Nilai murni untuk PHP
        }

        // Fungsi untuk validasi semua field wajib terisi sebelum submit [cite: 50]
        function validateForm() {
            const requiredInputs = document.querySelectorAll('input[required]');
            let allValid = true;

            requiredInputs.forEach(input => {
                // Cek jika field kosong
                if (!input.value.trim()) {
                    allValid = false;
                    input.classList.add('border-red-500', 'ring-1', 'ring-red-500'); 
                } else {
                    input.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
                }
            });

            // Tambahan: Memastikan user sudah klik Hitung (atau perhitungan otomatis sudah berjalan)
            if (document.getElementById('hidden_harga_paket').value == 0) {
                // Cek apakah ada layanan yang dipilih tetapi harga masih 0
                const isServiceSelected = document.querySelectorAll('input[type="checkbox"][name="layanan[]"]:checked').length > 0;
                
                // Hanya cek jika sudah ada layanan dan input wajib terisi
                if (allValid && isServiceSelected) {
                    document.getElementById('validation-message').innerHTML = '⚠️ Mohon klik tombol **Hitung** terlebih dahulu untuk mengkonfirmasi tagihan.';
                    document.getElementById('validation-message').classList.remove('hidden');
                    allValid = false;
                }
            }

            const validationMsg = document.getElementById('validation-message');
            if (!allValid) {
                validationMsg.classList.remove('hidden'); // Tampilkan pesan error
                // Pastikan pesan default kembali jika error bukan karena belum hitung
                if (validationMsg.innerHTML.includes('lengkapi')) {
                    validationMsg.innerHTML = '⚠️ Mohon lengkapi semua data wajib pada formulir.';
                }
                return false;
            } else {
                validationMsg.classList.add('hidden');
                return true;
            }
        }
        
        // Blok kode yang memastikan perhitungan otomatis berjalan:
        document.addEventListener('DOMContentLoaded', () => {
            // Listens on input change: Waktu Perjalanan dan Jumlah Peserta
            const inputsToListen = ['waktu_perjalanan', 'jumlah_peserta'];
            inputsToListen.forEach(id => {
                // 'change' event works when input loses focus after changing value
                document.getElementById(id).addEventListener('change', hitungTagihan);
                // 'input' event makes it more responsive as user types
                document.getElementById(id).addEventListener('input', hitungTagihan);
            });
            
            // Listens on checkbox changes
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="layanan[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', hitungTagihan);
            });

            // Panggil hitungTagihan() sekali saat halaman dimuat
            // Ini penting jika Anda menggunakan script yang sama untuk mode 'Edit'
            hitungTagihan(); 
        });
    </script>
</body>
</html>