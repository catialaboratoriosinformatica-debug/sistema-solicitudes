<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Carrera;

class CarreraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carreras = [
            'ADMINISTRACION',
            'CONTADURIA',
            'EDUCACION INICIAL',
            'EDUCACION INTEGRAL',
            'ELECTRONICA',
            'INFORMATICA',
        ];

        foreach ($carreras as $nombreCarrera) {
            Carrera::firstOrCreate(
                ['carrera' => $nombreCarrera]
            );
        }
    }
}
