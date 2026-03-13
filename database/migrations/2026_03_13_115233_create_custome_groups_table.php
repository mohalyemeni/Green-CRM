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
        Schema::create('customer_groups', function (Blueprint $table) {
            $table->id();

            // البيانات الأساسية
            $table->string('name', 150); // اسم المجموعة بالعربية
            $table->string('name_en', 150)->nullable(); // إضافة: اسم المجموعة بالإنجليزية للتناسق
            $table->string('code', 50)->unique()->nullable(); // كود المجموعة

            // الوصف: تم تغييره إلى string بحجم 500 كأفضل ممارسة لتسريع الأداء بدلاً من text
            $table->string('description', 500)->nullable();

            // خيارات العرض والحالة
            $table->boolean('status')->default(\App\Enums\ActiveStatus::ACTIVE->value);

            // -------------------------------------------------------------------
            // بيانات التتبع والتدقيق (Audit Trail)
            // -------------------------------------------------------------------
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
        Schema::dropIfExists('customer_groups');
    }
};
