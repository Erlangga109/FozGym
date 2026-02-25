# Troubleshooting Fitur AI

## Masalah yang Dihadapi dan Solusi

### 1. Masalah Parsing JSON
**Masalah**: API mengembalikan format JSON yang tidak langsung terparse
- Respons API mengandung format markdown code block (```json ... ```)
- Respons API terpotong karena batas token maksimum

**Solusi yang Diterapkan**:
- Menambahkan fungsi pembersih respons untuk menghapus markdown code block
- Menaikkan batas token maksimum dari 2000 ke 4000
- Memperbarui prompt agar lebih eksplisit meminta JSON langsung tanpa penjelasan tambahan

### 2. Masalah Model Gemini
**Masalah**: Model yang digunakan tidak ditemukan di API
- Awalnya menggunakan model `gemini-1.5-flash` dan `gemini-pro` yang ternyata tidak tersedia

**Solusi yang Diterapkan**:
- Mengidentifikasi model yang tersedia melalui `list_models.php`
- Mengganti model ke `gemini-pro-latest` yang merupakan model yang paling stabil dan tersedia

### 3. Masalah Respons Terlalu Panjang
**Masalah**: API mengembalikan respons yang terpotong karena batas token
- Model membawa "pikiran internal" yang menghabiskan token

**Solusi yang Diterapkan**:
- Menaikkan batas `maxOutputTokens` untuk Gemini API
- Menaikkan batas `max_tokens` untuk OpenAI API
- Menambahkan instruksi eksplisit ke prompt bahwa model harus mengembalikan JSON langsung

## Status Saat Ini
Fitur AI kini berfungsi dengan baik dan telah diuji. API menghasilkan jadwal latihan lengkap sesuai permintaan pengguna.

## Penggunaan
Sekarang fitur jadwal otomatis (AI) siap digunakan:
1. Pastikan API key diset di `config.php`
2. Pastikan `$AI_PROVIDER` diset ke `'gemini'` atau `'openai'`
3. Akses halaman `/schedules/generate_ai.php`
4. Isi form dengan informasi latihan Anda
5. Klik "Buat Jadwal Otomatis"

Jadwal akan dibuat dan tersedia di halaman `/schedules/index.php`

## Fitur Penghapusan
- Fitur penghapusan sekarang berlaku untuk semua jenis jadwal (baik manual maupun AI)
- Baik jadwal manual maupun jadwal AI bisa dihapus melalui tombol "Hapus" di halaman index maupun halaman detail
- Perbedaan utama: jadwal manual bisa diedit, sedangkan jadwal AI tidak bisa diedit (hanya bisa dilihat dan dihapus)

## Fitur Edit untuk Jadwal AI
- Sekarang jadwal AI bisa dikonversi menjadi jadwal manual agar bisa diedit
- Gunakan tombol "Jadikan Editable" atau "Edit" pada jadwal AI untuk mengonversinya
- Setelah dikonversi, jadwal bisa diedit seperti jadwal manual biasa

## Fitur AI Coach
- AI Coach di halaman `/ai.php` sekarang mendukung kedua layanan AI (OpenAI dan Google Gemini)
- Konfigurasi provider yang digunakan diatur di `config.php` melalui variabel `$AI_PROVIDER`
- Pastikan API key diset di `config.php` agar AI Coach bisa berfungsi secara penuh