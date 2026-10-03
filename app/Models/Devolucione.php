<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Devolucione
 * 
 * @property int $id
 * @property Carbon $fecha_devolucion
 * @property string|null $observacion
 * @property int $solicitud_fechas_id
 * @property int|null $status
 * 
 * @property Solicitude $solicitude
 *
 * @package App\Models
 */
class Devolucione extends Model
{
	protected $table = 'devoluciones';
    
    protected $casts = [
        'fecha_devolucion' => 'datetime',
        // Usamos la nueva clave foránea
        'solicitud_fechas_id' => 'int', 
        'status' => 'int'
    ];

    protected $fillable = [
        'fecha_devolucion',
        'observacion',
        // Clave corregida
        'solicitud_fechas_id', 
        'status'
    ];

    /**
     * Una devolución pertenece a una SolicitudFecha específica (el día de uso).
     */
    public function solicitudFecha(): BelongsTo
    {
        // Apuntamos a la clase SolicitudFecha y a la clave solicitud_fechas_id
        return $this->belongsTo(SolicitudFecha::class, 'solicitud_fechas_id');
    }
}
