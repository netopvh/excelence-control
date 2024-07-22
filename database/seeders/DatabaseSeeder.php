<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\ProductionSector;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $roleSuperAdmin = Role::create([
            'name' => 'superadmin',
        ]);

        $roleAdmin = Role::create([
            'name' => 'admin',
        ]);

        $roleFinanceiro = Role::create([
            'name' => 'financeiro',
        ]);

        $roleVendas = Role::create([
            'name' => 'vendas',
        ]);

        $roleDesign = Role::create([
            'name' => 'design',
        ]);

        $roleProducao = Role::create([
            'name' => 'producao',
        ]);

        $setores = [
            [
                'name' => 'Sublimação',
            ],
            [
                'name' => 'Resina',
            ],
            [
                'name' => 'Transfer',
            ],
            [
                'name' => 'Calandra',
            ],
            [
                'name' => 'Terceirizado',
            ],
            [
                'name' => 'Serigrafia',
            ],
            [
                'name' => 'Copos',
            ],
            [
                'name' => 'Laser',
            ],
            [
                'name' => 'Canetas',
            ],
            [
                'name' => 'Recorte',
            ]
        ];

        foreach ($setores as $setor) {
            ProductionSector::create($setor);
        }

        $admins = [
            [
                'name' => 'Robson Azevedo',
                'email' => 'robson@excelencebrindes.com.br',
                'username' => 'robson',
                'password' => bcrypt('123456'),
            ],
            [
                'name' => 'Dayane Azevedo',
                'email' => 'dayane@excelencebrindes.com.br',
                'username' => 'dayane',
                'password' => bcrypt('123456'),
            ],
            [
                'name' => 'Administrador',
                'email' => 'adm@excelencebrindes.com.br',
                'username' => 'administrador',
                'password' => bcrypt('123456'),
            ]
        ];

        $produtores = [
            [
                'name' => 'David',
                'email' => 'david@excelencebrindes.com.br',
                'username' => 'david',
                'password' => bcrypt('123456'),
            ],
            [
                'name' => 'Elyton',
                'email' => 'elyton@excelencebrindes.com.br',
                'username' => 'elyton',
                'password' => bcrypt('123456'),

            ],
            [
                'name' => 'Scarleth',
                'email' => 'scarleth@excelencebrindes.com.br',
                'username' => 'scarleth',
                'password' => bcrypt('123456'),
            ]
        ];

        $vendas = [
            [
                'name' => 'Amanda',
                'email' => 'amanda@excelencebrindes.com.br',
                'username' => 'amanda',
                'password' => bcrypt('123456'),
            ]
        ];

        $designers = [
            [
                'name' => 'Cristiano',
                'email' => 'cristiano@excelencebrindes.com.br',
                'username' => 'cristiano',
                'password' => bcrypt('123456'),
            ],
            [
                'name' => 'Andy',
                'email' => 'andy@excelencebrindes.com.br',
                'username' => 'andy',
                'password' => bcrypt('123456'),
            ]
        ];

        foreach ($admins as $admin) {
            $user = \App\Models\User::factory()->create($admin);

            $user->assignRole($roleAdmin, $roleDesign);
        }

        foreach ($produtores as $produtor) {
            $user = \App\Models\User::factory()->create($produtor);

            $user->assignRole($roleProducao);
        }

        foreach ($vendas as $venda) {
            $user = \App\Models\User::factory()->create($venda);

            $user->assignRole($roleVendas);
        }

        foreach ($designers as $designer) {
            $user = \App\Models\User::factory()->create($designer);

            $user->assignRole($roleDesign);
        }

        $joao = \App\Models\User::factory()->create([
            'name' => 'João',
            'email' => 'joao@excelencebrindes.com.br',
            'username' => 'joao',
            'password' => bcrypt('123456'),
        ]);

        $joao->assignRole($roleFinanceiro);

        $rubens = \App\Models\User::factory()->create([
            'name' => 'Rubens Coelho',
            'email' => 'rubens@excelencebrindes.com.br',
            'username' => 'rubens',
            'password' => bcrypt('021208'),
        ]);

        $rubens->assignRole($roleSuperAdmin, $roleAdmin, $roleVendas, $roleDesign, $roleProducao, $roleFinanceiro);
    }
}
