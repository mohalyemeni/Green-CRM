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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_group_id')
                ->constrained('service_groups')
                ->cascadeOnDelete()
                ->comment('رابط مجموعة الخدمة');

            $table->string('code')->index()->comment('كود الخدمة');
            $table->string('name')->index()->comment('اسم الخدمة');
            $table->string('slug')->unique()->comment('الـ Slug للخدمة');
            $table->text('description')->nullable()->comment('تعريف بالخدمة');
            $table->text('requirements')->nullable()->comment('المتطلبات اللازمة للخدمة');

            // الحسابات المالية
            $table->decimal('base_cost', 15, 2)->default(0)->comment('تكلفة الخدمة');
            $table->decimal('price', 15, 2)->default(0)->comment('سعر البيع');
            $table->decimal('min_price', 15, 2)->default(0)->comment('أقل سعر مسموح به');
            
            $table->decimal('max_discount', 15, 2)->default(0)->comment('أقصى خصم مسموح به');
            $table->string('discount_type')
                ->default(\App\Enums\DiscountType::AMOUNT->value) // استخدام القيمة من الـ Enum
                ->comment('نوع الخصم: مبلغ أو نسبة');

            $table->boolean('status')
                ->default(\App\Enums\ActiveStatus::ACTIVE->value)
                ->index();

            // هل الخدمة خاضعة للضريبة؟ (نعم أو لا)
            $table->boolean('is_taxable')->default(true)->comment('حالة الخضوع للضريبة');
            // نسبة الضريبة (مثلاً: 15.00 تعني 15%)
            $table->decimal('tax_rate', 5, 2)->default(00.00)->comment('نسبة الضريبة المئوية');

            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
