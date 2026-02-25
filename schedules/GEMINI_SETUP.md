# Panduan Setup Google Gemini API

## Cara Mendapatkan API Key Gemini

### 1. Buka Google AI Studio
- Kunjungi: https://aistudio.google.com/
- Login menggunakan akun Google Anda

### 2. Buat Project Baru
- Klik "Get API Key" atau "Create API Key"
- Ikuti instruksi untuk membuat project di Google Cloud Console
- Aktifkan Gemini API di project tersebut

### 3. Dapatkan API Key
- Setelah project dibuat, salin API key yang disediakan
- Simpan dengan aman karena hanya akan ditampilkan sekali

### 4. Konfigurasi di FozGym
- Buka file `config.php`
- Masukkan API key ke variabel `$GEMINI_API_KEY`
- Pastikan `$AI_PROVIDER` diset sebagai `'gemini'` untuk menggunakan layanan ini

```php
$GEMINI_API_KEY = 'masukkan_api_key_anda_disini';
$AI_PROVIDER = 'gemini';
```

## Keunggulan Gemini API
- **Kuota Gratis**: Google menyediakan kuota penggunaan gratis setiap bulan
- **Model Rekomendasi**: Gemini 2.0 Flash adalah model yang paling stabil dan kompatibel untuk berbagai fitur
- **Biaya Terjangkau**: Jika melewati kuota gratis, biayanya lebih murah daripada OpenAI

## Catatan Model
- Jika mengalami masalah dengan model `gemini-pro-latest`, coba dengan model `gemini-2.0-flash`
- Beberapa model terbaru (seperti `gemini-2.5-pro`) memiliki fitur "reasoning" yang bisa mengurangi ketersediaan respons teks

## Catatan Penting
- Jaga kerahasiaan API key Anda
- Jangan menyimpan API key dalam kode yang bisa diakses publik
- Monitor penggunaan API Anda di Google AI Studio
- Tersedia kuota gratis yang cukup untuk aplikasi latihan seperti FozGym