<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
    protected $table = 'activity_logs';
    protected $primaryKey = 'activityID';  

    // fillable attributes
    protected $fillable = [
        'actionType', // Create, Update, Delete, Login, Logout, Other
        'entity',     // table/entity name
        'entityID',   // id of the record affected
        'description',// details of action
        'userID'      // actor user ID
    ];

    /* 
       -------------------------
       Relationships (Eloquent)
       ------------------------- 
    */
       
    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    /**
     * Get the formatted date attribute
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('M d, Y h:i A');
    }
}