<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'role';

    protected $primaryKey = 'id_role';

    public $timestamps = false;

    protected $fillable = [
        'nama_role',
        'create_by',
    ];

    protected function casts(): array
    {
        return [
            'status_delete' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_role', 'id_role');
    }
}
