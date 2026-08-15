<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\HazardReport;
use App\Models\QaReview;
use App\Models\User;
use Carbon\Carbon;

class HazardReportSeeder extends Seeder
{
    public function run(): void
    {
        // Retrieve the first company and the superadmin user
        $company = Company::first();
        $adminUser = User::where('role', 'superadmin')->first();

        // Create a standard hazard report
        $report1 = HazardReport::create([
            'company_id' => $company->id,
            'reporter_name' => 'John Doe',
            'incident_date' => Carbon::now()->subDays(5)->toDateString(),
            'department_area' => 'Hangar B',
            'employee_hazard_description' => 'Oil spill near the main exit door. It is a slipping hazard.',
            'suggested_mitigation' => 'Clean the spill and place a warning sign.',
        ]);

        // Create a QA review for the first report
        QaReview::create([
            'hazard_report_id' => $report1->id,
            'qa_user_id' => $adminUser->id,
            'qa_hazard_description' => 'Confirmed oil spill due to a leaking hydraulic machine.',
            'root_cause' => 'Machine maintenance overdue.',
            'immediate_action' => 'Spill cleaned and warning signs placed.',
            'severity_rating' => 3,
            'likelihood_rating' => 4,
            'target_compliance_date' => Carbon::now()->addDays(2)->toDateString(),
        ]);

        // Create an anonymous hazard report
        HazardReport::create([
            'company_id' => $company->id,
            'reporter_name' => null, // Anonymous logic
            'incident_date' => Carbon::now()->subDays(2)->toDateString(),
            'department_area' => 'Ramp Area',
            'employee_hazard_description' => 'Faded lines on the pedestrian walkway make it hard to see at night.',
            'suggested_mitigation' => 'Repaint the lines with reflective paint.',
        ]);
    }
}