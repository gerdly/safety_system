<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\HazardReport;
use App\Models\Company;

// Utilize the guest layout for unauthenticated public access
#[Layout('layouts.guest')]
class PublicHazardReport extends Component
{
    // Store available companies for the dropdown
    public $companies = [];

    // Capture the company ID from the URL if accessed via a specific QR code (e.g., ?company_id=2)
    #[Url]
    public $company_id = '';

    // Form fields based exactly on the original .NET SMS structure
    public $is_anonymous = false;
    public $reporter_name = '';
    public $incident_date = '';
    public $department_area = '';
    public $employee_hazard_description = '';
    public $suggested_mitigation = '';

    // UI state to show the thank you message
    public $report_submitted = false;

    public function mount()
    {
        // Load all active companies to populate the dropdown
        $this->companies = Company::all();
        
        // Default the incident date to today to improve mobile UX
        $this->incident_date = now()->format('Y-m-d');
    }

    public function submitReport()
    {
        // Validate the input data before processing
        $this->validate([
            'company_id' => 'required|exists:companies,id',
            'incident_date' => 'required|date|before_or_equal:today',
            'department_area' => 'required|string|max:255',
            'employee_hazard_description' => 'required|string',
            'suggested_mitigation' => 'nullable|string',
        ]);

        // Save the new hazard report utilizing Eloquent ORM
        HazardReport::create([
            'company_id' => $this->company_id,
            // Evaluate the anonymous toggle to decide what to save
            'reporter_name' => $this->is_anonymous ? null : $this->reporter_name,
            'incident_date' => $this->incident_date,
            'department_area' => $this->department_area,
            'employee_hazard_description' => $this->employee_hazard_description,
            'suggested_mitigation' => $this->suggested_mitigation,
        ]);

        // Update the UI state to hide the form and show the success message
        $this->report_submitted = true;
    }

    public function render()
    {
        return view('livewire.public-hazard-report');
    }
}