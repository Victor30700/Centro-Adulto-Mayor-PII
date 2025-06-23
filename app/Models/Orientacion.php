<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orientacion extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'orientacion'; // Cambiado a 'orientacion'

    // Define la clave primaria si no es 'id' (en este caso es 'cod_or')
    protected $primaryKey = 'cod_or';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fecha_ingreso',
        'id_adulto',
        'tipo_orientacion',
        'motivo_orientacion',
        'resultado_obtenido',
        'id_usuario',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_ingreso' => 'date',
    ];

    /**
     * Get the AdultoMayor that owns the Orientacion.
     * Relación con el modelo AdultoMayor (como en tu ejemplo 'adulto()')
     */
    public function adulto()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto', 'id_adulto');
    }
    public function usuario() // Nombre de la relación para el usuario
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
