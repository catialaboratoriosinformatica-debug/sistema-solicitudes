<?php

namespace App\Filament\Resources\Solicitudes\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Solicitude; // Importa tu modelo Solicitud
use App\Models\Profesores;
use App\Models\Carrera;
use Illuminate\Support\Facades\DB;

class SolicitudesPorCarreraChart extends ChartWidget
{
    protected ?string $heading = 'Solicitudes de Equipos por Carrera';

    protected function getData(): array
    {
        // Usamos DB::table para mantener la consistencia con el widget de laboratorios
        $stats = DB::table('solicitudes')
            ->join('profesores', 'solicitudes.profesores_id', '=', 'profesores.id')
            ->join('carrera', 'profesores.carrera_id', '=', 'carrera.id')
            ->select('carrera.carrera as carrera', DB::raw('count(solicitudes.id) as total'))
            ->groupBy('carrera.carrera')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad de Solicitudes',
                    'data' => $stats->pluck('total')->toArray(),
                    // Aplicamos la misma paleta de colores que el widget de laboratorios
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
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false, // Ocultamos la leyenda para que se vea más limpio como en tu imagen
                ],
            ],
        ];
    }
}
