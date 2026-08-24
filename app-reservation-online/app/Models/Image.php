<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;

    protected $fillable = ['nom' , 'path'];

    public function salles() : BelongsToMany {
        return $this->belongsToMany(Salle::class)->withPivot('modifier_designation');
    }
}
