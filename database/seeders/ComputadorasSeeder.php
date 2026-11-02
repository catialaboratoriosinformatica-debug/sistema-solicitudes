<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Computadora;

class ComputadorasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $computadoras = [
            'Laptop LENOVO',
            'Laptop VIT',
            'Laptop HP',
        ];

        $complementoComun = 'Cargador';

        foreach ($computadoras as $nombreComputadora) {
            Computadora::firstOrCreate(
                ['computadora' => $nombreComputadora],
                [
                    'complemento' => $complementoComun,
                    'cantidad' => 1, // Puedes ajustar la cantidad inicial si lo requieres
                ]
            );
        }
    }
}
