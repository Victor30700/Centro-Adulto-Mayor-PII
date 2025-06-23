<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoFamiliar extends Model
{
    protected $table = 'grupo_familiar';
    protected $primaryKey = 'id_familiar';

    protected $fillable = [
        'apellido_paterno',
        'apellido_materno',
        'nombres',
        'parentesco',
        'edad',
        'ocupacion',
        'direccion',
        'telefono',
        'id_adulto',
    ];

    public function adulto()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto');
    }
}
