<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reservation extends Model
{
    protected $fillable = ['date_heure_reservation_debut' , 'date_fin_reservation' , 'durer_reservation' , 'nombre_personnes' , 'status' , 'user_id' , 'completed_at'] ;

    public function user(): BelongsTo {
        return $this->belongsTo(User::class) ;
    }

    public function salle(): BelongsTo {
        return $this->belongsTo(User::class) ;
    }

    public function equipements(): BelongsToMany {
        return $this->belongsToMany(Equipement::class)->withPivot('quantity') ;
    }
}
