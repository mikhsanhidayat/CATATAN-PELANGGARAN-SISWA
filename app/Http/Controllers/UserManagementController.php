<?php

namespace App\Http\Controllers;
use App\Models\Role;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class UserManagementController extends Controller
{
    public function index(Request $request): Response
    {
        // Memastikan hanya Admin yang dapat mengakses halaman.
        abort_unless(
            $request->user()->role?->nama_role === 'Admin',
            403
        );

        // Validasi parameter Search dan Filter Role.
        $validated = $request->validate([
            'cari' => ['nullable', 'string', 'max:150'],
            'id_role' => ['nullable', 'integer', 'min:1'],
        ]);

        // Mengambil parameter dari request.
        $cari = $validated['cari'] ?? null;
        $idRole = isset($validated['id_role'])
            ? (int) $validated['id_role']
            : null;

        // Mengambil statistik pengguna dari Stored Procedure.
        $cards = DB::connection('sqlsrv')->select(
            'EXEC dbo.sp_dash_users @bagian = ?',
            ['card']
        );

        // Mengambil daftar pengguna dengan Search dan Filter Role.
        $users = DB::connection('sqlsrv')->select(
            'EXEC dbo.sp_dash_users @bagian = ?, @cari = ?, @id_role = ?',
            ['list', $cari, $idRole]
        );

        // Mengambil daftar pilihan Role untuk dropdown.
        $roles = DB::connection('sqlsrv')->select(
            'SELECT id_role, nama_role FROM dbo.pilihan_role'
        );

        // Mengirim data ke halaman Vue menggunakan Inertia.
        return Inertia::render('users/Index', [
            'cards' => $cards[0] ?? null,
            'users' => $users,
            'roles' => $roles,
            'filters' => [
                'cari' => $cari,
                'id_role' => $idRole,
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless(
            $request->user()->role?->nama_role === 'Admin',
            403
        );

        return Inertia::render('users/Create', [
            'roles' => Role::query()
                ->where('status_delete', false)
                ->orderBy('nama_role')
                ->get(['id_role', 'nama_role']),
            'tahunAjaran' => TahunAjaran::query()
                ->where('status_delete', false)
                ->orderByDesc('nama_tahun_ajaran')
                ->get(['id_tahun_ajaran', 'nama_tahun_ajaran']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Memastikan hanya Admin yang dapat menambahkan pengguna.
        abort_unless(
            $request->user()->role?->nama_role === 'Admin',
            403
        );

        // 2. Validasi form dengan batasan panjang karakter SQL Server.
        $validated = $request->validate([
            'id_role' => ['required', 'integer', 'min:1'],
            'nama_users' => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['required', 'string', 'in:Laki-laki,Perempuan', 'max:10'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat' => ['required', 'string', 'max:500'],
            'no_hp' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'id_role.required' => 'Role wajib dipilih.',
            'id_role.min' => 'Pilihan role tidak valid.',
            'nama_users.required' => 'Nama pengguna wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // 3. Format audit trail create_by: "ID - Nama"
        $createBy = $request->user()->id_users . ' - ' . $request->user()->nama_users;

        // 4. Hash password sebelum dikirim ke Stored Procedure.
        $passwordHash = Hash::make($validated['password']);

        // 5. Eksekusi Stored Procedure dbo.sp_dash_users @bagian = 'insert'
        try {
            DB::connection('sqlsrv')->select(
                'EXEC dbo.sp_dash_users 
                    @bagian = ?, 
                    @id_role = ?, 
                    @nama_users = ?, 
                    @jenis_kelamin = ?, 
                    @tempat_lahir = ?, 
                    @tanggal_lahir = ?, 
                    @alamat = ?, 
                    @no_hp = ?, 
                    @email = ?, 
                    @password_hash = ?, 
                    @create_by = ?',
                [
                    'insert',
                    $validated['id_role'],
                    $validated['nama_users'],
                    $validated['jenis_kelamin'],
                    $validated['tempat_lahir'],
                    $validated['tanggal_lahir'],
                    $validated['alamat'],
                    $validated['no_hp'],
                    $validated['email'],
                    $passwordHash,
                    $createBy,
                ]
            );
        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();

            // Tangani error nomor HP duplikat dari SP (50005) maupun unique index users_no_hp_unique (2601/2627)
            if (
                str_contains($errorMessage, '50005') ||
                str_contains($errorMessage, 'Nomor HP sudah terdaftar') ||
                str_contains($errorMessage, 'users_no_hp_unique')
            ) {
                throw ValidationException::withMessages([
                    'no_hp' => 'Nomor HP sudah terdaftar. Silakan gunakan nomor lain.',
                ]);
            }

            // Tangani error email duplikat dari SP (50004) maupun unique index users_email_unique (2601/2627)
            if (
                str_contains($errorMessage, '50004') ||
                str_contains($errorMessage, 'Email sudah terdaftar') ||
                str_contains($errorMessage, 'users_email_unique')
            ) {
                throw ValidationException::withMessages([
                    'email' => 'Email ini sudah terdaftar dalam sistem.',
                ]);
            }

            // Tangani error role tidak aktif dari SP (50003)
            if (
                str_contains($errorMessage, '50003') ||
                str_contains($errorMessage, 'Role tidak ditemukan atau tidak aktif')
            ) {
                throw ValidationException::withMessages([
                    'id_role' => 'Role yang dipilih tidak ditemukan atau sudah tidak aktif.',
                ]);
            }

            // Tangani error data tidak lengkap dari SP (50002)
            if (
                str_contains($errorMessage, '50002') ||
                str_contains($errorMessage, 'Data pengguna belum lengkap')
            ) {
                throw ValidationException::withMessages([
                    'nama_users' => 'Data pengguna belum lengkap untuk diproses.',
                ]);
            }

            // Error tak terduga lainnya tanpa menampilkan pesan SQL mentah
            throw ValidationException::withMessages([
                'nama_users' => 'Gagal menyimpan data pengguna. Silakan periksa kembali isian form.',
            ]);
        }

        // 6. Redirect kembali ke halaman manajemen pengguna dengan flash toast notifikasi sukses.
        return redirect()->route('users.index')->with('toast', [
            'type' => 'success',
            'message' => 'Pengguna baru berhasil ditambahkan.',
        ]);
    }

    public function show(Request $request, int $id_users): Response
    {
        // 1. Otorisasi: hanya Admin yang dapat mengakses detail pengguna.
        abort_unless(
            $request->user()->role?->nama_role === 'Admin',
            403
        );

        // 2. Mengambil data detail profil dari Stored Procedure dbo.sp_dash_users @bagian = 'detail'
        $userDetail = DB::connection('sqlsrv')->select(
            'EXEC dbo.sp_dash_users @bagian = ?, @id_users = ?',
            ['detail', $id_users]
        );

        // Jika data pengguna tidak ditemukan, kembalikan response 404
        abort_if(empty($userDetail), 404);

        // 3. Mengambil data riwayat jabatan dari Stored Procedure dbo.sp_dash_users @bagian = 'riwayat'
        $userRiwayat = DB::connection('sqlsrv')->select(
            'EXEC dbo.sp_dash_users @bagian = ?, @id_users = ?',
            ['riwayat', $id_users]
        );

        // 4. Mengambil data riwayat activity UPDATE dari Stored Procedure dbo.sp_dash_users @bagian = 'activity'
        $userActivity = DB::connection('sqlsrv')->select(
            'EXEC dbo.sp_dash_users @bagian = ?, @id_users = ?',
            ['activity', $id_users]
        );

        // 5. Mengambil daftar pilihan Role untuk dropdown modal edit
        $roles = DB::connection('sqlsrv')->select(
            'SELECT id_role, nama_role FROM dbo.pilihan_role'
        );

        // 6. Render ke halaman Inertia users/Show
        return Inertia::render('users/Show', [
            'user' => $userDetail[0],
            'riwayat' => $userRiwayat,
            'activity' => $userActivity,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, int $id_users): RedirectResponse
    {
        // 1. Otorisasi: hanya Admin yang dapat mengubah data pengguna.
        abort_unless(
            $request->user()->role?->nama_role === 'Admin',
            403
        );

        // 2. Validasi form edit pengguna.
        $validated = $request->validate([
            'id_role' => ['required', 'integer', 'min:1'],
            'nama_users' => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['required', 'string', 'in:Laki-laki,Perempuan'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'alamat' => ['required', 'string', 'max:500'],
            'no_hp' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'id_role.required' => 'Role wajib dipilih.',
            'id_role.integer' => 'Role tidak valid.',
            'id_role.min' => 'Role tidak valid.',
            'nama_users.required' => 'Nama pengguna wajib diisi.',
            'nama_users.max' => 'Nama pengguna maksimal 150 karakter.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tempat_lahir.max' => 'Tempat lahir maksimal 100 karakter.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh melebihi hari ini.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.max' => 'Alamat maksimal 500 karakter.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.max' => 'Nomor HP maksimal 20 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // 3. Format audit trail update_by: "ID - Nama"
        $updateBy = $request->user()->id_users . ' - ' . $request->user()->nama_users;

        // 4. Hash password jika diisi, atau null jika tidak ingin diubah.
        $passwordHash = !empty($validated['password'])
            ? Hash::make($validated['password'])
            : null;

        // 5. Eksekusi Stored Procedure dbo.sp_dash_users @bagian = 'update'
        try {
            DB::connection('sqlsrv')->select(
                'EXEC dbo.sp_dash_users 
                    @bagian = ?, 
                    @id_role = ?, 
                    @nama_users = ?, 
                    @jenis_kelamin = ?, 
                    @tempat_lahir = ?, 
                    @tanggal_lahir = ?, 
                    @alamat = ?, 
                    @no_hp = ?, 
                    @email = ?, 
                    @password_hash = ?, 
                    @id_users = ?, 
                    @update_by = ?',
                [
                    'update',
                    (int) $validated['id_role'],
                    trim($validated['nama_users']),
                    trim($validated['jenis_kelamin']),
                    trim($validated['tempat_lahir']),
                    $validated['tanggal_lahir'],
                    trim($validated['alamat']),
                    trim($validated['no_hp']),
                    trim($validated['email']),
                    $passwordHash,
                    $id_users,
                    $updateBy,
                ]
            );
        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();

            // Tangani error email duplikat dari SP (50004) atau Unique Index
            if (
                str_contains($errorMessage, '50004') ||
                str_contains($errorMessage, 'Email sudah terdaftar') ||
                str_contains($errorMessage, 'users_email_unique')
            ) {
                throw ValidationException::withMessages([
                    'email' => 'Email sudah terdaftar untuk pengguna lain.',
                ]);
            }

            // Tangani error nomor HP duplikat dari SP (50005) atau Unique Index
            if (
                str_contains($errorMessage, '50005') ||
                str_contains($errorMessage, 'Nomor HP sudah terdaftar') ||
                str_contains($errorMessage, 'users_no_hp_unique')
            ) {
                throw ValidationException::withMessages([
                    'no_hp' => 'Nomor HP sudah terdaftar untuk pengguna lain.',
                ]);
            }

            // Tangani error role tidak aktif dari SP (50003)
            if (
                str_contains($errorMessage, '50003') ||
                str_contains($errorMessage, 'Role tidak ditemukan atau tidak aktif')
            ) {
                throw ValidationException::withMessages([
                    'id_role' => 'Role yang dipilih tidak ditemukan atau sudah tidak aktif.',
                ]);
            }

            // Tangani error user tidak ditemukan atau sudah terhapus (50007)
            if (
                str_contains($errorMessage, '50007') ||
                str_contains($errorMessage, 'Pengguna tidak ditemukan atau sudah dihapus')
            ) {
                throw ValidationException::withMessages([
                    'nama_users' => 'Pengguna tidak ditemukan atau sudah dihapus.',
                ]);
            }

            // Error tak terduga lainnya
            throw ValidationException::withMessages([
                'nama_users' => 'Gagal memperbarui data pengguna. Silakan periksa kembali isian form.',
            ]);
        }

        // 6. Redirect kembali ke halaman detail pengguna dengan flash toast notifikasi sukses.
        return redirect()->route('users.show', $id_users)->with('toast', [
            'type' => 'success',
            'message' => 'Data pengguna berhasil diperbarui.',
        ]);
    }

    public function destroy(Request $request, int $id_users): RedirectResponse
    {
        // 1. Otorisasi: hanya Admin yang dapat menghapus pengguna.
        abort_unless(
            $request->user()->role?->nama_role === 'Admin',
            403
        );

        // 2. Proteksi level aplikasi: Admin tidak boleh menghapus akun sendiri.
        if ((int) $request->user()->id_users === $id_users) {
            throw ValidationException::withMessages([
                'general' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ]);
        }

        // 3. Format audit trail delete_by: "ID - Nama"
        $deleteBy = $request->user()->id_users . ' - ' . $request->user()->nama_users;
        $actorId = (int) $request->user()->id_users;

        // 4. Eksekusi Stored Procedure dbo.sp_dash_users @bagian = 'delete'
        try {
            DB::connection('sqlsrv')->select(
                'EXEC dbo.sp_dash_users 
                    @bagian = ?, 
                    @id_users = ?, 
                    @actor_id = ?, 
                    @delete_by = ?',
                [
                    'delete',
                    $id_users,
                    $actorId,
                    $deleteBy,
                ]
            );
        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();

            // Tangani error tidak dapat menghapus akun sendiri (50008)
            if (
                str_contains($errorMessage, '50008') ||
                str_contains($errorMessage, 'Tidak dapat menghapus akun sendiri')
            ) {
                throw ValidationException::withMessages([
                    'general' => 'Tidak dapat menghapus akun yang sedang Anda gunakan.',
                ]);
            }

            // Tangani error pengguna tidak ditemukan atau sudah dihapus (50007)
            if (
                str_contains($errorMessage, '50007') ||
                str_contains($errorMessage, 'Pengguna tidak ditemukan atau sudah dihapus')
            ) {
                throw ValidationException::withMessages([
                    'general' => 'Pengguna tidak ditemukan atau sudah dihapus.',
                ]);
            }

            // Error tak terduga lainnya
            throw ValidationException::withMessages([
                'general' => 'Gagal menghapus pengguna. Silakan coba kembali.',
            ]);
        }

        // 5. Redirect kembali ke halaman manajemen pengguna (/users) dengan flash toast notifikasi sukses.
        return redirect()->route('users.index')->with('toast', [
            'type' => 'success',
            'message' => 'Pengguna berhasil dihapus.',
        ]);
    }
}
