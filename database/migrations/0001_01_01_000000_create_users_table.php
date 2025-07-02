<?php

use App\Enums\UserGender;
use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->required();
            $table->string('lastname')->required();
            $table->string('email')->unique()->required();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->required();
            $table->string('password')->required();
            $table->string('address_line_1')->required();
            $table->string('address_line_2')->nullable();
            $table->string('city')->required();
            $table->string('zip_code')->required();
            $table->enum('role', [
                UserRole::USER->value,
                UserRole::ADMIN->value,
            ])->default(UserRole::USER->value);
            $table->enum('gender', [
                UserGender::MALE->value,
                UserGender::FEMALE->value,
                UserGender::OTHER->value,
            ]);
            $table->rememberToken();
            $table->timestamps();
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

        Schema::table('users', function (Blueprint $table) {
            $table->string('address_line_2')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');

        Schema::table('users', function (Blueprint $table) {
            $table->string('address_line_2')->nullable(false)->change();
        });
    }
};
