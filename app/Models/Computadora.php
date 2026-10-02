<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Computadora
 * 
 * @property int $id
 * @property string $computadora
 * @property string|null $complemento
 * @property int $cantidad
 * 
 * @property Collection|Solicitude[] $solicitudes
 *
 * @package App\Models
 */
class Computadora extends Model
{
	protected $table = 'computadoras';
	

	protected $casts = [
		'cantidad' => 'int'
	];

	protected $fillable = [
		'computadora',
		'complemento',
		'cantidad'
	];

	/*public function solicitudes()
	{
		return $this->hasMany(Solicitude::class, 'computadoras_id');
	}*/
}
