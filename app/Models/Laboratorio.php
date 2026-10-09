<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Laboratorio
 * 
 * @property int $id
 * @property int $salas_id
 * @property int $profesores_id
 * @property Carbon $fecha
 * @property Carbon $hora_inicio
 * @property Carbon $hora_final
 * @property int|null $disponibilidad
 * @property string|null $observacion
 * 
 * @property Profesore $profesore
 * @property Sala $sala
 *
 * @package App\Models
 */
class Laboratorio extends Model
{
	protected $table = 'laboratorios';
	public $timestamps = false;

	protected $casts = [
		'salas_id' => 'int',
		'profesores_id' => 'int',
		'fecha' => 'datetime',
		'hora_inicio' => 'string', // Guardado como formato TIME 'H:i:s'
        'hora_final' => 'string',  // Guardado como formato TIME 'H:i:s'
        'disponibilidad' => 'boolean', // Ajustado a boolean para PostgreSQL
	];

	protected $fillable = [
		'salas_id',
		'profesores_id',
		'fecha',
		'hora_inicio',
		'hora_final',
		'disponibilidad',
		'observacion'
	];

	public function profesore()
	{
		return $this->belongsTo(Profesore::class, 'profesores_id');
	}

	public function sala()
	{
		return $this->belongsTo(Sala::class, 'salas_id');
	}
}
