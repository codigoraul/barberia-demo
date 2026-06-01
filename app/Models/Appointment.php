<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'service_id',
        'specialist_id',
        'date',
        'time',
        'total_price',
        'status',
    ];

    /**
     * Propiedades que deben ser convertidas de forma automática (casteo).
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relación: Una cita pertenece a un servicio.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relación: Una cita pertenece a un especialista (barbero).
     */
    public function specialist(): BelongsTo
    {
        return $this->belongsTo(Specialist::class);
    }
}
