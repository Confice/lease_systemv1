<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stall extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'stalls';
    protected $primaryKey = 'stallID';

    // fillable attributes matching migration
    protected $fillable = [
        'stallNo',
        'storeID',
        'marketplaceID',
        'size',
        'rentalFee',
        'applicationDeadline',
        'stallStatus',
        'lastStatusChange'
    ];

    // type casting for timestamps and numbers
    protected $casts = [
        'applicationDeadline' => 'datetime',
        'lastStatusChange' => 'datetime',
        'rentalFee' => 'decimal:2'
    ];

    /* 
       -------------------------
       Relationships (Eloquent)
       ------------------------- 
    */

    // stall belongs to a marketplace
    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class, 'marketplaceID');
    }

    // stall optionally belongs to a store (nullable)
    public function store()
    {
        return $this->belongsTo(Store::class, 'storeID');
    }

    // stall can have many applications (prospective tenants)
    public function applications()
    {
        return $this->hasMany(Application::class, 'stallID');
    }

    // stall can have many contracts over time
    public function contracts()
    {
        return $this->hasMany(Contract::class, 'stallID');
    }

    // stall can have many bills (monthly charges)
    public function bills()
    {
        return $this->hasMany(Bill::class, 'stallID');
    }

    /**
     * Get formatted stall ID based on marketplace
     * Returns HUB-000X for "The Hub by D & G Properties"
     * Returns BAZ-000X for "Your One-Stop Bazaar"
     * Returns default format for other marketplaces
     */
    public function getFormattedStallIdAttribute()
    {
        $marketplaceName = $this->marketplace ? $this->marketplace->marketplace : '';
        $marketplaceUpper = strtoupper($marketplaceName);
        
        $prefix = '';
        
        // Check for "The Hub by D & G Properties"
        if (str_contains($marketplaceUpper, 'THE HUB') || str_contains($marketplaceUpper, 'HUB BY D & G')) {
            $prefix = 'HUB-';
        }
        // Check for "Your One-Stop Bazaar"
        elseif (str_contains($marketplaceUpper, 'ONE-STOP BAZAAR') || str_contains($marketplaceUpper, 'YOUR ONE-STOP')) {
            $prefix = 'BAZ-';
        }
        // Default: use first 3 letters if no match
        else {
            $prefix = strtoupper(substr($marketplaceName, 0, 3)) . '-';
        }
        
        return $prefix . str_pad($this->stallID, 4, '0', STR_PAD_LEFT);
    }
}
