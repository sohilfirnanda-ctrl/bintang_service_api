# Bintang Service API

Backend REST API untuk aplikasi pemesanan servis motor **Bintang Service**, dibangun dengan **Laravel 13** dan **Laravel Sanctum**. Proyek ini melanjutkan aplikasi Flutter Bintang Service (UTS) yang sebelumnya memakai backend PHP native.

## Fitur

- Registrasi & login pelanggan berbasis token (Sanctum)
- Katalog layanan bengkel dengan pencarian & pagination
- Booking servis dengan beberapa layanan sekaligus, total harga dihitung otomatis
- Ubah jadwal, batalkan, dan lihat riwayat booking
- Fitur admin: kelola layanan, ubah status booking, data pelanggan, ringkasan bengkel
- Format response seragam `{status, message, data}` + `meta` untuk pagination
- Validasi input dengan Form Request

## Instalasi

    composer install
    copy .env.example .env
    php artisan key:generate
    php artisan migrate:fresh --seed
    php artisan serve

Atur `DB_*` di `.env` ke MySQL (database `bintang_service_api`). API berjalan di `http://127.0.0.1:8000/api`.

## Akun Seeder

| Role | Email | Password |
|---|---|---|
| Admin | admin@bintangservice.test | password123 |
| Pelanggan | rahmat@example.com | password123 |

## Struktur Database

| Tabel | Fungsi |
|---|---|
| `customers` | Akun pelanggan & admin (kolom `role`) |
| `services` | Katalog layanan dan harga |
| `bookings` | Data utama pemesanan servis |
| `booking_details` | Rincian layanan pada setiap booking |

## Daftar Endpoint (21)

| No | Method | Endpoint | Akses |
|---|---|---|---|
| 1 | POST | `/api/register` | Publik |
| 2 | POST | `/api/login` | Publik |
| 3 | GET | `/api/services` | Publik |
| 4 | GET | `/api/services/{id}` | Publik |
| 5 | GET | `/api/me` | Token |
| 6 | PUT | `/api/profile` | Token |
| 7 | POST | `/api/logout` | Token |
| 8 | GET | `/api/bookings` | Token |
| 9 | POST | `/api/bookings` | Token |
| 10 | GET | `/api/bookings/{id}` | Token |
| 11 | PUT | `/api/bookings/{id}` | Token |
| 12 | PATCH | `/api/bookings/{id}/cancel` | Token |
| 13 | GET | `/api/bookings/{id}/details` | Token |
| 14 | POST | `/api/services` | Admin |
| 15 | PUT | `/api/services/{id}` | Admin |
| 16 | DELETE | `/api/services/{id}` | Admin |
| 17 | PATCH | `/api/bookings/{id}/status` | Admin |
| 18 | GET | `/api/admin/customers` | Admin |
| 19 | GET | `/api/admin/customers/{id}` | Admin |
| 20 | GET | `/api/admin/customers/{id}/bookings` | Admin |
| 21 | GET | `/api/admin/summary` | Admin |

## Pengujian

    npx newman run docs/postman/bintang_service_api.postman_collection.json -e docs/postman/bintang_service_local.postman_environment.json

- Postman Collection v2.1 & environment: [`docs/postman/`](docs/postman/)
- Hasil pengujian: [`docs/testing_results.md`](docs/testing_results.md)