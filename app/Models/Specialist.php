<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialist extends Model
{
    protected $fillable = [
        'name',
        'role',
        'bio',
        'image',
        'user_id',
    ];

    /**
     * Relación: Un especialista tiene muchas citas asignadas en su agenda.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Relación: Un especialista pertenece a un usuario de acceso.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
