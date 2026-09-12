<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSalleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => ['sometimes', 'required', 'string', 'max:255'],
            'capacite' => ['sometimes', 'required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'string', 'in:disponible,indisponible'],
            'localisation' => ['sometimes', 'required', 'string', 'max:255'],
            'prix' => ['sometimes', 'required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de la salle est obligatoire.',
            'nom.max' => 'Le nom de la salle ne doit pas dépasser 255 caractères.',
            'capacite.required' => 'La capacité de la salle est obligatoire.',
            'capacite.integer' => 'La capacité doit être un nombre entier.',
            'capacite.min' => 'La capacité doit être d\'au moins 1 personne.',
            'status.in' => 'Le statut doit être "disponible" ou "indisponible".',
            'localisation.required' => 'La localisation de la salle est obligatoire.',
            'prix.required' => 'Le prix de la salle est obligatoire.',
            'prix.numeric' => 'Le prix doit être une valeur numérique valide.',
            'prix.min' => 'Le prix ne peut pas être négatif.',
        ];
    }
}
