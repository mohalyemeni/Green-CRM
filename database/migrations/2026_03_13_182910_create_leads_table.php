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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            // --- البيانات الأساسية للهوية ---
            $table->string('lead_number', 50)->unique(); // رقم تسلسلي (مثل LE-00001)
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('full_name', 255)->virtualAs('concat(first_name, " ", last_name)'); // اسم كامل مولد تلقائياً

            // --- بيانات التواصل ---
            $table->string('mobile', 50)->nullable()->index();
            $table->string('whatsapp', 50)->nullable()->index();
            $table->string('phone', 50)->nullable()->comment('رقم هاتف');
            $table->string('email', 150)->nullable()->index();

            // --- بيانات العمل والشركة التابعة للعميل ---
            $table->string('job_title', 150)->nullable();
            $table->string('company_name', 200)->nullable(); // اسم شركة العميل المحتمل
            $table->string('website', 255)->nullable();

            // --- العلاقات (العناصر التي قمت بإنشائها مسبقاً) ---

            // يتبع لأي شركة وفرع في نظامك (من ملفاتك: Companies & Branches)
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');

            // التصنيفات (من ملفاتك: Statuses, Sources, Industries)
            $table->foreignId('lead_status_id')->nullable()->constrained('lead_statuses')->onDelete('set null');
            $table->foreignId('lead_source_id')->nullable()->constrained('lead_sources')->onDelete('set null');
            $table->foreignId('industry_id')->nullable()->constrained('industries')->onDelete('set null');

            // الموظف المسؤول (Owner)
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');

            // --- الموقع الجغرافي (من ملفاتك: Countries) ---
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->string('state', 150)->nullable();
            $table->string('city', 150)->nullable();
            $table->text('address')->nullable();

            // --- البيانات المالية والتقييم ---
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onDelete('set null');
            $table->decimal('estimated_value', 15, 2)->default(0); // القيمة المتوقعة للصفقة
            $table->unsignedTinyInteger('priority')->default(2); // 1: High, 2: Medium, 3: Low
            $table->unsignedTinyInteger('rating')->default(0); // تقييم العميل من 1 إلى 5 نجوم

            // --- بيانات إضافية ---
            $table->text('description')->nullable(); // نبذة عن العميل أو متطلباته
            $table->text('notes')->nullable();
            $table->timestamp('last_contacted_at')->nullable(); // تاريخ آخر تواصل

            // --- بيانات التتبع (Audit Trail) ---
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
