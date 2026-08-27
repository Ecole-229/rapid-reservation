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
        'nom_client',
        'telephone_client',
        'cree_par_id',
    ];

    protected $casts = [
        'date_heure_debut' => 'datetime',
        'date_heure_fin' => 'datetime',
        'terminee_at' => 'datetime',
    ];

    protected $appends = ['nom_demandeur'];

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

    public function creePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cree_par_id');
    }

    public function getNomDemandeurAttribute(): string
    {
        return $this->user?->nom ?? $this->nom_client ?? 'Client sans compte';
    }
}
