# Informasi & Dokumentasi Konteks Proyek

**Nama Aplikasi:** Sistem Pencatatan Pelanggaran Siswa  
**Tech Stack:** Laravel 13, Vue 3 (Inertia.js), Fortify (Authentication), SQL Server (Database)  
**Tanggal Update Konteks:** 2026-09-27  

---

## 1. Ringkasan & Arsitektur Basis Data

Aplikasi ini menggunakan basis data SQL Server dengan skema custom yang telah disesuaikan:

### a. Tabel `role`
- **Primary Key:** `id_role` (BigInteger Auto-increment)
- **Kolom Utama:** `nama_role` (Unique, max 30)
- **Kolom Audit:** `create_at` (default `SYSDATETIME()`), `create_by`, `status_delete` (default `false`), `delete_at`, `delete_by`
- **Data Awal (Seeder):** `'Admin'`, `'Guru'`, `'Petugas'`

### b. Tabel `users`
- **Primary Key:** `id_users` (BigInteger Auto-increment)
- **Foreign Key:** `id_role` (mengarah ke `role.id_role`, constraint `restrictOnDelete`)
- **Data Diri & Akun:** `nama_users` (max 150), `jenis_kelamin` (max 10), `tempat_lahir` (max 100), `tanggal_lahir` (date), `alamat` (max 500), `no_hp` (max 20), `email` (unique), `password`
- **Autentikasi Fortify:** `email_verified_at`, `remember_token`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`
- **Kolom Audit:** `create_at` (default `SYSDATETIME()`), `create_by`, `status_delete` (default `false`), `delete_at`, `delete_by`
- **Model:** `App\Models\User` (`$primaryKey = 'id_users'`, `$timestamps = false`)

### c. Tabel `tahun_ajaran`
- **Primary Key:** `id_tahun_ajaran` (BigInteger Auto-increment)
- **Kolom Utama:** `nama_tahun_ajaran` (Unique, max 9, contoh: `'2026-2027'`)
- **Kolom Audit:** `create_at`, `create_by`, `status_delete`, `delete_at`, `delete_by`
- **Model:** `App\Models\TahunAjaran` (`$primaryKey = 'id_tahun_ajaran'`, `$timestamps = false`)

### d. Tabel `riwayat_jabatan`
- **Primary Key:** `id_riwayat_jabatan` (BigInteger Auto-increment)
- **Foreign Key:** `id_users` (ke `users.id_users`), `id_tahun_ajaran` (ke `tahun_ajaran.id_tahun_ajaran`)
- **Kolom Utama:** `nama_tahun_ajaran` (max 35), `nama_jabatan` (max 35), `status` (Check Constraint: `'Masih Menjabat'`, `'pindah jabatan'`, `'mengundurkan diri'`, `'dikeluarkan'`), `akhir_menjabat` (date nullable)
- **Kolom Audit:** `create_at`, `create_by`, `status_delete`, `delete_at`, `delete_by`
- **Model:** `App\Models\RiwayatJabatan` (`$primaryKey = 'id_riwayat_jabatan'`, `$timestamps = false`)

### e. View SQL Server `dbo.data_users`
View di SQL Server menghubungkan data `users`, `role`, serta jabatan & tahun ajaran terbaru dari `riwayat_jabatan`.
- Menggunakan `LEFT JOIN` agar user yang belum memiliki riwayat jabatan tetap muncul dengan `nama_jabatan = NULL` (di UI tampil *"Belum ada"*).
- Memfilter/mengecualikan user dengan `status_delete = 1`.

---

## 2. Alur Kerja & Data Flow `GET /users` (`users.index`)

Alur pemrosesan data saat halaman `/users` dibuka:

```
[SQL Server View: dbo.data_users]
          │ (Menghubungkan users + role + riwayat_jabatan)
          ▼
[Laravel Controller: UserManagementController@index]
          │ (Otorisasi role Admin -> DB::table('dbo.data_users')->get(...))
          ▼
[Inertia.js Bridge]
          │ (Mengirim data sebagai Prop JSON 'users')
          ▼
[Vue Page Component: resources/js/pages/users/Index.vue]
          │ (Menerima props 'users' & merender tabel HTML di Browser)
```

### Rincian Berjalan:
1. **Route (`routes/web.php`):**
   `Route::get('users', [UserManagementController::class, 'index'])->name('users.index');`
   Dilingkupi middleware `auth` dan `verified`.
2. **Controller (`app/Http/Controllers/UserManagementController.php`):**
   - Memeriksa apakah user yang sedang login ber-role `'Admin'` via `abort_unless(..., 403)`.
   - Mengambil kolom `id_users`, `nama_users`, `nama_role`, `nama_jabatan`, `nama_tahun_ajaran`, `email`, `no_hp` dari `dbo.data_users`.
   - Merender view Inertia `'users/Index'` dengan prop `users`.
3. **Tampilan Vue (`resources/js/pages/users/Index.vue`):**
   - Memiliki tipe `UserRow` TypeScript yang sesuai.
   - Merender daftar pengguna dalam tabel. Jika `nama_jabatan` bernilai `null`, menampilkan string `"Belum ada"`.

---

## 3. Status Komponen Aplikasi Saat Ini

| Komponen / Route | File Terkait | Status Kesiapan |
|---|---|---|
| **Daftar User (`GET /users`)** | `UserManagementController@index`, `resources/js/pages/users/Index.vue` | **Berjalan Aktif** (Menampilkan user Admin dari view DB) |
| **Form Tambah User (`GET /users/create`)** | `UserManagementController@create` | **Controller Siap, Frontend Vue Belum Ada** (`resources/js/pages/users/Create.vue` belum dibuat) |
| **Registrasi Publik** | Fortify (`config/fortify.php`) | **Nonaktif** (Fitur registrasi dinonaktifkan; pembuatan user akan dilakukan oleh Admin) |
| **Seeder Akun Admin** | `database/seeders/DatabaseSeeder.php` | **Berjalan** (Membuat role `Admin`, `Guru`, `Petugas` & akun Admin pertama) |
| **Stored Procedure / Insert Logic** | SQL Server / Controller | **Belum Dibuat** (Menunggu instruksi pembuatan prosedur penulisan data) |

---

## 4. Daftar File Penting & Fungsinya

1. `routes/web.php`: Mendaftarkan endpoint URL aplikasi (`/users` dan `/users/create`) di bawah proteksi autentikasi.
2. `app/Http/Controllers/UserManagementController.php`: Mengelola logika pemanggilan view SQL Server (`dbo.data_users`), otorisasi hak akses Admin, dan persiapan data `roles` & `tahunAjaran` untuk form tambah.
3. `resources/js/pages/users/Index.vue`: Komponen Vue untuk menampilkan tabel data pengguna secara responsif dengan Inertia.js.
4. `app/Models/User.php`: Model Eloquent untuk tabel `users` dengan konfigurasi primary key `id_users` dan casting atribut `tanggal_lahir`.
5. `app/Models/Role.php`: Model Eloquent untuk tabel `role`.
6. `app/Models/TahunAjaran.php`: Model Eloquent untuk tabel `tahun_ajaran`.
7. `app/Models/RiwayatJabatan.php`: Model Eloquent untuk tabel `riwayat_jabatan` lengkap dengan Check Constraint status jabatannya.
8. `database/seeders/DatabaseSeeder.php`: Seeder interaktif untuk inisialisasi data awal master role dan akun administrator.

---

## 5. Panduan & Batasan Langkah Selanjutnya

- **Tanpa `migrate:fresh`:** Dilarang menjalankan `php artisan migrate:fresh` atau mengubah data secara destruktif tanpa konfirmasi.
- **Kerja Bertahap:** Setiap perubahan kode wajib didahului penjelasan fungsi file dan alurnya.
- **Penulisan Data:** Logika simpan data (`POST`) akan dirancang menggunakan Stored Procedure SQL Server sesuai petunjuk tahap berikutnya.
- **Fitur Baru:** Dilarang menambah fitur seperti soft-delete filter, pencarian/filter kompleks, atau aksi edit/delete sebelum diminta.

---

## 6. Riwayat Perubahan

### Sesi 2026-09-27 — Penyesuaian Struktur Tabel & Kolom
| File | Perubahan |
|---|---|
| `database/migrations/0001_01_01_000001_create_users_table.php` | Hapus duplikasi `Schema::create('role')` dan `dropIfExists('role')` |
| `app/Concerns/ProfileValidationRules.php` | Kunci validasi `'name'` → `'nama_users'` |
| `app/Http/Requests/Settings/ProfileUpdateRequest.php` | `->id` → `->id_users` |
| `app/Actions/Fortify/CreateNewUser.php` | `$input['name']` → `$input['nama_users']` + komentar TODO |
| `resources/js/types/auth.ts` | `id` → `id_users`, `name` → `nama_users`, hapus `created_at`/`updated_at` |
| `resources/js/components/UserInfo.vue` | `user.name` → `user.nama_users` (3 tempat) |
| `resources/js/components/AppHeader.vue` | `auth.user.name` → `auth.user.nama_users` (2 tempat) |
| `resources/js/pages/settings/Profile.vue` | Label, id, name, default-value, error key → `nama_users` |
| `resources/js/pages/auth/Register.vue` | Hapus import `@/routes/register` (file tidak ada); ganti `store.form()` dengan action statis |
| `resources/js/pages/auth/Login.vue` | Hapus import + link "Sign up" (route nonaktif) |
| `resources/js/pages/Welcome.vue` | Hapus import + tombol Register; bersihkan nav |

### Sesi 2026-09-28 — Desain Login DisiplinKu & Dashboard Dummy

#### Perubahan Tampilan Login
| File | Perubahan | Alasan |
|---|---|---|
| `resources/js/layouts/auth/AuthSimpleLayout.vue` | Tambah prop `wide?: boolean` (default `false`). Jika `wide=true` → render slot langsung tanpa pembungkus `max-w-sm` | Agar Login.vue bisa memakai desain halaman penuh dua kolom |
| `resources/js/layouts/AuthLayout.vue` | Terima dan teruskan prop `wide` ke `AuthSimpleLayout` | Perantara layout agar prop sampai ke AuthSimpleLayout |
| `resources/js/pages/auth/Login.vue` | Redesain total mengikuti tema DisiplinKu: form kiri + panel ilustrasi biru kanan. Memakai `layout: { wide: true }`. Form tetap pakai `store.form()` Fortify, error dari server, passkey, lupa password | Tampilan login lebih profesional sesuai tema aplikasi |

**Catatan penting Login.vue:**
- Autentikasi tetap melalui Fortify (tidak ada login palsu/demo)
- Halaman autentikasi lain (ForgotPassword, ResetPassword, VerifyEmail, dll.) **tidak terpengaruh** — tetap pakai layout sempit bawaan
- Angka pada panel ilustrasi biru adalah **contoh tampilan statis**, bukan data SQL Server
- Registrasi publik tetap nonaktif

#### Perubahan Dashboard
| File | Perubahan | Alasan |
|---|---|---|
| `resources/js/pages/Dashboard.vue` | Ganti `PlaceholderPattern` dengan tampilan dummy DisiplinKu: 3 kartu statistik, grafik garis ketertiban SVG, dan tabel pelanggaran | Memperlihatkan gambaran tampilan dashboard sebelum data asli SQL Server tersedia |

**Catatan Dashboard.vue:**
- Semua angka (142, 138, 4, 98,2%) dan data tabel adalah **dummy statis** — tidak membaca database
- Data dummy ada di `const dummyRows` di dalam `<script setup>` — mudah diganti dengan props Inertia saat fitur data asli siap
- Label badge "Data contoh" ditambahkan agar jelas bahwa ini bukan data real

#### Hasil Build
```
✓ 3368 modules transformed.
✓ built in 1.78s   — 0 error, 0 warning
```

