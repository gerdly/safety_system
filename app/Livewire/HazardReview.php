<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\HazardReport;
use App\Models\QaReview; // Asegúrate de tener este modelo creado
use Carbon\Carbon;

#[Layout('layouts.app')]
class HazardReview extends Component
{
    public HazardReport $report;

    // Form Fields
    public $qa_hazard_description = '';
    public $root_cause = '';
    public $immediate_action = '';
    public $corrective_action = '';
    public $severity_rating = 1;
    public $likelihood_rating = 1;
    public $target_compliance_date;

    // Calculated state
    public $current_risk_score = 1;
    public $is_high_risk_alert = false;

    /**
     * Initializes the component with the specific report data.
     */
    public function mount($id)
    {
        // Load the report with its relationships
        $this->report = HazardReport::with(['company', 'qaReview'])->findOrFail($id);

        // Run the initial risk calculation
        $this->calculateRiskAndCompliance();
    }

    /**
     * Livewire magic lifecycle hook: Triggers automatically when $severity_rating changes.
     */
    public function updatedSeverityRating()
    {
        $this->calculateRiskAndCompliance();
    }

    /**
     * Livewire magic lifecycle hook: Triggers automatically when $likelihood_rating changes.
     */
    public function updatedLikelihoodRating()
    {
        $this->calculateRiskAndCompliance();
    }

    /**
     * Recalculates the Risk Score and assigns the Target Compliance Date.
     */
    public function calculateRiskAndCompliance()
    {
        // Ensure we are multiplying integers
        $this->current_risk_score = (int)$this->severity_rating * (int)$this->likelihood_rating;
        $this->is_high_risk_alert = false;

        // Auto-assign the compliance target date based on the SRM manual rules
        if ($this->current_risk_score >= 1 && $this->current_risk_score <= 5) {
            $this->target_compliance_date = now()->addDays(90)->format('Y-m-d');
        } elseif ($this->current_risk_score >= 6 && $this->current_risk_score <= 10) {
            $this->target_compliance_date = now()->addDays(60)->format('Y-m-d');
        } elseif ($this->current_risk_score >= 11 && $this->current_risk_score <= 15) {
            $this->target_compliance_date = now()->addDays(30)->format('Y-m-d');
        } elseif ($this->current_risk_score >= 16 && $this->current_risk_score <= 24) {
            $this->target_compliance_date = now()->addDays(15)->format('Y-m-d');
        } elseif ($this->current_risk_score == 25) {
            $this->target_compliance_date = now()->addDays(15)->format('Y-m-d');
            $this->is_high_risk_alert = true;
        }
    }

    /**
     * Validates and saves the QA Review.
     */
    public function submitReview()
    {
        $this->validate([
            'qa_hazard_description' => 'required|string',
            'root_cause' => 'required|string',
            'immediate_action' => 'required|string',
            'corrective_action' => 'required|string',
            'severity_rating' => 'required|integer|min:1|max:5',
            'likelihood_rating' => 'required|integer|min:1|max:5',
            'target_compliance_date' => 'required|date',
        ]);

        QaReview::create([
            'hazard_report_id' => $this->report->id,
            'qa_user_id' => auth()->id(),
            'qa_hazard_description' => $this->qa_hazard_description,
            'root_cause' => $this->root_cause,
            'immediate_action' => $this->immediate_action,
            'corrective_action' => $this->corrective_action,
            'severity_rating' => $this->severity_rating,
            'likelihood_rating' => $this->likelihood_rating,
            'risk_score' => $this->current_risk_score,
            'target_compliance_date' => $this->target_compliance_date,
            'qa_review_date' => now(),
        ]);

        session()->flash('message', 'QA Review submitted successfully.');
        
        // Redirect back to the dashboard/reports list
        return redirect()->route('hazards.index'); // Adjust name to match your routes
    }

    public function render()
    {
        return view('livewire.hazard-review');
    }
}