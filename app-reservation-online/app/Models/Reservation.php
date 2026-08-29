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
        'creer_par',
        'nom_client',
        'telephone',
    ];

    protected $casts = [
        'date_heure_debut' => 'datetime',
        'date_heure_fin' => 'datetime',
        'terminee_at' => 'datetime',
        'nombre_personnes' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creer_par');
    }

    public function equipements(): BelongsToMany
    {
        return $this->belongsToMany(Equipement::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Nom effectif du client (soit l'utilisateur inscrit, soit le client externe).
     */
    public function getNomAfficheAttribute(): string
    {
        if ($this->user) {
            return $this->user->nom;
        }

        return $this->nom_client ?? 'Client inconnu';
    }

    /**
     * Téléphone effectif du client.
     */
    public function getTelephoneAfficheAttribute(): ?string
    {
        if ($this->user && !empty($this->user->telephone)) {
            return $this->user->telephone;
        }

        return $this->telephone;
    }

    /**
     * Scope pour filtrer par statut (en_attente, confirmee, rejetee, terminee).
     */
    public function scopeStatus($query, ?string $status)
    {
        if (empty($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Scope pour filtrer par salle.
     */
    public function scopeForSalle($query, ?int $salleId)
    {
        if (empty($salleId)) {
            return $query;
        }

        return $query->where('salle_id', $salleId);
    }

    /**
     * Scope pour filtrer par utilisateur inscrit.
     */
    public function scopeForUser($query, ?int $userId)
    {
        if (empty($userId)) {
            return $query;
        }

        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour rechercher par nom de client, téléphone, ou nom de salle.
     */
    public function scopeSearch($query, ?string $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('nom_client', 'like', "%{$search}%")
                ->orWhere('telephone', 'like', "%{$search}%")
                ->orWhereHas('user', function ($u) use ($search) {
                    $u->where('nom', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('telephone', 'like', "%{$search}%");
                })
                ->orWhereHas('salle', function ($s) use ($search) {
                    $s->where('nom', 'like', "%{$search}%")
                        ->orWhere('localisation', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Scope pour vérifier les conflits de créneaux sur une salle donnée.
     */
    public function scopeOverlapping($query, int $salleId, string $debut, string $fin, ?int $excludeId = null)
    {
        return $query->where('salle_id', $salleId)
            ->whereIn('status', ['en_attente', 'confirmee'])
            ->where(function ($q) use ($debut, $fin) {
                $q->where(function ($sub) use ($debut, $fin) {
                    $sub->where('date_heure_debut', '<', $fin)
                        ->where('date_heure_fin', '>', $debut);
                });
            })
            ->when($excludeId, function ($q) use ($excludeId) {
                $q->where('id', '!=', $excludeId);
            });
    }
}
