<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_jabatan', function (Blueprint $table) {
            $table->id('id_riwayat_jabatan');

            $table->foreignId('id_users')
                ->constrained(table: 'users', column: 'id_users');
            $table->foreignId('id_tahun_ajaran')
                ->constrained(table: 'tahun_ajaran', column: 'id_tahun_ajaran');
            $table->string('nama_tahun_ajaran', 35);
            $table->string('nama_jabatan', 35);
            $table->string('status', 20);
            $table->date('akhir_menjabat')->nullable();
            $table->dateTime('create_at')
                ->default(DB::raw('SYSDATETIME()'));
            $table->string('create_by');
            $table->boolean('status_delete')->default(false);
            $table->dateTime('delete_at')->nullable();
            $table->string('delete_by')->nullable();
        });

        DB::statement("
            ALTER TABLE riwayat_jabatan
            ADD CONSTRAINT CK_riwayat_jabatan_status
            CHECK (status IN (
                'Masih Menjabat',
                'pindah jabatan',
                'mengundurkan diri',
                'dikeluarkan'
            ))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_jabatan');
    }
};
