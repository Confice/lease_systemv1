<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $table = 'stores';
    protected $primaryKey = 'storeID';

    // mass assignable fields
    protected $fillable = [
        'storeName',
        'businessType',
        'userID',
        'marketplaceID'
    ];

    /* 
       -------------------------
       Relationships (Eloquent)
       ------------------------- 
    */
       
    // store belongs to a user (owner)
    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    // store belongs to a marketplace
    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class, 'marketplaceID');
    }

    // a store can have multiple stalls
    public function stalls()
    {
        return $this->hasMany(Stall::class, 'storeID');
    }
}
