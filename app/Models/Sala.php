<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class Sala
 * 
 * @property int $id
 * @property string $sala
 * @property int|null $disponibilidad
 * 
 * @property Collection|Laboratorio[] $laboratorios
 *
 * @package App\Models
 */
class Sala extends Model
{
	protected $table = 'salas';
	public $timestamps = false;

	protected $casts = [
		'disponibilidad' => 'boolean'
	];

	protected $fillable = [
		'sala',
		'disponibilidad'
	];

	public function laboratorios()
	{
		return $this->hasMany(Laboratorio::class, 'salas_id');
	}

	public function getProximaFechaActivaAttribute(): ?Carbon
    {
        // Busca la solicitud más próxima cuya fecha_solicitud sea hoy o en el futuro.
        $proximaSolicitud = $this->laboratorios()
                                ->whereDate('fecha', '>=', Carbon::now()->startOfDay()) // Filtra: solo desde hoy en adelante
                                ->orderBy('fecha') // Ordena para obtener la más temprana
                                ->first(); // Toma la primera (la más próxima)

        return $proximaSolicitud?->fecha; // Devuelve la fecha o null si no encontró ninguna
    }

	public function actualizarDisponibilidad(): void
    {
        // Una sala está OCUPADA (0) si tiene AL MENOS UNA solicitud
        // cuya fecha_solicitud es HOY o en el FUTURO.
        $tienelaboratoriosActivasOFuturas = $this->laboratorios()
                                                ->whereDate('fecha', '>=', Carbon::now()->startOfDay())
                                                ->exists();

        // Determina el nuevo estado de disponibilidad
        $nuevaDisponibilidad = $tienelaboratoriosActivasOFuturas ? false : true; // 0 = Ocupado, 1 = Disponible

        // Si el estado actual es diferente al que debería ser, lo actualiza y guarda en la DB.
        if ($this->disponibilidad !== $nuevaDisponibilidad) {
            $this->disponibilidad = $nuevaDisponibilidad;
            $this->save(); // ¡Guarda el cambio en la base de datos!
        }
    }

	protected static function booted(): void
    {
        static::retrieved(function (Sala $sala) {
            $sala->actualizarDisponibilidad();
        });
    }
}
