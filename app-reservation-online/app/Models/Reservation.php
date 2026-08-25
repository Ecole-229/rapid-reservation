<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'date_heure_debut',
        'date_heure_fin',
        'nombre_personnes',
        'status',
        'user_id',
        'salle_id',
        'terminee_at',
    ];

    protected $casts = [
        'date_heure_debut' => 'datetime',
        'date_heure_fin' => 'datetime',
        'terminee_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class);
    }

    public function equipements(): BelongsToMany
    {
        return $this->belongsToMany(
            Equipement::class,
            'equipement_reservation'
        )->withPivot('quantity');
    }
}
