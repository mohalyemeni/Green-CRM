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
        Schema::create('opportunity_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('name_en', 150)->nullable();
            $table->string('code', 50)->unique()->nullable();
            $table->string('description', 500)->nullable();
            $table->string('color', 20)->nullable();
            $table->string('icon', 100)->nullable();
            $table->boolean('status')->default(\App\Enums\ActiveStatus::ACTIVE->value);
            $table->integer('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportinity_sourcs');
    }
};
