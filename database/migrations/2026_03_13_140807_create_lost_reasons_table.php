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
        Schema::create('lost_reasons', function (Blueprint $table) {
            $table->id();

            // البيانات الأساسية
            $table->string('name', 150); // اسم السبب بالعربية (مثل: السعر مرتفع)
            $table->string('name_en', 150)->nullable(); // اسم السبب بالإنجليزية (مثل: High Price)
            $table->string('code', 50)->unique()->nullable(); // كود مختصر (مثل: PRICE_01)

            // الوصف
            $table->string('description', 500)->nullable();

            // الترتيب والحالة
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(\App\Enums\ActiveStatus::ACTIVE->value);

            // التتبع (Audit Trail)
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
        Schema::dropIfExists('lost_reasons');
    }
};
