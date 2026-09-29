<?php
/**
 * Created by Reliese Model.
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
/**
 * Class Carrera
 * 
 * @property int $id
 * @property string $carrera
 * 
 * @property Collection|Profesore[] $profesores
 *
 * @package App\Models
 */
class Carrera extends Model
{
	protected $table = 'carrera';
	/*public $timestamps = false;*/

	protected $fillable = [
		'carrera'
	];

	public function profesores()
	{
		return $this->hasMany(Profesore::class);
	}
}
