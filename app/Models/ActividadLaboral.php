<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadLaboral extends Model
{
    protected $table = 'actividad_laboral';
    protected $primaryKey = 'id_act_lab';

    protected $fillable = [
        'nombre_actividad',
        'direccion_trabajo',
        'horario',
        'horas_x_dia',
        'rem_men_aprox',
        'telefono_laboral',
        'id_adulto',
    ];

    public function adulto()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto');
    }
}
