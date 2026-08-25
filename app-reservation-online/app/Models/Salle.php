<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salle extends Model
{
    use SoftDeletes;

   protected $fillable = [ 'nom' , 'description', 'capacite' , 'status'  , 'prix', 'localisation'];

   public function reservations(): HasMany {
    return $this->hasMany(Reservation::class);
   }

   public function images() : HasMany {
        return $this->hasMany(Image::class);
   }

}
