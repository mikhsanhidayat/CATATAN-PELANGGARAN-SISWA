<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';

    protected $primaryKey = 'id_tahun_ajaran';

    public $timestamps = false;

    protected $fillable = [
        'nama_tahun_ajaran',
        'create_by',
    ];

    protected function casts(): array
    {
        return [
            'status_delete' => 'boolean',
        ];
    }
    public function riwayatJabatan(): HasMany
{
    return $this->hasMany(
        RiwayatJabatan::class,
        'id_tahun_ajaran',
        'id_tahun_ajaran'
    );
}
}
