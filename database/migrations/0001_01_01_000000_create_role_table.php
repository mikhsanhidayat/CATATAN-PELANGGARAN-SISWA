<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role', function (Blueprint $table) {
            $table->id('id_role');
            $table->string('nama_role', 30)->unique();

            $table->dateTime('create_at')->default(DB::raw('SYSDATETIME()'));
            $table->string('create_by');
            $table->boolean('status_delete')->default(false);
            $table->dateTime('delete_at')->nullable();
            $table->string('delete_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role');
    }
};
