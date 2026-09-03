<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Salle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom',
        'description',
        'capacite',
        'status',
        'prix',
        'localisation',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }



    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }
    

    public function estDisponible(string|\DateTimeInterface $debut, string|\DateTimeInterface $fin): bool
    {
        $debut = Carbon::parse($debut);
        $fin = Carbon::parse($fin);

        return !$this->reservations()
            ->whereIn('status', ['en_attente', 'confirmee'])
            ->where('date_heure_debut', '<', $fin)
            ->where('date_heure_fin', '>', $debut)
            ->exists();
    }
    /**
     * Scope pour rechercher par nom, description ou localisation.
     */
    public function scopeSearch($query, ?string $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%")
                ->orWhere('localisation', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope pour filtrer par statut (disponible, indisponible).
     */
    public function scopeStatus($query, ?string $status)
    {
        if (empty($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    protected function casts(): array
    {
        return [
            'capacite' => 'integer',
            'prix' => 'decimal:2',
        ];
    }
}
