<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'bills';
    protected $primaryKey = 'billID'; 

    // fillable fields matching migration
    protected $fillable = [
        'stallID',
        'contractID',
        'dueDate',
        'amount',
        'datePaid',
        'status',
        'paymentProof',
        'dateUploaded'
    ];

    // casts for date fields and amounts
    protected $casts = [
        'dueDate' => 'datetime',
        'datePaid' => 'datetime',
        'dateUploaded' => 'datetime',
        'amount' => 'decimal:2'
    ];

    /* 
       -------------------------
       Relationships (Eloquent)
       ------------------------- 
    */

    // bill belongs to a stall
    public function stall()
    {
        return $this->belongsTo(Stall::class, 'stallID');
    }

    // bill belongs to a contract
    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contractID');
    }
}
