# Backend E-Commerce Laravel

## Tentang Project

Backend dari platform e-commerce yang dibangun menggunakan Laravel dan mengintegrasikan RajaOngkir untuk manajemen ongkos kirim dan Midtrans untuk memproses pembayaran. Backend ini digunakan untuk menyediakan API untuk frontend mengakses data. Backend ini juga menyediakan fitur untuk mengelola data e-commerce, seperti data user, data produk, data transaksi, dan data laporan. Frontend dapat ditemukan [di sini](https://github.com/Yoga-Firmansyah/e-commerce-v2.git).
### Beberapa Fitur yang tersedia:
- Autentikasi User 
- Manajemen Produk
- Laporan Pendapatan dan Transaksi
- Informasi Customer
- Manajemen User

---

## Instalasi

1. **Clone repository**  
   ```bash
   git clone https://github.com/Yoga-Firmansyah/e-commerce-v2-backend.git
   cd e-commerce-v2-backend
   ```
2. **Install dependency PHP**  
   ```bash
   composer install
   ```
3. **Konfigurasi Environment**  
   Copy file .env dari .env.example
   ```bash
   cp .env.example .env
   ```
   Konfigurasi file `.env` sesuai konfigurasi lokal kamu:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=perpustakaan
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   Generate key
   ```bash
   php artisan key:generate
   ```
   Generate JWT key
   ```bash
   php artisan jwt:key
   ```
4. **Konfigurasi RajaOngkir**  
   Konfigurasi file `.env` sesuai dengan API key yang didapatkan dari RajaOngkir:
   ```env
   RAJAONGKIR_API_KEY=YOUR_RAJAONGKIR_API_KEY
   ```
5. **Konfigurasi Midtrans**  
   Konfigurasi file `.env` sesuai dengan API key dan secret key yang didapatkan dari Midtrans:
   ```env
   MIDTRANS_CLIENTKEY=YOUR_MIDTRANS_CLIENTKEY
   MIDTRANS_SERVERKEY=YOUR_MIDTRANS_SERVERKEY
   ```
6. **Migrasi & Seeder Database**  
   Jalankan migrasi database dan seeder
   ```bash
   php artisan migrate --seed
   ```
7. **Jalankan Aplikasi**  
   ```bash
   php artisan serve
   ```

---

Secara default email dan password untuk login admin adalah:
```
Email   : admin@example.com
Password: admin123
```
Email dan password dapat diubah di file `database/seeders/UserSeeder.php`
