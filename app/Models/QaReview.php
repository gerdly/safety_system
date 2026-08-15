<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QaReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'hazard_report_id',
        'qa_user_id',
        'qa_review_date',
        'qa_hazard_description',
        'root_cause',
        'immediate_action',
        'corrective_action',
        'severity_rating',
        'likelihood_rating',
        'target_compliance_date',
        'actual_closure_date'
    ];

    public function hazardReport()
    {
        return $this->belongsTo(HazardReport::class);
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'qa_user_id');
    }
}