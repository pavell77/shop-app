<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Спочатку створюємо колонку, якщо її ще немає
            // unsignedBigInteger, бо roles.id зазвичай такий самий
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id')->default(2)->after('id'); 
            }

            // 2. Тепер додаємо зовнішній ключ
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('users', function ($table) {
            $table->dropForeign(['role_id']);
        });
    }
};