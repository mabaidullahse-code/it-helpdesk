<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('priority', ['critical', 'high', 'medium', 'low'])->unique();
            $table->unsignedInteger('response_time_hours')->default(4); // target hours to respond/assign
            $table->unsignedInteger('resolution_time_hours')->default(24); // target hours to complete resolution
            $table->unsignedInteger('escalation_threshold_hours')->nullable();
            $table->unsignedInteger('reminder_interval_hours')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_policies');
    }
};
