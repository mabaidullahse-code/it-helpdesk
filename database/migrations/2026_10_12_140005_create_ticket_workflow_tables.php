<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('assigned_to_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('previous_technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('ticket_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->string('action_label');
            $table->text('comment')->nullable();
            $table->boolean('is_visible_to_complainant')->default(true);
            $table->timestamps();
        });

        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('comment');
            $table->boolean('is_internal')->default(false); // true: IT internal only, false: visible to complainant
            $table->string('type')->default('general'); // general, investigation, info_request, info_response, resolution_summary
            $table->timestamps();
        });

        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('uploaded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('file_type')->nullable();
            $table->boolean('is_internal')->default(false);
            $table->timestamps();
        });

        Schema::create('ticket_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->unique()->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1 to 5
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_feedback');
        Schema::dropIfExists('ticket_attachments');
        Schema::dropIfExists('ticket_comments');
        Schema::dropIfExists('ticket_status_histories');
        Schema::dropIfExists('ticket_assignments');
    }
};
