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
        Schema::create('service_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('service_groups')
                ->nullOnDelete();
            $table->string('name')->index()->comment('اسم الخدمة');
            $table->text('description')->nullable()->comment('تعريف بالخدمة');
            $table->text('requirements')->nullable()->comment('المتطلبات اللازمة للخدمة');

            $table->boolean('status')
                ->default(\App\Enums\ActiveStatus::ACTIVE->value)
                ->index();

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
        Schema::dropIfExists('service_groups');
    }
};
