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
        Schema::create('industries', function (Blueprint $table) {
            // المعرف الأساسي
            $table->id();

            // -------------------------------------------------------------------
            // البيانات الأساسية للقطاع / النشاط
            // -------------------------------------------------------------------
            $table->string('name', 150); // اسم القطاع بالعربية (مثل: الرعاية الصحية)
            $table->string('name_en', 150)->nullable(); // اسم القطاع بالاجنبي (مثل: Healthcare)

            // وصف القطاع (تم تغييره إلى string 500 لتحسين الأداء)
            $table->string('description', 500)->nullable();

            // أيقونة القطاع (لتخزين كلاس الأيقونة مثل ri-heart-pulse-line أو مسار صورة)
            $table->string('icon', 100)->nullable();

            // -------------------------------------------------------------------
            // خيارات العرض والحالة
            // -------------------------------------------------------------------
            $table->boolean('status')->default(\App\Enums\ActiveStatus::ACTIVE->value); // حالة التفعيل
            $table->integer('sort_order')->default(0); // ترتيب العرض في القوائم

            // -------------------------------------------------------------------
            // بيانات التتبع والتدقيق (Audit Trail)
            // -------------------------------------------------------------------
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            // تواريخ الإنشاء والتعديل والحذف المنطقي
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industries');
    }
};
