<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\HazardReport;
use App\Models\QaReview; // Ensure this model exists

class HazardManagement extends Component
{
    use WithPagination;

    // Loading states are automatically handled by Livewire in the frontend, 
    // but we can keep a processing state for the button.
    public $isProcessing = false;

    /**
     * Closes a hazard report by setting the actual closure date.
     * Equivalent to CloseHazardReportAsync in .NET.
     */
    public function closeHazard($reportId)
    {
        $this->isProcessing = true;

        $review = QaReview::where('hazard_report_id', $reportId)->first();

        if ($review && is_null($review->actual_closure_date)) {
            $review->update([
                'actual_closure_date' => now()
            ]);
            
            session()->flash('message', 'Hazard report closed successfully.');
        }

        $this->isProcessing = false;
    }

    /**
     * Fetch reports and render the view.
     * Equivalent to GetAllHazardReportsAsync in .NET.
     */
    public function render()
    {
        // Eloquent handles the Include() with the 'with' method
        $reports = HazardReport::with(['company', 'qaReview'])
            ->orderBy('incident_date', 'desc')
            ->paginate(10);

        return view('livewire.hazard-management', [
            'reports' => $reports
        ]);
    }
}