<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {


        Schema::create('users', function (Blueprint $table) {
            $table->id('id_users');

            $table->foreignId('id_role')
    ->constrained(table: 'role', column: 'id_role');

            $table->string('nama_users', 150);
            $table->string('jenis_kelamin', 10);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->string('alamat', 500);
            $table->string('no_hp', 20);

            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            $table->dateTime('create_at')->default(DB::raw('SYSDATETIME()'));
            $table->string('create_by');
            $table->boolean('status_delete')->default(false);
            $table->dateTime('delete_at')->nullable();
            $table->string('delete_by')->nullable();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');

    }
};
