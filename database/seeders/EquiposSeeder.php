<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Equipo;

class EquiposSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipos = [
            'Video Beam BLANCO',
            'Video Beam NEGRO',
            'Video Beam BEIGE',
        ];

        $complementoComun = 'Cable VGA y Cable de Power';

        foreach ($equipos as $nombreEquipo) {
            Equipo::firstOrCreate(
                ['equipo' => $nombreEquipo],
                [
                    'complemento' => $complementoComun,
                    'cantidad' => 1, // Puedes ajustar la cantidad predeterminada si lo deseas
                ]
            );
        }
    }
}
