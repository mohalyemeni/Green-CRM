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
        Schema::create('countries', function (Blueprint $table) {
            // المعرف الأساسي
            $table->id();

            // -------------------------------------------------------------------
            // أسماء الدولة
            // -------------------------------------------------------------------
            $table->string('name', 150); // الاسم بالعربي (مثال: اليمن)
            $table->string('name_en', 150)->nullable(); // الاسم بالإنجليزية (مثال: Yemen)

            // -------------------------------------------------------------------
            // الأكواد الدولية
            // -------------------------------------------------------------------
            $table->char('country_code', 2)->unique()->nullable(); // كود الدولة (مثال: YE)
            $table->string('phone_code', 20)->nullable(); // مفتاح الاتصال (مثال: +967)

            // -------------------------------------------------------------------
            // الجنسيات (تم توحيد النمط مع الأسماء)
            // -------------------------------------------------------------------
            $table->string('nationality', 150)->nullable(); // الجنسية بالعربي (مثال: يمني)
            $table->string('nationality_en', 150)->nullable(); // الجنسية بالإنجليزية (مثال: Yemeni)

            // حالة التفعيل (مفعل كافتراضي)
            $table->boolean('status')->default(\App\Enums\ActiveStatus::ACTIVE->value);

            // -------------------------------------------------------------------
            // بيانات التتبع (Audit Trail)
            // -------------------------------------------------------------------
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            // تواريخ الإنشاء والتحديث
            $table->timestamps();

            // الحذف المنطقي
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
