<?php

use App\Enums\ActiveStatus;
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
        Schema::create('companies', function (Blueprint $table) {
            // المعرف الأساسي
            $table->id();

            // البيانات الأساسية للشركة
            $table->string('name'); // اسم الشركة
            $table->string('name_en')->nullable(); // اسم الشركة أجنبي
            $table->string('short_name', 50)->nullable(); // الاسم المختصر

            // روابط النظام
            $table->string('slug')->unique(); // الرابط المخصص للشركة (مثال: my-era-gems)

            // بيانات الاتصال والهوية
            $table->string('website')->nullable(); // الموقع الإلكتروني
            $table->string('logo')->nullable(); // مسار شعار النشاط التجاري (Logo)

            // -------------------------------------------------------------------
            // الإعدادات المحاسبية (العملة الأساسية)
            // -------------------------------------------------------------------
            // استخدمنا restrictOnDelete لمنع حذف العملة إذا كانت مرتبطة كعملة أساسية لشركة
            $table->foreignId('base_currency_id')
                ->constrained('currencies')
                ->restrictOnDelete();

            // الحالة والملاحظات
            $table->boolean('status')->default(ActiveStatus::ACTIVE->value); // الحالة
            $table->text('notes')->nullable(); // ملاحظات

            // -------------------------------------------------------------------
            // التتبع والمستخدمين (Audit Trail)
            // -------------------------------------------------------------------
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            // -------------------------------------------------------------------

            // تواريخ الإنشاء والتحديث
            $table->timestamps();

            // الحذف المنطقي (ينشئ حقل deleted_at)
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
