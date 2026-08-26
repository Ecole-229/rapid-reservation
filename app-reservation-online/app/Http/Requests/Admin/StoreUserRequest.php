<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'nom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', 'in:admin,responsable,user'],
            'password' => ['nullable', 'string', 'min:6'],
            'mot_de_passe' => ['nullable', 'string', 'min:6', 'required_without:password'],
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail doit être valide.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'telephone.max' => 'Le numéro de téléphone ne doit pas dépasser 20 caractères.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle sélectionné est invalide (choix possibles : admin, responsable, user).',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'mot_de_passe.required_without' => 'Le mot de passe est obligatoire.',
        ];
    }
}
