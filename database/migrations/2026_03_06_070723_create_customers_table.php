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
            $table->string('customer_number')->unique()->comment('رقم العميل');
            $table->string('name')->comment('اسم العميل');
            $table->string('national_id')->unique()->nullable()->comment('الهوية الوطنية');
            $table->tinyInteger('age')->unsigned()->nullable()->comment('العمر');
            $table->tinyInteger('gender')->unsigned()->nullable()->comment('الجنس: 1=ذكر، 2=أنثى');

            // بيانات العنوان
            $table->text('general_address')->nullable()->comment('العنوان العام');
            $table->string('building_number')->nullable()->comment('رقم المبنى');
            $table->string('street_name')->nullable()->comment('اسم الشارع');
            $table->string('district')->nullable()->comment('الحي');
            $table->string('city')->nullable()->comment('المدينة');
            $table->string('country')->nullable()->comment('الدولة');

            // بيانات التواصل
            $table->string('mobile')->nullable()->comment('رقم الموبايل');
            $table->string('email')->unique()->nullable()->comment('البريد الإلكتروني');

            // البيانات المالية والتجارية
            $table->string('tax_number')->nullable()->comment('الرقم الضريبي');
            $table->string('dealing_method')->nullable()->comment('طريقة التعامل (كاش/آجل)');
            $table->decimal('credit_limit', 15, 2)->default(0)->comment('حد الدين');

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
