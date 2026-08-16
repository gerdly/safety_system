<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\HazardReport;

// Apply the custom blank layout for printing
#[Layout('layouts.print')]
class HazardReportPrint extends Component
{
    public HazardReport $report;

    /**
     * Initializes the component by fetching the report and its relations.
     */
    public function mount($id)
    {
        // Load report with company and QA review details
        $this->report = HazardReport::with(['company', 'qaReview'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.hazard-report-print');
    }
}