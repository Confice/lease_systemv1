<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'documents';
    protected $primaryKey = 'documentID';

    // mass-assignable fields (we keep file storage fields simple)
    protected $fillable = [
        'userID',
        'documentType',       // Proposal, Tenancy
        'requirementConfig',  // optional admin JSON
        'files',              // JSON array of uploaded file objects
        'docStatus',          // pending/approved/etc (note migration uses 'docStatus')
        'revisionComment',
        'applicationID',
        'contractID'
    ];

    // cast JSON columns to arrays automatically
    protected $casts = [
        'requirementConfig' => 'array', // read/write as PHP array
        'files' => 'array'              // each file object becomes PHP array
    ];

    /* 
       -------------------------
       Relationships (Eloquent)
       ------------------------- 
    */

    // document belongs to the uploader (user)
    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    // document may belong to an application (when uploaded during application)
    public function application()
    {
        return $this->belongsTo(Application::class, 'applicationID');
    }

    // document may belong to a contract (when uploaded for tenancy docs)
    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contractID');
    }
}
