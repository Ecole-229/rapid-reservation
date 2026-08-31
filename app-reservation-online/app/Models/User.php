<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['nom', 'email', 'mot_de_passe', 'role', 'telephone'])]
#[Hidden(['mot_de_passe', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * Spécifie la colonne utilisée pour le mot de passe dans la base de données.
     */
    public function getAuthPasswordName(): string
    {
        return 'mot_de_passe';
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'user_id');
    }

    public function reservationsCreees(): HasMany
    {
        return $this->hasMany(Reservation::class, 'cree_par_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isResponsable(): bool
    {
        return $this->role === 'responsable';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Scope pour filtrer par rôle(s).
     */
    public function scopeRole($query, string|array $roles)
    {
        if (is_array($roles)) {
            return $query->whereIn('role', $roles);
        }

        if (str_contains($roles, ',')) {
            $roleArray = array_map('trim', explode(',', $roles));
            return $query->whereIn('role', $roleArray);
        }

        return $query->where('role', $roles);
    }

    /**
     * Scope pour la recherche par nom, email ou téléphone.
     */
    public function scopeSearch($query, ?string $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('telephone', 'like', "%{$search}%");
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mot_de_passe' => 'hashed',
        ];
    }
}

