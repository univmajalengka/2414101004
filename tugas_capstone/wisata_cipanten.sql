-- 1. Membuat Database 
CREATE DATABASE IF NOT EXISTS db_cipanten_wisata;
USE db_cipanten_wisata;

-- 2. Membuat Tabel Pesanan
-- Tabel ini akan menyimpan semua data pemesanan yang diinput dari form.
CREATE TABLE pesanan (
    -- ID Pesanan sebagai Primary Key dan Auto Increment
    id_pesanan INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    
    -- Data Pemesan
    nama_pemesan VARCHAR(100) NOT NULL,
    nomor_hp VARCHAR(15) NOT NULL,
    
    -- Data Perjalanan
    tanggal_pesan DATE NOT NULL,
    waktu_hari INT(3) NOT NULL COMMENT 'Waktu Pelaksanaan Perjalanan (Hari)',
    
    -- Layanan yang Dipilih (Menggunakan CHAR(1) untuk 'Y' atau 'N')
    penginapan CHAR(1) NOT NULL DEFAULT 'N' COMMENT 'Y=Pilih, N=Tidak Pilih',
    transportasi CHAR(1) NOT NULL DEFAULT 'N' COMMENT 'Y=Pilih, N=Tidak Pilih',
    service_makan CHAR(1) NOT NULL DEFAULT 'N' COMMENT 'Y=Pilih, N=Tidak Pilih (Service/Makan)',
    
    jumlah_peserta INT(5) NOT NULL,
    
    -- Data Harga (Menggunakan BIGINT atau DECIMAL untuk mata uang)
    harga_paket BIGINT(20) NOT NULL COMMENT 'Total Biaya Layanan yang Dipilih',
    jumlah_tagihan BIGINT(20) NOT NULL COMMENT 'Total Tagihan (Hari x Peserta x Harga Paket)',
    
    -- Kolom Opsional: Waktu pembuatan data
    tanggal_input TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Contoh Data Awal (Opsional, untuk pengujian)
INSERT INTO pesanan (nama_pemesan, nomor_hp, tanggal_pesan, waktu_hari, penginapan, transportasi, service_makan, jumlah_peserta, harga_paket, jumlah_tagihan) VALUES
('LSP INFORMATIKA', '085719195627', '2025-01-29', 2, 'N', 'Y', 'Y', 1, 1700000, 3400000),
('Muhaemin Test', '08125678', '2025-02-15', 1, 'Y', 'N', 'Y', 5, 1500000, 7500000),
('Sudirman', '081286638807', '2025-03-01', 1, 'Y', 'Y', 'N', 2, 2200000, 4400000);