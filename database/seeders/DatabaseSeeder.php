<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Rubens Coelho',
            'email' => 'rubens@excelencebrindes.com.br',
            'password' => bcrypt('123456'),
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Administrador',
            'email' => 'adm@excelencebrindes.com.br',
            'password' => bcrypt('123456'),
        ]);
    }
}
