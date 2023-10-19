<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference',
        'reference_autoincrement_id',
        'admin_user_id',
        'client_id',
        'project_id',
        'payment_method_id',
        'budget_status_id',
        'level_commission',
        'duracion',
        'cuotas_mensuales',
        'commercial_id',
        'temp',
        'concept',
        'creation_date',
        'description',
        'gross',
        'base',
        'iva',
        'iva_percentage',
        'total',
        'discount',
        'expiration_date',
        'accepted_date',
        'cancelled_date',
        'note',
        'billed_in_advance',
        'retention_percentage',
        'total_retention',
        'invoiced_advance',
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
