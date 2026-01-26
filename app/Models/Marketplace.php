<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Marketplace extends Model
{
    use HasFactory, SoftDeletes; 
    protected $table = 'marketplaces';
    protected $primaryKey = 'marketplaceID';

    // columns allowed for mass assignment
    protected $fillable = [
        'marketplace',        // marketplace name (note: column name used in migration)
        'marketplaceAddress', // address
        'logoPath',           // optional logo path
        'facebookLink',       // optional facebook page
        'telephoneNo',        // landline
        'viberNo'             // viber contact
    ];

    /* 
       -------------------------
       Relationships (Eloquent)
       ------------------------- 
    */

    // marketplace has many stores
    public function stores()
    {
        return $this->hasMany(Store::class, 'marketplaceID');
    }

    // marketplace has many stalls
    public function stalls()
    {
        return $this->hasMany(Stall::class, 'marketplaceID');
    }
}
