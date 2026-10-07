<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_users
 * @property int $id_role
 * @property string $nama_users
 * @property string $jenis_kelamin
 * @property string $tempat_lahir
 * @property Carbon $tanggal_lahir
 * @property string $alamat
 * @property string $no_hp
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $create_at
 * @property string $create_by
 * @property bool $status_delete
 * @property Carbon|null $delete_at
 * @property string|null $delete_by
 */
#[Fillable([
    'id_role',
    'nama_users',
    'jenis_kelamin',
    'tempat_lahir',
    'tanggal_lahir',
    'alamat',
    'no_hp',
    'email',
    'password',
    'create_by',
])]
#[Hidden([
    'password',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'remember_token',
])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    protected $primaryKey = 'id_users';

    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    protected function tanggalLahir(): Attribute
{
    return Attribute::make(
        get: fn (?string $value): ?string => $value === null
            ? null
            : Carbon::parse(
                preg_replace('/:(AM|PM)$/', ' $1', $value)
            )->toDateString(),
    );
}

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'create_at' => 'datetime',
            'status_delete' => 'boolean',
            'delete_at' => 'datetime',
            /* @chisel-2fa */
            'two_factor_confirmed_at' => 'datetime',
            /* @end-chisel-2fa */
        ];
    }

        public function riwayatJabatan(): HasMany
{
    return $this->hasMany(
        RiwayatJabatan::class,
        'id_users',
        'id_users'
    );
}
public function role(): BelongsTo
{
    return $this->belongsTo(Role::class, 'id_role', 'id_role');
}
}
