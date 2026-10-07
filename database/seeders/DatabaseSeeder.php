<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = [
            'nama_users' => $this->command->ask('Nama admin'),
            'jenis_kelamin' => $this->command->ask(
                'Jenis kelamin (Laki-laki/Perempuan)'
            ),
            'tempat_lahir' => $this->command->ask('Tempat lahir'),
            'tanggal_lahir' => $this->command->ask(
                'Tanggal lahir (YYYY-MM-DD)'
            ),
            'alamat' => $this->command->ask('Alamat'),
            'no_hp' => $this->command->ask('Nomor HP'),
            'email' => $this->command->ask('Email admin'),
            'password' => $this->command->secret(
                'Password admin (minimal 8 karakter)'
            ),
        ];

        Validator::make($admin, [
            'nama_users' => ['required', 'string', 'max:150'],
            'jenis_kelamin' => [
                'required',
                Rule::in(['Laki-laki', 'Perempuan']),
            ],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date_format:Y-m-d'],
            'alamat' => ['required', 'string', 'max:500'],
            'no_hp' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ])->validate();

        DB::transaction(function () use ($admin): void {
            foreach (['Admin', 'Guru', 'Petugas'] as $namaRole) {
                DB::table('role')->insert([
                    'nama_role' => $namaRole,
                    'create_by' => 'SYSTEM',
                ]);
            }

            $idRoleAdmin = DB::table('role')
                ->where('nama_role', 'Admin')
                ->value('id_role');

            $user = new User([
                'id_role' => $idRoleAdmin,
                'nama_users' => $admin['nama_users'],
                'jenis_kelamin' => $admin['jenis_kelamin'],
                'tempat_lahir' => $admin['tempat_lahir'],
                'tanggal_lahir' => $admin['tanggal_lahir'],
                'alamat' => $admin['alamat'],
                'no_hp' => $admin['no_hp'],
                'email' => $admin['email'],
                'password' => $admin['password'],
                'create_by' => 'SYSTEM',
            ]);

            $user->email_verified_at = now();
            $user->save();
        });

        $this->command->info('Role dan akun admin pertama berhasil dibuat.');
    }
}
