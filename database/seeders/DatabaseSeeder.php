<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Cria o usuário administrador padrão
        User::firstOrCreate(
            ['email' => 'admin@barberflow.com'],
            [
                'name' => 'Admin BarberFlow',
                'password' => Hash::make('password'),
            ]
        );

        // Adiciona configurações iniciais
        DB::table('settings')->insertOrIgnore(['key' => 'company_name', 'value' => 'Minha Barbearia']);
    }
}
