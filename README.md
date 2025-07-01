Coding partner

Show thinking
Tentu, ini adalah draf README.md yang cocok untuk proyek sistem pencatatan keuangan Anda, lengkap dengan deskripsi, fitur, teknologi, dan panduan instalasi.

Sistem Pencatatan Keuangan - Kedai UMKM Hana
Proyek ini adalah implementasi dari skripsi berjudul "RANCANG BANGUN SISTEM PENCATATAN KEUANGAN BERBASIS WEB BERDASARKAN TRANSAKSI HARIAN STUDI KASUS KEDAI UMKM HANA KECAMATAN KUOK".


Sistem ini dikembangkan untuk mengatasi permasalahan pencatatan keuangan yang masih dilakukan secara manual menggunakan buku tulis di Kedai UMKM Hana. Tujuannya adalah menyediakan solusi digital berbasis web yang mudah digunakan untuk mencatat transaksi, memantau kondisi keuangan, dan mengelola data setoran barang dari agen secara efisien dan akurat.



Fitur Utama ✨

Manajemen Multi-Peran: Sistem memiliki tiga peran utama dengan hak akses yang terpisah dengan jelas: Pemilik, Karyawan, dan Agen.

Halaman Khusus Sesuai Peran: Setiap peran memiliki halaman khusus yang dirancang untuk tugas utama mereka:


Karyawan: Halaman untuk mencatat pemasukan dan pengeluaran harian dengan cepat.


Agen: Halaman untuk mencatat setoran barang yang mereka kirim ke kedai, lengkap dengan riwayatnya.

Pemilik: Dashboard komprehensif untuk mengelola semua aspek sistem.


Laporan Keuangan Interaktif: Halaman laporan khusus untuk Pemilik yang menampilkan ringkasan statistik (pemasukan, pengeluaran, keuntungan) dan detail transaksi dengan filter rentang tanggal yang dinamis.


Manajemen Data Master: Pemilik dapat mengelola data penting seperti akun pengguna (Karyawan & Agen) dan kategori transaksi.


Monitoring Setoran Agen: Pemilik dapat memantau semua riwayat setoran barang yang dilakukan oleh setiap Agen.

Teknologi yang Digunakan 🛠️
Framework: Laravel 10

Admin Panel: Filament 3

Manajemen Peran: Spatie/laravel-permission

Database: MySQL

Instalasi ⚙️
Berikut adalah langkah-langkah untuk menjalankan proyek ini di lingkungan lokal.

Clone Repository

Bash

git clone [URL_REPOSITORY_ANDA]
cd [NAMA_FOLDER_PROYEK]
Install Dependencies

Bash

composer install
Setup Environment
Salin file .env.example menjadi .env.

Bash

cp .env.example .env
Buka file .env dan sesuaikan konfigurasi database Anda (DB_DATABASE, DB_USERNAME, DB_PASSWORD).

Generate Application Key

Bash

php artisan key:generate
Jalankan Migrasi & Seeder
Perintah ini akan membuat semua tabel database dan mengisinya dengan data awal (peran dan pengguna dummy).

Bash

php artisan migrate:fresh --seed
Jalankan Aplikasi

Bash

php artisan serve
Aplikasi sekarang berjalan di http://127.0.0.1:8000.