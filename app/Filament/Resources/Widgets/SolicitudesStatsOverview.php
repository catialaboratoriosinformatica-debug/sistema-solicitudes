<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Solicitude; // Importa tu modelo Solicitud
use App\Models\Equipo;
use App\Models\SolicitudFecha;
use App\Models\Computadora;
use App\Models\Profesore;
use App\Models\Sala;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SolicitudesStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
       // Contar el total de solicitudes
        $totalSolicitudes = Solicitude::count();

        $totalSinEntregar = SolicitudFecha::where('status', false)->count(); 
        if ($totalSinEntregar > 0) {
            $cacheKey = $this->getCacheKey();
            $lastNotificationTime = Cache::get($cacheKey);

            // Define el intervalo de tiempo (3 horas)
            $intervaloEnHoras = 1;
            $tiempoMinimoEntreNotificaciones = Carbon::now()->subHours($intervaloEnHoras);

            // Si no se ha mostrado antes O si ya pasaron las 3 horas desde la última vez
            if (is_null($lastNotificationTime) || Carbon::parse($lastNotificationTime)->lessThanOrEqualTo($tiempoMinimoEntreNotificaciones)) {
                Notification::make()
                    ->title('¡Atención: Solicitudes Pendientes!')
                    ->body("Tienes {$totalSinEntregar} solicitudes de equipos sin entregar.")
                    ->danger() // Muestra la notificación en color rojo (peligro)
                    ->duration(5000) // La notificación se mostrará por 5 segundos
                    ->send();

                // Almacena el timestamp actual en caché
                Cache::put($cacheKey, Carbon::now());
            }
        }

        $description = 'Pendientes por entregar';
        $descriptionIcon = 'heroicon-o-clock';
        $color = 'danger';

        if ($totalSinEntregar === 0) {
            $description = 'Todo al día';
            $descriptionIcon = 'heroicon-o-check-circle';
            $color = 'success';
        }

        $totalEquipos = Equipo::sum('cantidad');

        $totalComputadoras = Computadora::sum('cantidad');

        // Contar solicitudes de equipos (donde equipo_id no es nulo)
        //$solicitudesDeEquipos = Solicitudes::whereNotNull('equipos_id')->count();

        // Contar solicitudes de computadoras (donde computadoras_id no es nulo)
        //$solicitudesDeComputadoras = Solicitudes::whereNotNull('computadoras_id')->count();

        // Contar solicitudes 'Sin Entregar'
        //$solicitudesSinEntregar = Solicitudes::where('status', false)->whereDoesntHave('devoluciones')->count();

        // Contar solicitudes 'Entregadas'
        //$solicitudesEntregadas = Solicitudes::where('status', true)->whereDoesntHave('devoluciones')->count();

        // Contar solicitudes 'Devueltas'
        $devueltas = SolicitudFecha::has('devolucion')->count();

        $salasDisponibles = Sala::where('disponibilidad', 1)->count();


        return [
            Stat::make('Solicitudes de Equipos', $totalSolicitudes)
                ->description('Solicitudes Registradas')
                ->url(route('filament.admin.resources.solicitudes.index'))
                ->descriptionIcon('heroicon-o-document-text')
                ->color('info'),
            
            Stat::make('Solicitudes Sin Entregar', $totalSinEntregar)
                ->description($description)
                ->descriptionIcon($descriptionIcon)
                ->color($color)
                ->url(route('filament.admin.resources.solicitudes.index')),
            
            Stat::make('Solicitudes de Equipos Devueltas', $devueltas)
                ->description('Equipos que Fueron Devueltos')
                ->url(route('filament.admin.resources.devoluciones.index'))
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('warning'),

            Stat::make('Salas Operativas', $salasDisponibles)
                ->description('Salas Disponibles')
                ->descriptionIcon('heroicon-o-check-circle') // Icono para disponible
                ->color('success') // Color verde para disponible
                ->url(route('filament.admin.resources.salas.index')),

            Stat::make('Video Beam Existentes', $totalEquipos)
                ->description('Video Beam en el Inventario')
                ->url(route('filament.admin.resources.equipos.index'))
                ->descriptionIcon('heroicon-o-video-camera')
                ->color('secondary'),

            Stat::make('Computadoras Existentes', $totalComputadoras)
                ->description('Computadoras en el Inventario')
                ->url(route('filament.admin.resources.computadoras.index'))
                ->descriptionIcon('heroicon-o-computer-desktop')
                ->color('secondary'),

        ];
    }

    protected function getColumns(): int // Para la página ListRecords
{
    return 3;
}

protected function getCacheKey(): string
{
   if (Auth::check()) {
            return 'last_solicitudes_sin_entregar_notification_' . Auth::id();
        }
        return 'last_solicitudes_sin_entregar_notification_guest';
}
}

