<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonaJuridica extends Model
{
    use HasFactory;

    protected $table = 'persona_juridica';
    protected $primaryKey = 'id_juridica';
    public $timestamps = false;

    protected $fillable = [
        'id_encargado',
        'nombre_institucion',
        'direccion',
        'telefono_juridica',
        'nombre_funcionario',
    ];

    public function encargado()
    {
        return $this->belongsTo(Encargado::class, 'id_encargado');
    }
}
