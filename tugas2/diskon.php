<?php
    // 1. Pembuatan Fungsi (Prosedur) 
    function hitungDiskon($totalBelanja) {
        $diskon = 0; // Inisialisasi diskon

    // 2. Logika Diskon [cite: 81]
    // Kondisi 1: Total belanja >= Rp. 100.000 (Diskon 10%) 
    if ($totalBelanja >= 100000) {
        $diskon = 0.1 * $totalBelanja; 
    } 

    // Kondisi 2: Total belanja >= Rp. 50.000 DAN < Rp. 100.000 (Diskon 5%) 
    // Menggunakan 'elseif' akan secara otomatis mengecek kondisi < Rp. 100.000 karena kondisi pertama tidak terpenuhi.
    elseif ($totalBelanja >= 50000) { 
        $diskon = 0.05 * $totalBelanja;
    }

    // Kondisi 3: Total belanja < Rp. 50.000 (Diskon Rp. 0) 
    // Jika kedua kondisi di atas tidak terpenuhi, diskon tetap 0 (sesuai inisialisasi).

    // 3. Nilai Kembalian (Return Value) [cite: 87]
    return $diskon; 
}

// 4. Eksekusi dan Output (di luar definisi fungsi) [cite: 89, 90]

// Deklarasikan variabel $totalBelanja dan berikan contoh nilai Rp. 120.000 
$totalBelanja = 120000; 

// Panggil fungsi hitungDiskon() dan simpan hasilnya dalam variabel $diskon [cite: 92]
$diskon = hitungDiskon($totalBelanja);

// Hitung total yang harus dibayar 
$totalBayar = $totalBelanja - $diskon; 

echo "## Hasil Perhitungan Diskon ##\n";
echo "------------------------------\n";
echo "Total Belanja Awal: Rp. " . number_format($totalBelanja, 0, ',', '.') . "\n";
echo "Diskon Diterima: Rp. " . number_format($diskon, 0, ',', '.') . "\n";
echo "------------------------------\n";
echo "Total yang Harus Dibayar: Rp. " . number_format($totalBayar, 0, ',', '.') . "\n";

?>