<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\HazardReport;

class HazardManagement extends Component
{
    use WithPagination;

    // Property bound to the search input field in the frontend
    public $search = '';
    
    // Optional: Property to filter by a specific MRO if needed in the future
    public $selectedCompanyId = null;

    /**
     * Reset the pagination back to page 1 whenever the search term changes.
     */
    public function updatingSearch()
    {
        // This is a Livewire lifecycle hook
        $this->resetPage();
    }

    /**
     * Render the component using Eloquent scopes instead of a Service class.
     */
    public function render()
    {
        // Use the custom local scopes defined in the HazardReport model
        $hazards = HazardReport::with(['qaReview', 'company'])
            ->forCompany($this->selectedCompanyId)
            ->search($this->search)
            ->orderBy('incident_date', 'desc')
            ->paginate(10);

        return view('livewire.hazard-management', [
            'hazards' => $hazards
        ]);
    }
}