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
        Schema::create('pipeline_stages', function (Blueprint $table) {
            // المفتاح الأساسي
            $table->id();

            // -------------------------------------------------------------------
            // البيانات الأساسية للمرحلة
            // -------------------------------------------------------------------
            $table->string('name', 150); // اسم المرحلة بالعربية (مثل: تفاوض)
            $table->string('name_en', 150)->nullable(); // اسم المرحلة بالإنجليزية (مثل: Negotiation)
            $table->string('code', 50)->unique()->nullable(); // كود المرحلة
            $table->string('description', 500)->nullable(); // وصف مختصر للمرحلة

            // احتمالية الفوز (نسبة مئوية من 0 إلى 100)
            $table->decimal('probability', 5, 2)->default(0);

            // إعدادات العرض في الواجهة (مثل: Kanban Board)
            $table->integer('sort_order')->default(0); // تم توحيد المسمى لـ sort_order كبقية الجداول
            $table->string('color', 20)->nullable(); // لون المرحلة (HEX Code)

            // محددات حالة الفرصة
            $table->boolean('is_won')->default(false); // هل تعتبر مرحلة فوز؟
            $table->boolean('is_lost')->default(false); // هل تعتبر مرحلة خسارة؟

            // حالة التفعيل (Active/Inactive)
            $table->boolean('status')->default(\App\Enums\ActiveStatus::ACTIVE->value);

            // -------------------------------------------------------------------
            // بيانات التتبع (Audit Trail)
            // -------------------------------------------------------------------
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            // تواريخ الإنشاء والتحديث والحذف المنطقي
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipeline_stages');
    }
};
