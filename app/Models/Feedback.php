<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';
    protected $primaryKey = 'feedbackID';

    protected $fillable = [
        'contractID',
        'user_id',
        'usability_comprehension',
        'usability_learning',
        'usability_effort',
        'usability_interface',
        'functionality_registration',
        'functionality_tasks',
        'functionality_results',
        'functionality_security',
        'reliability_error_handling',
        'reliability_command_tolerance',
        'reliability_recovery',
        'comments',
        'archived_at',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contractID');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the route key for implicit route model binding (URL uses feedbackID).
     */
    public function getRouteKeyName()
    {
        return 'feedbackID';
    }
}
