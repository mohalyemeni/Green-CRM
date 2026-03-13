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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();

            // --- البيانات الأساسية ---
            $table->string('title', 255); // عنوان الفرصة
            $table->text('description')->nullable(); // وصف التفاصيل
            $table->string('opportunity_number', 50)->unique(); // رقم مرجعي تلقائي (مثل OPP-2024-001)

            // --- الروابط والعلاقات المرجعية (Foreign Keys) ---

            // الشركة والفرع (من ملفاتك: Companies & Branches)
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');

            // العميل أو جهة الاتصال (من ملفك: Customer)
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');

            // قمع المبيعات والمرحلة (من ملفك: PipelineStages)
            // ملاحظة: يُفترض وجود جدول pipelines لإدارة أنواع الأقماع المختلفة
            $table->foreignId('pipeline_id')->nullable()->constrained('pipelines')->onDelete('set null');
            $table->foreignId('stage_id')->nullable()->constrained('pipeline_stages')->onDelete('set null');

            // مصدر الفرصة (من ملفك: OpportunitySources)
            $table->foreignId('opportunity_source_id')->nullable()->constrained('opportunity_sources')->onDelete('set null');

            // النوع (يُمكن ربطه بجدول تصنيفات أو إبقاؤه نصياً)
            $table->string('opportunity_type', 100)->nullable();

            // --- البيانات المالية والتقييم ---

            // العملة (من ملفك: Currencies)
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onDelete('set null');
            $table->decimal('expected_revenue', 15, 2)->default(0); // الإيراد المتوقع

            // الاحتمالية (%) - يتم تحديثها عادة تلقائياً من جدول الـ Stages ولكن يمكن تخصيصها هنا
            $table->integer('probability')->default(0);

            // التوقيت
            $table->date('expected_close_date')->nullable(); // تاريخ الإغلاق المتوقع
            $table->timestamp('closed_at')->nullable(); // تاريخ الإغلاق الفعلي

            // الأولويات والحالة
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');

            // --- الإغلاق والخسارة ---

            // سبب الخسارة (من ملفك: LostReasons)
            // قمت بتغييره من text إلى FK لربطه بجدول الأسباب الذي جهزته
            $table->foreignId('lost_reason_id')->nullable()->constrained('lost_reasons')->onDelete('set null');
            $table->text('lost_reason_notes')->nullable(); // ملاحظات إضافية عند الخسارة

            // --- المسؤوليات والتتبع (Audit Trail) ---
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // الموظف المسؤول
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes(); // للحذف المنطقي
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
