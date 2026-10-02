<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Equipo
 * 
 * @property int $id
 * @property string $equipo
 * @property string|null $complemento
 * @property int $cantidad
 * 
 * @property Collection|Solicitude[] $solicitudes
 *
 * @package App\Models
 */
class Equipo extends Model
{
	protected $table = 'equipos';
	

	protected $casts = [
		'cantidad' => 'int'
	];

	protected $fillable = [
		'equipo',
		'complemento',
		'cantidad'
	];

	/*public function solicitudes()
	{
		return $this->hasMany(Solicitude::class, 'equipos_id');
	}*/
}
