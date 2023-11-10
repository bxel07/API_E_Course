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
            $table->string('ktp')->nullable()->after('password');
            $table->string('cv')->nullable()->after('ktp');
            $table->string('ijazah')->nullable()->after('cv');
            $table->string('avatar')->nullable()->after('ijazah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropColumns('users', ['ktp', 'cv', 'ijazah', 'avatar']);
    }
};
