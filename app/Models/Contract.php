<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'contracts';
    protected $primaryKey = 'contractID';

    // fillable fields matching migration
    protected $fillable = [
        'stallID',
        'userID',
        'startDate',
        'endDate',
        'contractStatus',
        'expiringStatus',
        'customReason',
        'renewedFrom'
    ];

    // cast date fields to Carbon instances
    protected $casts = [
        'startDate' => 'datetime',
        'endDate' => 'datetime',
    ];

    /* 
       -------------------------
       Relationships (Eloquent)
       ------------------------- 
    */

    // which stall this contract is for
    public function stall()
    {
        return $this->belongsTo(Stall::class, 'stallID');
    }

    // which user (tenant) this contract belongs to
    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    // contract that this contract was renewed from (self relation)
    public function renewedFromContract()
    {
        return $this->belongsTo(Contract::class, 'renewedFrom');
    }

    // feedbacks tied to this contract
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'contractID'); 
    }

    // bills under this contract
    public function bills()
    {
        return $this->hasMany(Bill::class, 'contractID');
    }

    // documents attached to this contract
    public function documents()
    {
        return $this->hasMany(Document::class, 'contractID');
    }
}
