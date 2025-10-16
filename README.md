# Testing Espay - Laravel Virtual Account Integration

Aplikasi testing untuk integrasi **Espay Payment Gateway** dengan fokus pada fitur **Virtual Account (VA)**. Dibangun menggunakan **Laravel 12** dengan **TailwindCSS 4** untuk UI yang modern dan responsif.

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Penggunaan](#-penggunaan)
- [API Endpoints](#-api-endpoints)
- [Database Schema](#-database-schema)
- [Scheduled Commands](#-scheduled-commands)
- [Testing](#-testing)
- [Troubleshooting](#-troubleshooting)

## ✨ Fitur Utama

### 1. **Manajemen Virtual Account**
- ✅ Create VA (SENDINVOICE API)
- ✅ Update VA (SENDINVOICE dengan flag update=Y)
- ✅ Delete VA (DELETE-VA API dengan signature RSA)
- ✅ List semua VA dengan status (ACTIVE, EXPIRED, FAILED)
- ✅ Auto-expire VA berdasarkan `expired_date`

### 2. **Payment Callback Handler**
- ✅ Menerima notifikasi pembayaran dari Espay
- ✅ Menyimpan transaksi ke database
- ✅ Response sesuai format Espay API

### 3. **Transaction Management**
- ✅ List semua transaksi
- ✅ Detail transaksi dengan informasi lengkap
- ✅ Status tracking (PAID, FAILED, PENDING)

## 🛠 Tech Stack

- **Framework**: Laravel 12.x
- **PHP**: ^8.2
- **Database**: MySQL
- **Frontend**: 
  - TailwindCSS 4.0
  - Blade Templates
  - Heroicons (via blade-heroicons)
  - Vite
- **Testing**: Pest PHP
- **Queue**: Database driver
- **Cache**: Database driver

## 📦 Persyaratan Sistem

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/MariaDB
- OpenSSL (untuk signature RSA)

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone <repository-url>
cd testing-espay
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=testing_espay
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Konfigurasi Espay

Tambahkan kredensial Espay di `.env`:

```env
ESPAY_SIGNATURE_KEY=your_signature_key_here
ESPAY_MERCHANT_CODE=your_merchant_code_here
```

### 6. Generate Private Key (untuk Delete VA)

```bash
# Generate private key
openssl genrsa -out storage/app/private.pem 2048

# Generate public key (kirim ke Espay)
openssl rsa -in storage/app/private.pem -pubout -out storage/app/public.pem
```

### 7. Migrasi Database

```bash
php artisan migrate
```

### 8. Build Assets

```bash
npm run build
```

## ⚙️ Konfigurasi

### Espay Configuration

File: `config/espay.php`

```php
return [
    'signature_key' => env('ESPAY_SIGNATURE_KEY'),
    'merchant_code' => env('ESPAY_MERCHANT_CODE'),
];
```

### Queue & Cache

Aplikasi menggunakan database driver untuk queue dan cache. Pastikan tabel sudah ter-migrate dengan benar.

## 💻 Penggunaan

### Development Mode

Gunakan composer script untuk menjalankan semua service sekaligus:

```bash
composer dev
```

Script ini akan menjalankan:
- PHP Development Server (port 8000)
- Queue Worker
- Laravel Pail (log viewer)
- Vite Dev Server

Atau jalankan manual:

```bash
# Terminal 1: Laravel Server
php artisan serve

# Terminal 2: Queue Worker
php artisan queue:listen

# Terminal 3: Vite
npm run dev
```

### Production Build

```bash
composer setup
```

Script ini akan:
1. Install composer dependencies
2. Copy .env.example ke .env (jika belum ada)
3. Generate application key
4. Run migrations
5. Install npm dependencies
6. Build assets

## 🔌 API Endpoints

### Web Routes

| Method | URI | Action | Description |
|--------|-----|--------|-------------|
| GET | `/` | DashboardController@index | Dashboard utama |
| GET/POST | `/va` | VirtualAccountController | CRUD Virtual Account |
| GET | `/transactions` | TransactionController@index | List transaksi |
| GET | `/transactions/{id}` | TransactionController@show | Detail transaksi |

### API Routes (Callback)

| Method | URI | Action | Description |
|--------|-----|--------|-------------|
| POST | `/api/v1.0/transfer-va/payment` | EspayController@receive | Callback pembayaran dari Espay |
| POST | `/api/v1.0/testing-with-body` | EspayController@testingWithBody | Testing endpoint |

**Note**: API routes tidak menggunakan CSRF protection.

## 🗄 Database Schema

### Table: `espay_virtualaccount`

| Column | Type | Description |
|--------|------|-------------|
| id | UUID | Primary key |
| rq_uuid | String | UUID request ke Espay |
| rq_datetime | DateTime | Waktu request |
| rs_datetime | DateTime | Waktu response |
| order_id | String | Order ID unik |
| ccy | String(5) | Mata uang (default: IDR) |
| comm_code | String(50) | Kode merchant |
| bank_code | String(10) | Kode bank |
| va_expired | Integer | Durasi expired (menit) |
| expired_date | DateTime | Tanggal kadaluarsa |
| va_number | String(50) | Nomor VA dari Espay |
| error_code | String(10) | Kode error |
| error_message | String(255) | Pesan error |
| description | String(255) | Deskripsi |
| signature | String(255) | SHA256 signature |
| update_flag | Enum(Y/N) | Flag create/update |
| remark1 | String | No HP pelanggan |
| remark2 | String | Nama pelanggan |
| remark3 | String | Email pelanggan |
| remark4 | String | Keterangan tambahan |
| status | Enum | ACTIVE, EXPIRED, FAILED |

### Table: `transaction`

| Column | Type | Description |
|--------|------|-------------|
| id | BigInt | Primary key |
| trx_id | String | ID transaksi (unique) |
| payment_request_id | String | Payment request ID |
| va_number | String | Nomor VA |
| customer_no | String | Nomor customer |
| paid_amount | Decimal(15,2) | Jumlah dibayar |
| total_amount | Decimal(15,2) | Total tagihan |
| currency | String(5) | Mata uang |
| status | String(20) | PAID, FAILED, PENDING |
| trx_datetime | Timestamp | Waktu transaksi |
| paid_at | Timestamp | Waktu pembayaran |
| member_code | String | Kode member |
| debit_from | String | Rekening debit |
| debit_from_name | String | Nama pemilik debit |
| debit_from_bank | String | Bank debit |
| credit_to | String | Rekening kredit |
| credit_to_name | String | Nama pemilik kredit |
| credit_to_bank | String | Bank kredit |
| product_code | String | Kode produk |
| product_value | String | Nilai produk |
| fee_type | String | Tipe fee |
| tx_fee | Decimal(15,2) | Biaya transaksi |
| payment_ref | String | Referensi pembayaran |
| user_id | String | User ID |

## ⏰ Scheduled Commands

### Expire Virtual Accounts

Command untuk menandai VA yang sudah kadaluarsa:

```bash
php artisan va:expire
```

**Cara kerja:**
- Mencari semua VA dengan status `ACTIVE`
- Mengecek `expired_date <= now()`
- Update status menjadi `EXPIRED`

**Setup Cron (Production):**

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Tambahkan di `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('va:expire')->everyMinute();
}
```

## 🧪 Testing

Aplikasi menggunakan **Pest PHP** untuk testing.

```bash
# Run all tests
composer test

# atau
php artisan test
```

## 🔐 Security Notes

1. **Private Key**: Simpan `private.pem` di `storage/app/` dan jangan commit ke repository
2. **Environment Variables**: Jangan commit file `.env` yang berisi kredensial
3. **CSRF Protection**: API callback routes sudah di-exclude dari CSRF verification
4. **Signature Validation**: Semua request ke Espay menggunakan SHA256 signature

## 📝 Signature Generation

### Create/Update VA (SHA256)

```php
$raw_string = strtoupper("##{$signatureKey}##{$rq_uuid}##{$rq_datetime}##{$order_id}##{$amount}##{$ccy}##{$comm_code}##{$action}##");
$signature = hash('sha256', $raw_string);
```

### Delete VA (RSA SHA256)

```php
$stringToSign = "{$method}:{$relativeUrl}:{$hashedBody}:{$timestamp}";
openssl_sign($stringToSign, $binarySignature, $privateKey, OPENSSL_ALGO_SHA256);
$xSignature = base64_encode($binarySignature);
```

## 🐛 Troubleshooting

### Error: "Private key tidak valid"

Pastikan file `storage/app/private.pem` ada dan valid:

```bash
openssl rsa -in storage/app/private.pem -check
```

### Error: "Signature mismatch"

Periksa:
1. `ESPAY_SIGNATURE_KEY` di `.env` sudah benar
2. Format string signature sesuai dokumentasi Espay
3. Semua parameter dalam urutan yang benar

### VA tidak auto-expire

Pastikan:
1. Command `va:expire` sudah terdaftar
2. Cron job sudah disetup (production)
3. Queue worker berjalan

### Callback tidak diterima

Periksa:
1. URL callback sudah didaftarkan di Espay dashboard
2. Route `/api/v1.0/transfer-va/payment` accessible dari internet
3. Log di `storage/logs/laravel.log`

## 📚 Dokumentasi API Espay

- [Espay API Documentation](https://sandbox-api.espay.id/docs)
- Sandbox URL: `https://sandbox-api.espay.id`
- Production URL: `https://api.espay.id`

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
