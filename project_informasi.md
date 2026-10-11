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

---

### Sesi 2026-10-10 — Manajemen Pengguna, Stored Procedure, Debugging Hydration Mismatch & Refactoring UI Dashboard

#### 1. Implementasi Manajemen Pengguna (`GET /users`)
- **Backend (`UserManagementController.php`)**:
  - Mengambil data statistik pengguna melalui Stored Procedure: `EXEC dbo.sp_dash_users @bagian = 'card'`.
  - Mengambil daftar pengguna dengan filter dan pencarian: `EXEC dbo.sp_dash_users @bagian = 'list', @cari = ?, @id_role = ?`.
  - Mengambil opsi dropdown role dari view/tabel `dbo.pilihan_role`.
- **Frontend (`resources/js/pages/users/Index.vue`)**:
  - Menampilkan 4 kartu statistik pengguna (`KartuStatistikUsers.vue`).
  - Search bar dengan *debounce 400ms* dan dropdown filter role (`TabelUsers.vue`).
  - Search dan Filter Role dapat dikombinasikan bersamaan tanpa menghilangkan state.

#### 2. Debugging Report: Penyelesaian Masalah Double Render (Hydration Mismatch)
- **Gejala:** Tampilan halaman `/users` muncul 2 kali (double render), terutama saat hard refresh (`Ctrl + Shift + R`). Terdapat pesan warning di browser: `[Vue warn]: Hydration children mismatch. Server rendered element contains more child nodes than client vdom`.
- **Akar Masalah:** Pada file `resources/js/components/NavMain.vue`, struktur submenu Data Master sebelumnya dibungkus tag `<div>` yang langsung berisi `SidebarMenuItem`. Karena `SidebarMenuItem` menghasilkan tag `<li>`, ini menyebabkan struktur HTML tidak valid (`<li>` sebagai anak langsung dari `<div>`) dan memicu kegagalan hydration DOM browser.
- **Solusi Permanen:**
  - Menambahkan import `SidebarMenuSub`, `SidebarMenuSubItem`, dan `SidebarMenuSubButton` dari `@/components/ui/sidebar`.
  - Mengganti tag pembungkus `<div>` menjadi `SidebarMenuSub` dan menggunakan `SidebarMenuSubItem` + `SidebarMenuSubButton`. Struktur DOM menjadi semantik yang valid (`<ul>` dan `<li>`).
  - Menghapus folder sementara `public/build` dari dev environment agar tidak terjadi konflik cache.

#### 3. Refactoring UI Menyeluruh Dashboard Admin
- **Header Atas (`AppSidebarHeader.vue`)**: Ditambahkan search bar tipis berlatar `bg-slate-100 dark:bg-neutral-800` di sisi kiri, serta tombol aksi (Theme toggle, Settings, Notifikasi dengan dot badge merah) di sisi kanan.
- **Menu Sidebar (`NavMain.vue`)**: Styling menu aktif diubah menjadi tema lembut ramah pengguna: latar belakang `bg-blue-50 dark:bg-blue-950/40`, teks & ikon `text-blue-700 dark:text-blue-400 font-semibold`, dan sudut membulat `rounded-lg`.
- **Top Stat Cards (`KartuStatistik.vue`)**: Mengadopsi tata letak referensi modern (4 Card: Total Kasus, Selesai Dibina, Surat Panggilan dengan badge Rose `Perlu Tindakan`, dan Tingkat Ketertiban dengan badge Emerald).
- **Area Kiri (Main Chart & Table)**:
  - `GrafikKetertiban.vue`: Judul *"Tren Kasus Pelanggaran & Pembinaan"*, filter waktu (Semua, Harian, Bulanan, Tahunan), ringkasan data statistik di atas grafik, dan kurva SVG dual-line (Biru `#2563eb` & Hijau `#10b981`).
  - `TabelPelanggaranTerbaru.vue`: Judul *"Pelanggaran Terbaru"*, avatar lingkaran inisial, kelas, pelanggaran, poin, dan badge status semantik (Emerald, Amber, Rose).
- **Area Kanan (Side Lists)**:
  - `StatistikTindakLanjut.vue`: Meniru *"Active Project"* dengan progress bar tipis warna-warni berdasarkan kategori status.
  - `SiswaPelanggarTerbanyak.vue`: Meniru *"Top Customer"* menampilkan 5 siswa pelanggar terbanyak dilengkapi avatar dan badge poin rose.
- **Halaman Utama (`Dashboard.vue`)**: Dirakit menjadi tata letak 2-kolom responsif (`lg:col-span-8` & `lg:col-span-4`) dengan background `bg-slate-50 dark:bg-neutral-950`.

---

## 7. Instruksi & Aturan Ketat untuk AI Agent

1. **Struktur Submenu `NavMain.vue`:** DILARANG mengembalikan struktur submenu ke tag `<div>` yang langsung membungkus `SidebarMenuItem`. Tetap gunakan `SidebarMenuSub`, `SidebarMenuSubItem`, dan `SidebarMenuSubButton` agar terhindar dari hydration mismatch.
2. **Layout Halaman:** DILARANG menambahkan `defineOptions({ layout: { breadcrumbs: [...] } })` pada file komponen halaman Vue. Layout aplikasi sudah dikelola terpusat via `resources/js/app.ts`.
3. **Aturan Database & Query:** Seluruh query pengolahan data, filter, agregasi, dan CRUD pada prinsipnya wajib dikerjakan melalui **SQL Server View dan Stored Procedure**. Laravel Controller hanya menangani validasi, pemanggilan prosedur, dan pengiriman data ke Inertia.
4. **Keamanan Database:** DILARANG menjalankan `php artisan migrate:fresh`, drop tabel, atau menghapus data riil yang ada di database.
5. **Workflow Konfirmasi:** Selalu jelaskan rencana perubahan terlebih dahulu dan minta konfirmasi (Ya/Tidak) kepada pengguna sebelum mengeksekusi modifikasi file.
6. **Development Server:** Jalankan pengembangan menggunakan `composer run dev` atau kombinasi `php artisan serve` & `npm run dev`. Hindari menjalankan `npm run build` selama proses development lokal.

---

### Sesi 2026-10-11 — Implementasi Fitur Detail Pengguna (`GET /users/{id_users}`) & Penyempurnaan UI

#### 1. Implementasi Backend Route & Controller
- **Routing (`routes/web.php`)**:
  - Menambahkan endpoint `Route::get('users/{id_users}', [UserManagementController::class, 'show'])->name('users.show')->whereNumber('id_users');`
- **Controller (`UserManagementController.php`)**:
  - Menambahkan method `show(Request $request, int $id_users)`:
    - Otorisasi hak akses ketat Admin via `abort_unless(..., 403)`.
    - Memanggil Stored Procedure SQL Server: `EXEC dbo.sp_dash_users @bagian = 'detail', @id_users = ?`. Jika tidak ditemukan me-return HTTP 404.
    - Memanggil Stored Procedure SQL Server: `EXEC dbo.sp_dash_users @bagian = 'riwayat', @id_users = ?`.
    - Mengirim props `user` dan `riwayat` ke komponen Inertia `users/Show`.

#### 2. Aktivasi Aksi pada Tabel Pengguna
- **Frontend (`resources/js/components/Users/TabelUsers.vue`)**:
  - Mengubah aksi tombol "Detail" menjadi komponen `<Link :href="`/users/${user.id_users}`">Detail</Link>` dari `@inertiajs/vue3`.

#### 3. Pembuatan & Refactoring Halaman Detail Pengguna (`resources/js/pages/users/Show.vue`)
- Mengadopsi secara presisi layout dan styling dari gambar referensi UI:
  - **Header & Breadcrumbs**: Menggunakan icon pembatas `<ChevronRight class="h-3 w-3" />` (`Data Master > Users > Detail Pengguna`), tombol `Kembali`, dan tombol `Edit Pengguna` (disabled).
  - **Card Informasi Pengguna (Kiri 8 col)**:
    - Avatar berbentuk lingkaran bulat penuh (`rounded-full bg-blue-100 text-blue-600`) dengan inisial dua huruf kapital.
    - Badge Role (`Admin`) berbentuk *pill* latar biru transparan halus dan Badge Status Aktif dengan dot hijau.
    - Grid 8 field data diri dengan ikon presisi: Nama Pengguna, Email, Nomor HP, Jenis Kelamin, Tempat Lahir, Tanggal Lahir (`YYYY-MM-DD`), Alamat Lengkap, dan Role.
  - **Card Informasi Akun & Ringkasan Jabatan (Kanan 4 col)**:
    - Masing-masing baris dilengkapi ikon di samping kiri label (ID User, Dibuat Pada, Dibuat Oleh, Email Verified dengan badge centang hijau "Ya", Status Akun dengan dot hijau "Aktif").
    - Ringkasan Jabatan menampilkan Jabatan Terakhir, Tahun Ajaran Aktif, dan Total Riwayat Jabatan.
  - **Card Riwayat Jabatan (Kiri 8 col)**:
    - Tabel minimalis bersih tanpa strip tebal: No, Tahun Ajaran, Jabatan, Tanggal Mulai, Tanggal Selesai, dan Status.
    - Status jabatan aktif ditandai dot hijau "Aktif", sedangkan riwayat selesai ditandai dot abu-abu "Selesai".
    - Tersedia *Empty State* informatif jika pengguna belum memiliki riwayat jabatan.
  - **Card Aktivitas / Audit (Kanan 4 col)**:
    - Timeline vertical dengan titik bulat biru solid dan layout horizontal rata kanan (Akun dibuat, Terakhir diperbarui, Terakhir login).
  - Menggunakan `isCurrentOrParentUrl(child.href)` pada submenu agar menu `Data Master > Users` tetap menyala aktif saat membuka URL detail `/users/:id`.

#### 4. Implementasi Fitur Edit Pengguna, Hapus Pengguna (Soft Delete), & Activity Log
- **Routing (`routes/web.php`)**:
  - `PUT /users/{id_users}` (`users.update`): Handler update data profil & password opsional.
  - `DELETE /users/{id_users}` (`users.destroy`): Handler soft-delete pengguna.
- **Backend Controller (`UserManagementController.php`)**:
  - `show()`: Mengambil list activity via `EXEC dbo.sp_dash_users @bagian = 'activity', @id_users = ?` dan dropdown role dari `dbo.pilihan_role`.
  - `update()`: Validasi request, hashing password jika diisi, eksekusi SP `@bagian = 'update'`. SQL Server secara atomik mencatat perubahan ke `dbo.activity_log`.
  - `destroy()`: Proteksi self-delete di level controller, eksekusi SP `@bagian = 'delete'`, redirect ke `users.index` dengan toast notifikasi.
- **Proteksi Autentikasi (`EnsureUserIsActive.php` & `FortifyServiceProvider.php`)**:
  - `Fortify::authenticateUsing()` menolak login pengguna dengan `status_delete = 1 / true` (*"Akun Anda sudah dinonaktifkan"*).
  - Middleware `EnsureUserIsActive` mengecek setiap request pengguna terotentikasi dan langsung me-logout sesi jika akun telah dinonaktifkan/dihapus.
- **Frontend Komponen (`ModalEditUser.vue` & `Show.vue`)**:
  - `ModalEditUser.vue`: Form modal 2 kolom terisi data profil aktual pengguna, input password baru opsional dengan konfirmasi, error mapping field, dan state loading.
  - `Show.vue`: Mengaktifkan tombol Edit, menambahkan tombol & dialog konfirmasi Hapus (disabled jika akun sendiri), serta merender **Card Aktivitas / Audit** secara kronologis (terbaru ke terlama) lengkap dengan perbandingan diff field (`sebelum -> sesudah`) dan badge *"Password diperbarui"* tanpa mengekspos hash password.




