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
        Schema::create('qa_reviews', function (Blueprint $table) {
            $table->id();

            // Foreign key linking to the HazardReport
            $table->foreignId('hazard_report_id')->constrained('hazard_reports')->onDelete('cascade');
            
            // Foreign key linking to the user/auditor (QaUserId)
            $table->foreignId('qa_user_id')->constrained('users')->onDelete('cascade');
            
            $table->timestamp('qa_review_date')->useCurrent();
            
            // Evaluation fields
            $table->text('qa_hazard_description')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('immediate_action')->nullable();
            $table->text('corrective_action')->nullable();
            
            // Risk Matrix fields
            $table->integer('severity_rating')->nullable();
            $table->integer('likelihood_rating')->nullable();
            $table->integer('risk_score')->virtualAs('severity_rating * likelihood_rating')->nullable(); // Calculated column
            
            // Dates
            $table->date('target_compliance_date')->nullable();
            $table->date('actual_closure_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qa_reviews');
    }
};
