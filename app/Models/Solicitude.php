<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Solicitude
 * 
 * @property int $id
 * @property int $profesores_id
 * @property int|null $equipos_id
 * @property int|null $computadoras_id
 * @property int|null $cantidad
 * 
 * @property Equipo|null $equipo
 * @property Computadora|null $computadora
 * @property Profesore $profesore
 * @property Collection|Devolucione[] $devoluciones
 * @property Collection|SolicitudFecha[] $solicitud_fechas
 *
 * @package App\Models
 */
class Solicitude extends Model
{
	protected $table = 'solicitudes';
	

	protected $casts = [
		'profesores_id' => 'int',
		'equipos_id' => 'int',
		'computadoras_id' => 'int',
		'cantidad' => 'int',
	];

	protected $fillable = [
		'profesores_id',
		'equipos_id',
		'computadoras_id',
		'cantidad',
		'observacion'
	];

	public function equipo()
	{
		return $this->belongsTo(Equipo::class, 'equipos_id');
	}

	public function computadora()
	{
		return $this->belongsTo(Computadora::class, 'computadoras_id');
	}

	public function profesore()
	{
		return $this->belongsTo(Profesore::class, 'profesores_id');
	}

	public function devoluciones()
	{
		return $this->hasMany(Devolucione::class, 'solicitudes_id');
	}

	public function fechasSolicitadas()
	{
		return $this->hasMany(SolicitudFecha::class, 'solicitudes_id');
	}
}
