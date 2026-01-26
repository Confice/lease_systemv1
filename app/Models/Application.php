<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'applications';
    protected $primaryKey = 'applicationID'; 

    // mass assignable attributes
    protected $fillable = [
        'userID',
        'stallID',
        'dateApplied',
        'appStatus',
        'remarks',
        'noticeType',
        'noticeDate',
        'contractID'
    ];

    // cast dates
    protected $casts = [
        'dateApplied' => 'datetime',
        'noticeDate' => 'datetime'
    ];

    /* 
       -------------------------
       Relationships (Eloquent)
       ------------------------- 
    */

    // application belongs to the applying user
    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    // application is for a particular stall
    public function stall()
    {
        return $this->belongsTo(Stall::class, 'stallID');
    }

    // application may be linked to a contract (after approval)
    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contractID');
    }

    // application may have multiple uploaded documents (stored in documents table linked by applicationID)
    public function documents()
    {
        return $this->hasMany(Document::class, 'applicationID');
    }
}
