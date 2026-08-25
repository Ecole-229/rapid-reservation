<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom',
        'description',
        'status',
        'stock_total',
        'image',
    ];

    public function reservations(): BelongsToMany
    {
        return $this->belongsToMany(
            Reservation::class,
            'equipement_reservation'
        )->withPivot('quantity');
    }

    public function quantiteDisponible(string|\DateTimeInterface $debut, string|\DateTimeInterface $fin): int
    {
        $debut = \Carbon\Carbon::parse($debut);
        $fin = \Carbon\Carbon::parse($fin);

        $quantiteReservee = $this->reservations()
            ->whereIn('reservations.status', ['en_attente', 'confirmee'])
            ->where('date_heure_debut', '<', $fin)
            ->where('date_heure_fin', '>', $debut)
            ->sum('equipement_reservation.quantity');

        return $this->stock_total - $quantiteReservee;
    }
}
