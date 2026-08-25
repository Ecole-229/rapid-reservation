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
}
