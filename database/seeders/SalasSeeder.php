<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sala;

class SalasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salas = [
            'SALA 1A',
            'SALA 1B',
            'SALA 2A',
            'SALA 2B',
            'SALA 3',
            'SALA 4',
            'SALA 5A',
            'SALA 5B',
            'SALA 8',
            'SALA MERCANTIL',
            'SALA VNET',
            'SALA VNET Conferencia',
        ];

        foreach ($salas as $nombreSala) {
            Sala::firstOrCreate(
                ['sala' => $nombreSala],
                ['disponibilidad' => true] // Por defecto se crean disponibles
            );
        }
    }
}
