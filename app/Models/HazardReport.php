<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HazardReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'reporter_name',
        'submitted_date',
        'incident_date',
        'department_area',
        'employee_hazard_description',
        'suggested_mitigation'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function qaReview()
    {
        return $this->hasOne(QaReview::class);
    }

    // Accessor equivalent to C# logic for masking the origin
    public function getDisplayNameAttribute()
    {
        return $this->reporter_name ?? 'Anonymous';
    }

    /**
     * Local scope to filter by search term.
     */
    public function scopeSearch($query, $searchTerm)
    {
        if (empty($searchTerm)) {
            return $query;
        }

        return $query->where(function ($subQuery) use ($searchTerm) {
            $subQuery->where('department_area', 'like', '%' . $searchTerm . '%')
                     ->orWhere('employee_hazard_description', 'like', '%' . $searchTerm . '%')
                     ->orWhere('reporter_name', 'like', '%' . $searchTerm . '%');
        });
    }

    /**
     * Local scope to filter by a specific MRO company.
     */
    public function scopeForCompany($query, $companyId)
    {
        if (empty($companyId)) {
            return $query;
        }

        return $query->where('company_id', $companyId);
    }
}