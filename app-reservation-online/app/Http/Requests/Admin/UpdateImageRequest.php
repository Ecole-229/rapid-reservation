<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateImageRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour la mise à jour d'une image.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => ['sometimes', 'required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'salle_id' => ['sometimes', 'required', 'integer', 'exists:salles,id'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif,svg', 'max:10240'],
            'path' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Messages personnalisés pour les erreurs de validation.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du média/image est obligatoire.',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'designation.max' => 'La désignation ne doit pas dépasser 255 caractères.',
            'salle_id.required' => 'La salle associée est obligatoire.',
            'salle_id.integer' => 'L\'identifiant de la salle doit être un nombre entier.',
            'salle_id.exists' => 'La salle sélectionnée est introuvable.',
            'image.file' => 'Le fichier uploadé doit être un fichier valide.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être de type : jpeg, png, jpg, webp, gif ou svg.',
            'image.max' => 'La taille maximale de l\'image ne doit pas dépasser 10 Mo.',
            'path.max' => 'Le chemin ou l\'URL de l\'image ne doit pas dépasser 1000 caractères.',
        ];
    }
}
