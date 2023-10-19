<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'company',
        'email',
        'industry',
        'activity',
        'identifier',
        'cif',
        'birthdate',
        'country',
        'city',
        'province',
        'address',
        'zipcode',
        'fax',
        'phone',
        'web',
        'facebook',
        'instagram',
        'pinterest',
        'is_client',
        'cookies_accepted',
        'privacy_policy_accepted',
        'newsletters_sending_accepted',
        'notes',
    ];
    
    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at', 
    ];
}
