<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use SoftDeletes;

    protected $fillable = ['nom', 'path', 'designation', 'salle_id'];

    protected $appends = ['url'];

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class);
    }

    /**
     * Accesseur pour obtenir l'URL complète accessible de l'image.
     */
    public function getUrlAttribute(): ?string
    {
        if (empty($this->path)) {
            return null;
        }

        if (filter_var($this->path, FILTER_VALIDATE_URL)) {
            return $this->path;
        }

        return asset(Storage::url($this->path));
    }

    /**
     * Scope pour rechercher par nom ou désignation.
     */
    public function scopeSearch($query, ?string $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%")
                ->orWhere('designation', 'like', "%{$search}%");
        });
    }

    /**
     * Scope pour filtrer les images par salle.
     */
    public function scopeForSalle($query, ?int $salleId)
    {
        if (empty($salleId)) {
            return $query;
        }

        return $query->where('salle_id', $salleId);
    }
}
