<?php

namespace App\Observers;

use App\Models\Devolucione;
use App\Models\Equipo;
use App\Models\Computadora;
use Illuminate\Support\Facades\Log;

class DevolucionesObserver
{
    /**
     * Handle the Devolucion "created" event.
     * Se ejecuta inmediatamente después de que un registro de Devolucion es creado y guardado.
     */
    public function created(Devolucione $devolucion): void
    {
        Log::info("OBSERVER DEVOLUCION: Se ha registrado una nueva devolución (ID: {$devolucion->id}). Iniciando ajuste de inventario.");

        // 1. Obtener la SolicitudFecha
        // Usamos la relación 'solicitudFecha()'
        $solicitudFecha = $devolucion->solicitudFecha; 

        if (!$solicitudFecha) {
            Log::error("OBSERVER ERROR: SolicitudFecha no encontrada para Devolucion ID: {$devolucion->id}. No se puede ajustar el inventario.");
            return;
        }

        // 2. Obtener la Solicitud Padre (Para saber qué equipo y cantidad se prestó)
        // Usamos la relación 'solicitude()' de SolicitudFecha
        $solicitudPadre = $solicitudFecha->solicitude;

        if (!$solicitudPadre) {
            Log::error("OBSERVER ERROR: Solicitud Padre no encontrada para SolicitudFecha ID: {$solicitudFecha->solicitud_fechas_id}. No se puede ajustar el inventario.");
            return;
        }

        $cantidad = (int) $solicitudPadre->cantidad;

        // 3. Lógica de Aumento de Stock para EQUIPOS
        if ($solicitudPadre->equipos_id && $cantidad > 0) {
            $equipo = Equipo::find($solicitudPadre->equipos_id);
            
            if ($equipo) {
                // Suma la cantidad devuelta al stock actual del equipo.
                $equipo->cantidad += $cantidad;
                $equipo->save();
                Log::info("INVENTARIO EQUIPOS OK: Stock de Equipo (ID: {$equipo->id}) AUMENTADO en {$cantidad}. Nuevo stock: {$equipo->cantidad}");
            } else {
                Log::warning("INVENTARIO EQUIPOS FALLO: Equipo con ID {$solicitudPadre->equipos_id} no encontrado para la devolución.");
            }
        }

        // 4. Lógica de Aumento de Stock para COMPUTADORAS
        if ($solicitudPadre->computadoras_id && $cantidad > 0) {
            $computadora = Computadora::find($solicitudPadre->computadoras_id);
            
            if ($computadora) {
                // Suma la cantidad devuelta al stock actual de la computadora.
                $computadora->cantidad += $cantidad;
                $computadora->save();
                Log::info("INVENTARIO COMPUTADORAS OK: Stock de Computadora (ID: {$computadora->id}) AUMENTADO en {$cantidad}. Nuevo stock: {$computadora->cantidad}");
            } else {
                Log::warning("INVENTARIO COMPUTADORAS FALLO: Computadora con ID {$solicitudPadre->computadoras_id} no encontrada para la devolución.");
            }
        }
        
        // 5. Opcional: Marcar la SolicitudFecha como devuelta si tuvieras un campo de estado para eso.
        // Como ya existe un registro en la tabla 'devoluciones', la relación 'hasOne' en SolicitudFecha ya indica que está devuelto.
    }
}