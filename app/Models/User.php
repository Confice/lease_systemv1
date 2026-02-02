<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // base class for authenticatable users (for login, remember token, etc.)
use Illuminate\Database\Eloquent\Factories\HasFactory; // provide model factories
use Illuminate\Database\Eloquent\SoftDeletes; // allow soft deletes if user is soft-deleted
use Illuminate\Notifications\Notifiable; // provide notifications ability (optional/useful for email verification)
use Illuminate\Support\Str; // string helper functions

class User extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable; // apply traits: factories, soft deletes, notifications
    protected $table = 'users'; // table name (optional because default is 'users', but explicit for clarity)
    protected $primaryKey = 'id'; // primary key column (default is 'id' — kept explicit here)

    // attributes that can be mass-assigned (create/update)
    // include only columns that are safe to set from request data
    protected $fillable = [
        'firstName',            // user's first name
        'middleName',           // user's middle name (nullable)
        'lastName',             // user's last name
        'email',                // login email (unique in migration)
        'password',             // hashed password
        'homeAddress',          // optional address
        'contactNo',            // optional phone number
        'birthDate',            // optional birthday
        'role',                 // Lease Manager or Tenant
        'userStatus',           // account status
        'customReason',         // reason for deactivating account
        'email_verified_at',    // timestamp if email verified
        'isFirstLogin',         // boolean for first login checks
        'themePreference',      // ui theme preference
        'reduceMotion'          // ui motion preference
    ];

    // attributes hidden when model is converted to arrays/JSON (for API responses)
    protected $hidden = [
        'password',        // never return password
        'remember_token',  // don't expose remember token
    ];

    // type casting to PHP types when retrieving from DB
    protected $casts = [
        'birthDate' => 'date',            // Carbon date object
        'email_verified_at' => 'datetime',// DateTime
        'isFirstLogin' => 'boolean',      // bool
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'reduceMotion' => 'boolean'
    ];

    // -----------------------
    // Formatting rules
    // -----------------------
    protected static $formatRules = [
        'capitalize' => ['firstName', 'middleName', 'lastName', 'homeAddress'],
        'uppercase'  => [],
        'lowercase'  => ['email'],
        'phone'      => ['contactNo'],
    ];

    protected static function booted()
    {
        static::saving(function ($user) {
            // Capitalize each word
            foreach (self::$formatRules['capitalize'] as $field) {
                if (!empty($user->$field)) {
                    $user->$field = Str::title($user->$field);
                }
            }

            // Uppercase
            foreach (self::$formatRules['uppercase'] as $field) {
                if (!empty($user->$field)) {
                    $user->$field = strtoupper($user->$field);
                }
            }

            // Lowercase
            foreach (self::$formatRules['lowercase'] as $field) {
                if (!empty($user->$field)) {
                    $user->$field = strtolower($user->$field);
                }
            }

            // Phone formatting: XXXX-XXX-XXXX
            foreach (self::$formatRules['phone'] as $field) {
                if (!empty($user->$field)) {
                    $digits = preg_replace('/\D/', '', $user->$field);
                    if (strlen($digits) === 11) {
                        $user->$field = substr($digits, 0, 4) . '-' . substr($digits, 4, 3) . '-' . substr($digits, 7, 4);
                    }
                }
            }
        });
    }

    // Accessor: automatically format on retrieval
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, self::$formatRules['capitalize']) && $value) {
            return Str::title($value);
        }

        if (in_array($key, self::$formatRules['uppercase']) && $value) {
            return strtoupper($value);
        }

        if (in_array($key, self::$formatRules['lowercase']) && $value) {
            return strtolower($value);
        }

        if (in_array($key, self::$formatRules['phone']) && $value) {
            $digits = preg_replace('/\D/', '', $value);
            return strlen($digits) === 11
                ? substr($digits, 0, 4) . '-' . substr($digits, 4, 3) . '-' . substr($digits, 7, 4)
                : $value;
        }

        return $value;
    }

    /* 
       -------------------------
       Relationships (Eloquent)
       ------------------------- 
    */

    // one user can own many stores (userID foreign key in stores)
    public function stores()
    {
        return $this->hasMany(Store::class, 'userID');
    }

    // one user can submit many applications
    public function applications()
    {
        return $this->hasMany(Application::class, 'userID');
    }

    // one user can have many contracts (tenant signs contracts)
    public function contracts()
    {
        return $this->hasMany(Contract::class, 'userID');
    }

    // one user can upload many documents
    public function documents()
    {
        return $this->hasMany(Document::class, 'userID');
    }

    // user has many activity logs (audit)
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'userID');
    }
}
