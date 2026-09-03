<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour la création d'une réservation.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'salle_id' => ['required', 'integer', 'exists:salles,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'nom_client' => ['required_without:user_id', 'nullable', 'string', 'max:255'],
            'telephone_client' => ['required_without:user_id', 'nullable', 'string', 'max:50'],
            'date_heure_debut' => ['required', 'date'],
            'date_heure_fin' => ['required', 'date', 'after:date_heure_debut'],
            'nombre_personnes' => ['required', 'integer', 'min:1'],
            'status' => ['nullable', 'string', 'in:en_attente,confirmee,rejetee,terminee'],
            'equipements' => ['nullable', 'array'],
            'equipements.*.id' => ['required_with:equipements', 'integer', 'exists:equipements,id'],
            'equipements.*.quantity' => ['required_with:equipements', 'integer', 'min:1'],
        ];
    }

    /**
     * Messages personnalisés pour les erreurs de validation.
     */
    public function messages(): array
    {
        return [
            'salle_id.required' => 'La salle à réserver est obligatoire.',
            'salle_id.exists' => 'La salle sélectionnée n\'existe pas.',
            'user_id.exists' => 'L\'utilisateur sélectionné n\'existe pas.',
            'nom_client.required_without' => 'Le nom du client est obligatoire si aucun compte utilisateur n\'est sélectionné.',
            'telephone_client.required_without' => 'Le numéro de téléphone du client est obligatoire si aucun compte utilisateur n\'est sélectionné.',
            'date_heure_debut.required' => 'La date et heure de début sont obligatoires.',
            'date_heure_debut.date' => 'La date de début n\'est pas un format valide.',
            'date_heure_fin.required' => 'La date et heure de fin sont obligatoires.',
            'date_heure_fin.date' => 'La date de fin n\'est pas un format valide.',
            'date_heure_fin.after' => 'La date et heure de fin doivent être postérieures à la date de début.',
            'nombre_personnes.required' => 'Le nombre de personnes est obligatoire.',
            'nombre_personnes.integer' => 'Le nombre de personnes doit être un entier.',
            'nombre_personnes.min' => 'La réservation doit comporter au moins 1 personne.',
            'status.in' => 'Le statut doit être "en_attente", "confirmee", "rejetee" ou "terminee".',
            'equipements.array' => 'La liste des équipements doit être un tableau.',
            'equipements.*.id.exists' => 'L\'un des équipements sélectionnés est invalide.',
            'equipements.*.quantity.min' => 'La quantité de chaque équipement doit être d\'au moins 1.',
        ];
    }
}
