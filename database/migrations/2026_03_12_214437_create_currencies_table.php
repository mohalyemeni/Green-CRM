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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // كود العملة 

            $table->string('name', 100);
            $table->string('symbol', 10)->nullable();
            $table->string('fraction_name', 50)->nullable();

            $table->decimal('exchange_rate', 15, 6)->default(1);
            $table->decimal('equivalent', 15, 6)->default(1);
            $table->decimal('max_exchange_rate', 15, 6)->default(0);
            $table->decimal('min_exchange_rate', 15, 6)->default(0);

            $table->boolean('is_local')->default(false);
            $table->boolean('is_inventory')->default(false);
            // خيارات العرض والحالة
            $table->boolean('status')->default(ActiveStatus::ACTIVE->value); // حالة التفعيل
            $table->text('notes')->nullable();

            // -------------------------------------------------------------------
            // الربط مع جدول المستخدمين (Users) مع خاصية Set Null عند الحذف
            // -------------------------------------------------------------------
            $table->foreignId('created_by')
                ->nullable()                  // يسمح بأن تكون القيمة Null
                ->constrained('users')        // يربط الحقل بجدول users
                ->nullOnDelete();             // عند حذف المستخدم، تصبح القيمة Null

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('deleted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            // -------------------------------------------------------------------

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
