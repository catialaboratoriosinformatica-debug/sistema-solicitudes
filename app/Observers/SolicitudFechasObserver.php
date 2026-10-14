<?php

namespace App\Observers;

use App\Models\SolicitudFecha;
use App\Models\Solicitude;
use App\Models\Equipo;
use App\Models\Computadora;
use Illuminate\Support\Facades\Log; // Para depuración

class SolicitudFechasObserver
{
    /**
     * Handle the SolicitudFecha "updated" event.
     * Se ejecuta cuando el estado de la línea de tiempo de la solicitud cambia.
     */
    public function updated(SolicitudFecha $solicitudFecha): void
    {
        // 1. Verificación de Cambio de Estado
        if (!$solicitudFecha->isDirty('status')) {
            return; 
        }
        
        // ¡CRÍTICO!: Usamos true (1) para ENTREGADO, dado que 'status' es booleano.
        $ENTREGADO = true; 
        
        $estadoNuevo = $solicitudFecha->status;
        $estadoAnterior = $solicitudFecha->getOriginal('status');

        // Lógica de Disminución de Stock: Transición a ENTREGADO
        // Verifica si el nuevo estado es ENTREGADO y el estado anterior NO lo era.
        if ($estadoNuevo === $ENTREGADO && $estadoAnterior !== $ENTREGADO) {
            
            Log::info("OBSERVER: Transición detectada: ({$estadoAnterior}) -> ENTREGADO ({$estadoNuevo}).");

            // 2. Obtener Solicitud Padre y Cantidad
            // Usamos la relación 'solicitude()' que definiste
            $solicitudPadre = $solicitudFecha->solicitude; 
            
            if (!$solicitudPadre) {
                Log::error("OBSERVER ERROR: Solicitud Padre no encontrada para SolicitudFecha ID: {$solicitudFecha->id}.");
                return;
            }

            $cantidad = (int) $solicitudPadre->cantidad;

            // 3. Lógica de Disminución de Stock para EQUIPOS
            if ($solicitudPadre->equipos_id && $cantidad > 0) {
                $equipo = Equipo::find($solicitudPadre->equipos_id);
                
                if ($equipo) {
                    // Resta la cantidad solicitada del stock actual del equipo.
                    $equipo->cantidad = max(0, $equipo->cantidad - $cantidad);
                    $equipo->save();
                    Log::info("INVENTARIO EQUIPOS OK: Stock de Equipo (ID: {$equipo->id}) disminuido en {$cantidad}. Nuevo stock: {$equipo->cantidad}");
                } else {
                    Log::warning("INVENTARIO EQUIPOS FALLO: Equipo con ID {$solicitudPadre->equipos_id} no encontrado en la tabla Equipos.");
                }
            }
            // NOTA: La lógica de Computadoras y Devolución se añade en el siguiente paso.
            if ($solicitudPadre->computadoras_id && $cantidad > 0) {
                $computadora = Computadora::find($solicitudPadre->computadoras_id);
                
                if ($computadora) {
                    // Resta la cantidad solicitada del stock actual de la computadora.
                    $computadora->cantidad = max(0, $computadora->cantidad - $cantidad);
                    $computadora->save();
                    Log::info("INVENTARIO COMPUTADORAS OK: Stock de Computadora (ID: {$computadora->id}) disminuido en {$cantidad}. Nuevo stock: {$computadora->cantidad}");
                } else {
                    Log::warning("INVENTARIO COMPUTADORAS FALLO: Computadora con ID {$solicitudPadre->computadoras_id} no encontrada en la tabla Computadoras.");
                }
            }
        } else {
            Log::info("OBSERVER: Cambio de estado sin impacto en el inventario (Actual: {$estadoNuevo}, Anterior: {$estadoAnterior}).");
        }
    }
}