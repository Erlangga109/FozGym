# Dokumentasi Fitur Jadwal Latihan

## Ringkasan
Fitur ini menambahkan kemampuan untuk membuat, mengelola, dan mengikuti jadwal latihan di FozGym. Terdapat dua jenis jadwal:
1. **Jadwal Manual**: Dibuat secara manual oleh pengguna
2. **Jadwal Otomatis (AI)**: Dibuat otomatis berdasarkan input pengguna menggunakan OpenAI API

## Struktur Database
Ditambahkan dua tabel baru:
- `workout_schedules`: Menyimpan informasi jadwal latihan
- `schedule_days`: Menyimpan detail latihan harian untuk setiap jadwal

## Halaman yang Ditambahkan
- `/schedules/index.php` - Daftar semua jadwal latihan pengguna
- `/schedules/create.php` - Membuat jadwal latihan manual
- `/schedules/view.php` - Melihat detail jadwal
- `/schedules/edit.php` - Mengedit informasi umum jadwal manual
- `/schedules/edit_day.php` - Mengedit latihan harian dalam jadwal
- `/schedules/delete.php` - Menghapus jadwal manual
- `/schedules/generate_ai.php` - Form untuk membuat jadwal otomatis dengan AI
- `/schedules/process_ai.php` - Proses pembuatan jadwal otomatis dengan OpenAI

## Fungsi Utama
1. **Manajemen Jadwal Manual**:
   - Membuat jadwal baru
   - Mengedit informasi jadwal
   - Menambahkan/mengedit latihan harian
   - Menghapus jadwal

2. **Pembuatan Jadwal Otomatis**:
   - Mengumpulkan informasi dari user (target otot, tujuan latihan, tingkat pengalaman, dll.)
   - Menggunakan OpenAI API untuk menghasilkan jadwal latihan yang disesuaikan
   - Menyimpan hasil dari AI ke dalam format jadwal

## Kebutuhan
- API key OpenAI atau Gemini harus diset di `config.php` untuk fitur AI
- Tabel database harus di-migrate menggunakan `migrate_schedules.php`

## Dukungan AI Provider
Sistem mendukung dua layanan AI yang bisa digunakan baik untuk fitur jadwal otomatis maupun AI Coach:

### OpenAI API
- Berbasis GPT-4o-mini
- Berbayar sesuai penggunaan
- Kualitas tinggi untuk generate jadwal latihan dan saran latihan

### Google Gemini API (Rekomendasi)
- Berbasis Gemini 1.5 Flash / gemini-pro-latest
- Menyediakan kuota gratis bulanan
- Biaya lebih terjangkau
- Konfigurasi: `$AI_PROVIDER = 'gemini'` di `config.php`

## Setup Gemini API
Lihat file `GEMINI_SETUP.md` untuk panduan langkah-demi-langkah cara mendapatkan dan mengkonfigurasi Gemini API.

## Troubleshooting
Jika mengalami masalah dengan fitur AI, lihat file `TROUBLESHOOTING.md` untuk solusi umum dan catatan perbaikan yang telah dilakukan.

## Akses
- Menu jadwal ditampilkan di header untuk role `customer` dan `trainer`
- Setiap user hanya bisa mengakses jadwal miliknya sendiri

## Fitur Jadwal
- **Jadwal Manual**: Bisa dibuat, diedit, dan dihapus sesuai kebutuhan
- **Jadwal AI**: Bisa dibuat otomatis dan dihapus, serta bisa dikonversi ke mode manual agar bisa diedit
- **Penghapusan**: Kedua jenis jadwal bisa dihapus sesuai kebutuhan

## Contoh Penggunaan
1. Untuk membuat jadwal manual:
   - Buka `/schedules/create.php`
   - Isi informasi jadwal
   - Setelah dibuat, gunakan `/schedules/edit_day.php` untuk menambahkan latihan harian

2. Untuk membuat jadwal otomatis:
   - Buka `/schedules/generate_ai.php`
   - Isi informasi latihan (target otot, tujuan, dll.)
   - Sistem akan menghubungi OpenAI dan membuatkan jadwal otomatis