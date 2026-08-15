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
        Schema::create('hazard_reports', function (Blueprint $table) {
            $table->id();
            // Foreign key linking to the companies table (CompanyId)
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            
            // Nullable for the Anonymous Reporting logic
            $table->string('reporter_name')->nullable(); 
            
            // DateTime fields
            $table->timestamp('submitted_date')->useCurrent();
            $table->date('incident_date');
            
            // Descriptive fields
            $table->string('department_area');
            $table->text('employee_hazard_description');
            $table->text('suggested_mitigation')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hazard_reports');
    }
};
