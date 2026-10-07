<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_role' => DB::table('role')
                ->where('nama_role', 'Guru')
                ->value('id_role'),

            'nama_users' => fake()->name(),
            'jenis_kelamin' => fake()->randomElement([
                'Laki-laki',
                'Perempuan',
            ]),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->date('Y-m-d', '-18 years'),
            'alamat' => fake()->address(),
            'no_hp' => fake()->numerify('08##########'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'create_by' => 'SYSTEM',

            /* @chisel-2fa */
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            /* @end-chisel-2fa */
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model has two-factor authentication configured.
     */
    public function withTwoFactor(): static
    {
        /* @chisel-2fa */
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => encrypt('secret'),
            'two_factor_recovery_codes' => encrypt(json_encode([
                'recovery-code-1',
            ])),
            'two_factor_confirmed_at' => now(),
        ]);
        /* @end-chisel-2fa */
    }
}
