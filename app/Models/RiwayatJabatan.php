<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RiwayatJabatan extends Model
{
    protected $table = 'riwayat_jabatan';

    protected $primaryKey = 'id_riwayat_jabatan';

    public $timestamps = false;

    protected $fillable = [
        'id_users',
        'id_tahun_ajaran',
        'nama_tahun_ajaran',
        'nama_jabatan',
        'status',
        'akhir_menjabat',
        'create_by',
    ];

    protected function casts(): array
    {
        return [
            'status_delete' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_users', 'id_users');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(
            TahunAjaran::class,
            'id_tahun_ajaran',
            'id_tahun_ajaran'
        );
    }


}
