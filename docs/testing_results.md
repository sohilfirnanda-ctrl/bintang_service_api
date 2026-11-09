# Testing Results – Bintang Service API

Hasil pengujian Backend API **Bintang Service** (aplikasi pemesanan servis motor) menggunakan Postman Collection v2.1 yang dijalankan dengan **Newman** (Postman collection runner versi command line).

## Lingkungan Pengujian

| Item | Keterangan |
|---|---|
| Framework | Laravel 13.40 + Laravel Sanctum 4.3 (token) |
| PHP | 8.4.26 |
| Database | MySQL (Laragon) – `bintang_service_api` |
| Base URL | `http://127.0.0.1:8000/api` |
| Collection | `docs/postman/bintang_service_api.postman_collection.json` |
| Environment | `docs/postman/bintang_service_local.postman_environment.json` |
| Runner | Newman 6.2.3 |
| Data awal | `php artisan migrate:fresh --seed` |
| Tanggal uji | 9 November 2026 |
| Penguji | Zacky Sohil Firnanda |

Perintah yang dijalankan:

    php artisan migrate:fresh --seed
    npx newman run docs/postman/bintang_service_api.postman_collection.json -e docs/postman/bintang_service_local.postman_environment.json

Akun seeder (password `password123`): admin `admin@bintangservice.test`, pelanggan `rahmat@example.com`.

## Format Response Standar

Semua endpoint mengembalikan format yang sama:

    {
      "status": "success",
      "message": "Daftar layanan berhasil diambil.",
      "data": [ ... ],
      "meta": { "current_page": 1, "per_page": 5, "total": 6, "last_page": 2 }
    }

`meta` hanya muncul pada endpoint dengan pagination. Untuk error, `status` bernilai `"error"`, `data` bernilai `null`, dan error validasi (422) menyertakan `errors` per field.

## Ringkasan 10 Test Wajib

| No | Skenario | Request | Expected | Actual | Hasil |
|---|---|---|---|---|---|
| T01 | Register pelanggan baru | `POST /register` | 201 + token | 201 Created, token ada | ✅ Lulus |
| T02 | Login pelanggan | `POST /login` | 200 + token | 200 OK, token ada | ✅ Lulus |
| T03 | Endpoint terproteksi dengan token | `GET /me` | 200 | 200 OK | ✅ Lulus |
| T04 | Buat booking data valid | `POST /bookings` | 201 | 201 Created, total harga sesuai | ✅ Lulus |
| T05 | Buat booking data tidak valid | `POST /bookings` | 422 + detail errors | 422, errors: motor_type, booking_date, booking_time, service_ids | ✅ Lulus |
| T06 | Endpoint terproteksi tanpa token | `GET /bookings` | 401 | 401 Unauthorized | ✅ Lulus |
| T07 | Resource tidak ditemukan | `GET /services/999` | 404 | 404 Not Found | ✅ Lulus |
| T08 | Daftar layanan + pagination | `GET /services?per_page=5` | 200 + meta | 200 OK, meta lengkap | ✅ Lulus |
| T09 | Pelanggan akses endpoint admin | `GET /admin/summary` | 403 | 403 Forbidden | ✅ Lulus |
| T10 | Admin ubah status booking | `PATCH /bookings/4/status` | 200 | 200 OK, status `confirmed` | ✅ Lulus |

Selain 10 test di atas, setiap response pada endpoint akun dan pelanggan diperiksa agar **tidak memuat `password` maupun `remember_token`** (semua lulus).

## Hasil Keseluruhan Collection

| | Dijalankan | Gagal |
|---|---|---|
| Request | 29 | 0 |
| Test script | 29 | 0 |
| Assertion | 66 | 0 |

Seluruh 21 endpoint terpanggil minimal satu kali dalam collection.

## Screenshot

### 1. Auth & Services (T01, T02, T03, T07, T08)
![Auth dan Services](screenshots/newman-1-auth-services.png)

### 2. Bookings (T04, T05, T06)
![Bookings](screenshots/newman-2-bookings.png)

### 3. Admin, Logout & Ringkasan (T09, T10)
![Admin dan ringkasan](screenshots/newman-3-admin-summary.png)

## Kesimpulan

Seluruh skenario wajib (201, 200, 401, 404, 422) beserta skenario tambahan (403, pagination, dan pemeriksaan data sensitif) lulus pengujian. Total 66 assertion pada 29 request berjalan tanpa kegagalan.