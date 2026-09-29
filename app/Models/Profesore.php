<?php
/**
 * Created by Reliese Model.
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
/**
 * Class Profesore
 * 
 * @property int $id
 * @property string $nombre
 * @property string|null $apellido
 * @property string $cargo
 * @property int $carrera_id
 * 
 * @property Carrera $carrera
 * @property Collection|Laboratorio[] $laboratorios
 * @property Collection|Solicitude[] $solicitudes
 *
 * @package App\Models
 */
class Profesore extends Model
{
	protected $table = 'profesores';
	/*public $timestamps = false;*/

	protected $casts = [
		'carrera_id' => 'int'
	];

	protected $fillable = [
		'nombre',
		'apellido',
		'cargo',
		'carrera_id'
	];

	public function carrera()
	{
		return $this->belongsTo(Carrera::class);
	}

	public function laboratorios()
	{
		return $this->hasMany(Laboratorio::class, 'profesores_id');
	}

	public function solicitude()
	{
		return $this->hasMany(Solicitude::class, 'profesores_id');
	}
}
