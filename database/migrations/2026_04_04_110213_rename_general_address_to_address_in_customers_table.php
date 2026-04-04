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
        // Schema::table('customers', function (Blueprint $table) {
        //     \Illuminate\Support\Facades\DB::statement("ALTER TABLE `customers` CHANGE `general_address` `address` text COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'العنوان العام'");
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //     Schema::table('customers', function (Blueprint $table) {
        //         \Illuminate\Support\Facades\DB::statement("ALTER TABLE `customers` CHANGE `address` `general_address` text COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'العنوان العام'");
        //     });
    }
};
