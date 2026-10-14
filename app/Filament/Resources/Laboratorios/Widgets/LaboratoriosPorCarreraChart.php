<?php

namespace App\Filament\Resources\Laboratorios\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class LaboratoriosPorCarreraChart extends ChartWidget
{
    protected ?string $heading = 'Uso de Laboratorios por Carrera';

    protected function getData(): array
    {
        // Consulta para agrupar laboratorios por el nombre de la carrera
        $stats = DB::table('laboratorios')
            ->join('profesores', 'laboratorios.profesores_id', '=', 'profesores.id')
            ->join('carrera', 'profesores.carrera_id', '=', 'carrera.id')
            ->select('carrera.carrera as carrera', DB::raw('count(laboratorios.id) as total'))
            ->groupBy('carrera.carrera')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Solicitudes',
                    'data' => $stats->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#36A2EB', // Azul
                        '#FF6384', // Rosado
                        '#FFCE56', // Amarillo
                        '#4BC0C0', // Verde menta
                        '#9966FF', // Morado
                        '#FF9F40'  // Naranja
                    ],
                ],
            ],
            'labels' => $stats->pluck('carrera')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Puedes usar 'pie' o 'doughnut' si prefieres
    }
}