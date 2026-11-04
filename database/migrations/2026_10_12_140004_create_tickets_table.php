<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_id', 30)->unique()->index();
            $table->foreignId('complainant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('complaint_categories')->cascadeOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('complaint_subcategories')->nullOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();

            $table->string('title');
            $table->text('description');

            $table->enum('priority', ['critical', 'high', 'medium', 'low'])->default('medium')->index();
            $table->enum('status', [
                'submitted',
                'under_review',
                'assigned',
                'in_progress',
                'awaiting_information',
                'on_hold',
                'awaiting_confirmation',
                'reopened',
                'escalated',
                'closed',
                'cancelled'
            ])->default('submitted')->index();

            $table->foreignId('assigned_technician_id')->nullable()->constrained('users')->nullOnDelete()->index();

            // Location and hardware details
            $table->string('location')->nullable();
            $table->string('room_number')->nullable();
            $table->string('asset_tag_or_serial')->nullable();
            $table->string('contact_number')->nullable();
            $table->unsignedInteger('affected_users_count')->nullable();
            $table->string('impact_level')->nullable();
            $table->string('preferred_visit_time')->nullable();
            $table->text('additional_info')->nullable();

            // Resolution and lifecycle details
            $table->text('resolution_summary')->nullable();
            $table->text('reopen_reason')->nullable();
            $table->text('on_hold_reason')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Lifecycle timestamps
            $table->timestamp('submitted_at')->nullable()->index();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('work_started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('confirmed_resolved_at')->nullable();
            $table->timestamp('reopened_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            // SLA tracking
            $table->timestamp('sla_response_deadline')->nullable()->index();
            $table->timestamp('sla_resolution_deadline')->nullable()->index();
            $table->timestamp('sla_responded_at')->nullable();
            $table->boolean('is_sla_response_breached')->default(false)->index();
            $table->boolean('is_sla_resolution_breached')->default(false)->index();
            $table->timestamp('sla_paused_at')->nullable();
            $table->unsignedInteger('sla_total_paused_minutes')->default(0);

            $table->timestamps();

            // Composite indexes for fast query filtering
            $table->index(['department_id', 'status']);
            $table->index(['assigned_technician_id', 'status']);
            $table->index(['complainant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
