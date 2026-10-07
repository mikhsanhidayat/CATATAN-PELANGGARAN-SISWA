# Penyesuaian Struktur Tabel `role` dan `users`

**Tanggal:** 2026-09-27
**Tahap:** Struktur tabel dan nama kolom + verifikasi build (belum ada fitur baru)
**Stack:** Laravel 13, Vue Starter Kit, Inertia.js, Fortify, SQL Server

---

## Struktur Tabel Akhir

### Tabel `role`

Dibuat oleh metode `$table->id('id_role')` di Laravel Blueprint.
Di SQL Server, Laravel menerjemahkan ini menjadi kolom BIGINT IDENTITY NOT NULL PRIMARY KEY.
Atribut UNSIGNED tidak berlaku di SQL Server (hanya MySQL).

| Kolom | Tipe Laravel Blueprint | Tipe SQL Server aktual |
|---|---|---|
| `id_role` | `id('id_role')` (auto-increment PK) | bigint IDENTITY NOT NULL PK |
| `nama_role` | `string(30)->unique()` | nvarchar(30) UNIQUE |
| `create_at` | `dateTime()->default(SYSDATETIME())` | datetime2 DEFAULT SYSDATETIME() |
| `create_by` | `string()` | nvarchar(255) NOT NULL |
| `status_delete` | `boolean()->default(false)` | bit DEFAULT 0 |
| `delete_at` | `dateTime()->nullable()` | datetime2 NULL |
| `delete_by` | `string()->nullable()` | nvarchar(255) NULL |

### Tabel `users`

| Kolom | Tipe Laravel Blueprint | Tipe SQL Server aktual |
|---|---|---|
| `id_users` | `id('id_users')` (auto-increment PK) | bigint IDENTITY NOT NULL PK |
| `id_role` | `foreignId('id_role')` (FK) | bigint NOT NULL, FK ke role.id_role |
| `nama_users` | `string(150)` | nvarchar(150) NOT NULL |
| `jenis_kelamin` | `string(10)` | nvarchar(10) NOT NULL |
| `tempat_lahir` | `string(100)` | nvarchar(100) NOT NULL |
| `tanggal_lahir` | `date()` | date NOT NULL |
| `alamat` | `string(500)` | nvarchar(500) NOT NULL |
| `no_hp` | `string(20)` | nvarchar(20) NOT NULL |
| `email` | `string()->unique()` | nvarchar(255) UNIQUE NOT NULL |
| `email_verified_at` | `timestamp()->nullable()` | datetime2 NULL |
| `password` | `string()` | nvarchar(255) NOT NULL |
| `remember_token` | `rememberToken()` | nvarchar(100) NULL |
| `two_factor_secret` | `text()->nullable()` | nvarchar(max) NULL |
| `two_factor_recovery_codes` | `text()->nullable()` | nvarchar(max) NULL |
| `two_factor_confirmed_at` | `timestamp()->nullable()` | datetime2 NULL |
| `create_at` | `dateTime()->default(SYSDATETIME())` | datetime2 DEFAULT SYSDATETIME() |
| `create_by` | `string()` | nvarchar(255) NOT NULL |
| `status_delete` | `boolean()->default(false)` | bit DEFAULT 0 |
| `delete_at` | `dateTime()->nullable()` | datetime2 NULL |
| `delete_by` | `string()->nullable()` | nvarchar(255) NULL |

Catatan penting: Model memakai `$timestamps = false` — Laravel tidak mengelola
`created_at` / `updated_at` secara otomatis. Gunakan `create_at` yang diisi manual.

---

## Status Fitur Registrasi Publik

`Features::registration()` TIDAK ada di `config/fortify.php` features array.
Artinya Fortify tidak mendaftarkan route `/register` sama sekali.

Konfirmasi dari `php artisan route:list --path=register`:

```
ERROR  Your application doesn't have any routes matching the given criteria.
```

Konsekuensi yang ditangani:
- Wayfinder tidak menghasilkan file `resources/js/routes/register/index.ts`
- `Register.vue` yang mengimpor file tersebut menyebabkan build error
- Link "Sign up" di Login.vue dan tombol "Register" di Welcome.vue mengarah ke route yang tidak ada

---

## Hasil Pemeriksaan Build

### `npm run build` — PERTAMA (sebelum perbaikan)

```
✗ Build failed in 3.67s

[UNLOADABLE_DEPENDENCY] Could not load ../../../../resources/js/routes/register
    resources/js/pages/auth/Register.vue?...lang.ts:18:23
    import { store } from "@/routes/register";
                          No such file or directory (os error 2)
```

### `npm run build` — KEDUA (setelah perbaikan)

```
✓ 3370 modules transformed.
✓ built in 3.19s
```

Tidak ada error, tidak ada warning TypeScript.

### Sintaks PHP — semua file yang diubah

```
No syntax errors detected in app/Concerns/ProfileValidationRules.php
No syntax errors detected in app/Http/Requests/Settings/ProfileUpdateRequest.php
No syntax errors detected in app/Actions/Fortify/CreateNewUser.php
No syntax errors detected in app/Http/Controllers/Settings/ProfileController.php
No syntax errors detected in database/migrations/0001_01_01_000000_create_role_table.php
No syntax errors detected in database/migrations/0001_01_01_000001_create_users_table.php
No syntax errors detected in database/migrations/2025_08_14_170933_add_two_factor_columns_to_users_table.php
No syntax errors detected in database/migrations/2024_01_01_000000_create_passkeys_table.php
No syntax errors detected in app/Models/User.php
No syntax errors detected in app/Http/Middleware/HandleInertiaRequests.php
```

---

## Verifikasi Chain Form Profil

Form profil (`settings/Profile.vue`) mengirim field `nama_users` ke server.

Alur validasi yang sudah diverifikasi cocok end-to-end:

```
Profile.vue
  name="nama_users" → dikirim via PATCH ke /settings/profile
    ↓
ProfileController@update → $request->user()->fill($request->validated())
    ↓
ProfileUpdateRequest::rules()
  → profileRules($this->user()->id_users)   ← sudah benar
    ↓
ProfileValidationRules::profileRules()
  → ['nama_users' => nameRules(), 'email' => emailRules($userId)]   ← sudah benar
    ↓
Model User → fillable includes 'nama_users'   ← sudah benar
  → kolom tabel users: nama_users nvarchar(150)   ← sudah benar
```

Rule `Rule::unique(User::class)->ignore($userId)` menggunakan `id_users`
sebagai nilai ignore — cocok dengan primary key model (`$primaryKey = 'id_users'`).

---

## Status `CreateNewUser.php`

Action ini terdaftar di `FortifyServiceProvider::configureActions()` via
`Fortify::createUsersUsing(CreateNewUser::class)`, tapi route `/register`
TIDAK TERDAFTAR karena `Features::registration()` tidak ada di features array.

Artinya: action ini tidak bisa dipanggil dari luar lewat HTTP request manapun.
File ini dibiarkan ada tapi tidak aktif dipakai.

Catatan untuk tahap berikutnya: jika nantinya admin bisa membuat akun baru
lewat panel admin, buat action atau controller baru — jangan aktifkan registrasi
publik Fortify ini.

---

## Semua File yang Diubah di Tahap Ini

### Migrasi

**`database/migrations/0001_01_01_000001_create_users_table.php`**
- Hapus `Schema::create('role', ...)` duplikat dari `up()` — mencegah error table already exists
- Hapus `Schema::dropIfExists('role')` dari `down()` — role dikelola file migrasinya sendiri

### PHP Backend

**`app/Concerns/ProfileValidationRules.php`**
- `'name'` → `'nama_users'` di `profileRules()`

**`app/Http/Requests/Settings/ProfileUpdateRequest.php`**
- `$this->user()->id` → `$this->user()->id_users`

**`app/Actions/Fortify/CreateNewUser.php`**
- `$input['name']` / `'name' =>` → `$input['nama_users']` / `'nama_users' =>`
- Tambah komentar TODO (action ini tidak aktif karena route /register tidak ada)

### TypeScript

**`resources/js/types/auth.ts`**
- `id: number` → `id_users: number`
- `name: string` → `nama_users: string`
- Hapus `created_at: string` dan `updated_at: string` (tabel tidak punya kolom ini)

### Vue Components

**`resources/js/components/UserInfo.vue`**
- `user.name` → `user.nama_users` (3 tempat: alt, inisial, teks nama)

**`resources/js/components/AppHeader.vue`**
- `auth.user.name` → `auth.user.nama_users` (2 tempat: alt avatar, inisial)

**`resources/js/pages/settings/Profile.vue`**
- Label, id, name attr, default-value, error message: `name` → `nama_users`

**`resources/js/pages/auth/Register.vue`**
- Hapus `import { store } from '@/routes/register'` — file tidak ada, penyebab build error
- Ganti `v-bind="store.form()"` dengan `action="/register" method="post"` (statik)

**`resources/js/pages/auth/Login.vue`**
- Hapus `import { register } from '@/routes'`
- Hapus blok "Don't have an account? Sign up"
- Tambah komentar: "Pendaftaran akun dinonaktifkan. Akun dibuat oleh admin."

**`resources/js/pages/Welcome.vue`**
- Hapus `import { register } from '@/routes'`
- Hapus Link "Register" dari nav
- Bersihkan struktur nav: hanya tampilkan Dashboard (user login) atau Log in (tamu)

---

## File yang Tidak Diubah dan Alasannya

| File | Alasan |
|---|---|
| `database/migrations/0001_01_01_000000_create_role_table.php` | Sudah benar |
| `app/Models/User.php` | Sudah benar sejak awal |
| `database/seeders/DatabaseSeeder.php` | Sudah benar |
| `database/factories/UserFactory.php` | Sudah benar |
| `config/fortify.php` | Tidak ada referensi kolom user |
| `app/Http/Middleware/HandleInertiaRequests.php` | `'name' => config('app.name')` adalah nama aplikasi |
| `app/Http/Controllers/Settings/SecurityController.php` | `$passkey->name` adalah nama perangkat passkey |
| `resources/js/components/PasskeyRegister.vue` | `name` adalah nama browser/device |

---

## Yang Sengaja Ditunda

- [ ] Form pembuatan akun oleh admin (bukan registrasi publik)
- [ ] `CreateNewUser.php` perlu dilengkapi kolom wajib saat form admin dibuat
- [ ] Filter `status_delete` dan soft delete behavior
- [ ] Role access dan middleware
- [ ] Halaman daftar user dan manajemen user

---

## Urutan Migrasi Final

```
0001_01_01_000000_create_role_table.php                          ← buat tabel role
0001_01_01_000001_create_cache_table.php                         ← bawaan Laravel
0001_01_01_000001_create_users_table.php                         ← buat tabel users (FK ke role)
0001_01_01_000002_create_jobs_table.php                          ← bawaan Laravel
2024_01_01_000000_create_passkeys_table.php                      ← passkeys (FK ke users.id_users)
2025_08_14_170933_add_two_factor_columns_to_users_table.php      ← kolom 2FA
```

Peringatan: Jangan jalankan `migrate:fresh` di database production.
Gunakan hanya di environment development yang bisa direset.

