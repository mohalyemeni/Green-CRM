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
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); // المفتاح الأساسي

            // البيانات الأساسية
            $table->string('customer_number')->unique()->index()->comment('رقم العميل');
            $table->string('name')->required()->index()->comment('اسم العميل');
            $table->tinyInteger('gender')->unsigned()->nullable()->comment('الجنس: 1=ذكر، 2=أنثى');

            // بيانات التواصل
            $table->string('phone')->nullable()->comment('رقم الهاتف');
            $table->string('mobile')->required()->index()->comment('رقم الموبايل');
            $table->string('whatsapp')->nullable()->comment('رقم الوتس');
            $table->string('email')->unique()->nullable()->comment('البريد الإلكتروني');

            // بيانات العنوان
            $table->text('general_address')->nullable()->comment('العنوان العام');
            $table->string('building_number')->nullable()->comment('رقم المبنى');
            $table->string('street_name')->nullable()->comment('اسم الشارع');
            $table->string('district')->nullable()->comment('الحي');
            $table->string('city')->nullable()->comment('المدينة');
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete()->comment('الدولة');

            // الحالة والملاحظات
            $table->tinyInteger('status')->default(1)->comment('1: Active, 2: Inactive, 3: Suspended حالة التفعيل');
            $table->text('notes')->nullable()->comment('ملاحظات');

            // التتبع والتدقيق (Audit Trails)
            // نربطها بجدول المستخدمين (users) لمعرفة من قام بالعملية
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('أنشئ بواسطة');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->comment('عدل بواسطة');

            // التواريخ والحذف الآمن
            $table->timestamps(); // تنشئ تلقائياً: created_at (أنشئ في) و updated_at (عدل في)
            $table->softDeletes(); // تنشئ تلقائياً: deleted_at (تاريخ الحذف - السجل محذوف)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
