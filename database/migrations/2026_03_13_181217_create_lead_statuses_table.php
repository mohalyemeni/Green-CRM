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
        Schema::create('lead_statuses', function (Blueprint $table) {
            // المفتاح الأساسي
            $table->id();

            // -------------------------------------------------------------------
            // البيانات الأساسية
            // -------------------------------------------------------------------
            $table->string('name', 150); // اسم الحالة بالعربية (مثل: قيد المتابعة)
            $table->string('name_en', 150)->nullable(); // اسم الحالة بالإنجليزية (مثل: Following Up)
            $table->string('code', 50)->unique()->nullable(); // كود الحالة (مثل: FOLLOW_UP)
            $table->string('description', 500)->nullable(); // وصف مختصر للحالة
            $table->string('color', 20)->nullable(); // لون الحالة في الواجهة (HEX Code)

            // -------------------------------------------------------------------
            // إعدادات النظام للحالة
            // -------------------------------------------------------------------
            $table->boolean('is_default')->default(false); // الحالة الافتراضية عند الإضافة
            $table->boolean('is_closed')->default(false); // هل تعني إغلاق ملف العميل؟

            // حالة التفعيل والترتيب
            $table->boolean('status')->default(\App\Enums\ActiveStatus::ACTIVE->value);
            $table->integer('sort_order')->default(0);

            // -------------------------------------------------------------------
            // التتبع والمستخدمين (Audit Trail)
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
        Schema::dropIfExists('lead_statuses');
    }
};
