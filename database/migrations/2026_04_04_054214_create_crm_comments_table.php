<?php

use App\Enums\CommentType;
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
        Schema::create('crm_comments', function (Blueprint $table) {
            $table->id();

            // --- العلاقة Polymorphic ---
            $table->morphs('commentable'); // commentable_id و commentable_type

            // --- محتوى التعليق ---
            $table->text('body'); // نص التعليق

            // --- الردود المتداخلة (Nested Comments) ---
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('crm_comments')
                ->cascadeOnDelete();

            // --- نوع التعليق ---
            $table->string('type', 30)->default(CommentType::NOTE->value); // comment, question, answer, internal

            // --- حالة التعليق ---
            $table->boolean('is_pinned')->default(false); // تعليق مثبت
            $table->boolean('is_internal')->default(false); // تعليق داخلي (لا يراه العميل)
            $table->boolean('is_resolved')->default(false); // تم حله (للأسئلة)

            // --- الإشارات (Mentions) ---
            $table->json('mentions')->nullable(); // [user_id1, user_id2, ...]

            // --- كاتب التعليق ---
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            // فهارس
            $table->index(['commentable_type', 'commentable_id', 'created_at']);
            $table->index('parent_id');
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_comments');
    }
};
