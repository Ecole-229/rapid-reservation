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
}
