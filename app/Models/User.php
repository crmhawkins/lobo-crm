<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $attributes = [
        // Valores predeterminados
        'almacen_id' => 0,
    ];
    protected $fillable = [
        'user_department_id',
        'username',
        'name',
        'surname',
        'role',
        'email',
        'password',
        'image',
        'seniority_years',
        'seniority_months',
        'holidays_days',
        'inactive',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function isAdmin(){
        return $this->role == 1 ? true : false;

    }
    public function isdirectorcomercial(){
        return ($this->role == 1 || $this->role == 2) ? true : false;

    }
    public function iscomercial(){
        return ($this->role == 1 || $this->role == 3) ? true : false;

    }
    public function isfabrica(){
        return ($this->role == 1 || $this->role == 5) ? true : false;

    }
    public function isalmacen(){
        return ($this->role == 1 || $this->role == 4) ? true : false;

    }
    public function isadministrativo(){
        return ($this->role == 1 || $this->role == 6) ? true : false;

    }
}
