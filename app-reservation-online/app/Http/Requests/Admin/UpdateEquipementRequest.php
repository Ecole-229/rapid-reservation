<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipementRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour la mise à jour d'un équipement.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'string', 'in:disponible,indisponible'],
            'stock_total' => ['sometimes', 'required', 'integer', 'min:0'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif,svg', 'max:10240'],
        ];
    }

    /**
     * Messages personnalisés pour les erreurs de validation.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de l\'équipement est obligatoire.',
            'nom.max' => 'Le nom de l\'équipement ne doit pas dépasser 255 caractères.',
            'status.in' => 'Le statut doit être "disponible" ou "indisponible".',
            'stock_total.required' => 'Le stock total est obligatoire.',
            'stock_total.integer' => 'Le stock total doit être un nombre entier.',
            'stock_total.min' => 'Le stock total ne peut pas être inférieur à 0.',
            'image.file' => 'Le fichier uploadé doit être un fichier valide.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être de type : jpeg, png, jpg, webp, gif ou svg.',
            'image.max' => 'La taille maximale de l\'image ne doit pas dépasser 10 Mo.',
        ];
    }
}
