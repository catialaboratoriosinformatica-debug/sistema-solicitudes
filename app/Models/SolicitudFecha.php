<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class SolicitudFecha
 * 
 * @property int $id
 * @property int $solicitudes_id
 * @property Carbon $fecha
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Solicitude $solicitude
 *
 * @package App\Models
 */
class SolicitudFecha extends Model
{ 

	protected $table = 'solicitud_fechas';

	protected $casts = [
		'solicitudes_id' => 'int',
		'fecha' => 'datetime',
		'status' => 'bool'
	];

	protected $fillable = [
		'solicitudes_id',
		'fecha',
		'status'
	];

	protected static function boot()
    {
        parent::boot();

        // Cuando se intente eliminar un registro de SolicitudFecha, 
        // en lugar de borrar la Devolucion relacionada, 
        // actualizamos su clave foránea a NULL para que el registro de Devolucion se mantenga.
        static::deleting(function (SolicitudFecha $solicitudFecha) {
            $solicitudFecha->devolucion()->update(['solicitud_fechas_id' => null]);
        });
    }

	public function solicitude()
	{
		return $this->belongsTo(Solicitude::class, 'solicitudes_id');
	}

	public function devolucion(): HasOne
    {
        // El modelo Devolucion tiene la clave foránea 'solicitud_fechas_id'
        // Esto es necesario para que el botón de Devolución sepa si ya se registró.
        return $this->hasOne(Devolucione::class, 'solicitud_fechas_id');
    }
}
