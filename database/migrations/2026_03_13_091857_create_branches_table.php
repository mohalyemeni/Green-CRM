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
        Schema::create('branches', function (Blueprint $table) {
            // 1. المعرف الأساسي
            $table->id();

            // 2. العلاقة مع الشركة (مهم جداً: منع حذف الشركة إذا كان لديها فروع)
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();

            // 3. البيانات الأساسية للفرع
            // كود الفرع غالباً يكون قصيراً، جعلناه 50 حرفاً وفريداً
            $table->string('code', 50)->unique()->nullable();
            $table->string('name'); // اسم الفرع (بالعربية)
            $table->string('name_en')->nullable(); // (إضافة مقترحة): اسم الفرع بالإنجليزية لتوحيد النظام
            $table->string('slug')->unique(); // رابط فريد للفرع

            // 4. البيانات القانونية والضريبية
            $table->string('commercial_register', 100)->nullable(); // السجل التجاري
            $table->string('tax_number', 100)->nullable(); // الرقم الضريبي

            // 5. بيانات العنوان والموقع
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('state', 150)->nullable(); // الولاية أو المحافظة
            $table->string('city', 150)->nullable(); // المدينة
            $table->string('district', 150)->nullable(); // الحي
            $table->string('building_number', 50)->nullable(); // رقم المبنى
            $table->string('street_address')->nullable(); // العنوان التفصيلي
            $table->string('postal_code', 20)->nullable(); // الرمز البريدي
            $table->string('po_box', 50)->nullable(); // صندوق البريد

            // 6. الإعدادات الخاصة بالفرع
            // المنطقة الزمنية مهمة جداً لضبط تواريخ الفواتير لكل فرع حسب دولته
            $table->string('timezone', 50)->default('Asia/Riyadh');
            // العملة الافتراضية للفرع (قد تختلف عن الشركة الأم إذا كان الفرع في دولة أخرى)
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->restrictOnDelete();

            // 7. بيانات التواصل
            $table->string('phone', 50)->nullable(); // الهاتف الأرضي
            $table->string('mobile', 50)->nullable(); // الجوال
            $table->string('email')->nullable(); // البريد الإلكتروني
            $table->string('fax', 50)->nullable(); // الفاكس

            // 8. الشعار والحالة
            $table->string('logo')->nullable(); // مسار شعار الفرع
            $table->boolean('status')->default(\App\Enums\ActiveStatus::ACTIVE->value); // حالة الفرع

            // 9. بيانات التتبع (Audit Trail)
            // (ملاحظة: أضفت created_by و updated_by بجانب deleted_by لتوحيد النظام)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            // 10. التواريخ والحذف المنطقي
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
