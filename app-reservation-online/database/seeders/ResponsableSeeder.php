<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ResponsableSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'responsablespace@gmail.com'],
            [
                'nom' => 'Responsable Hakim',
                'mot_de_passe' => Hash::make('reservation229'),
                'telephone' => '0197770707',
                'role' => 'responsable',
            ]
        );
    }
}
