<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. AÑADIR ESTA LÍNEA

class Fisioterapia extends Model
{
    use HasFactory, SoftDeletes; // <-- 2. AÑADIR 'SoftDeletes' AQUÍ

    protected $table = 'fisioterapia';
    protected $primaryKey = 'cod_fisio';

    protected $fillable = [
        'id_adulto',
        'id_historia',
        'id_usuario',
        'num_emergencia',
        'enfermedades_actuales',
        'alergias',
        'fecha_programacion',
        'motivo_consulta',
        'solicitud_atencion',
        'equipos',
    ];

    protected $casts = [
        'fecha_programacion' => 'date',
    ];

    /**
     * Relación con AdultoMayor.
     */
    public function adulto()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto', 'id_adulto');
    }

    /**
     * Relación con HistoriaClinica (opcional).
     */
    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class, 'id_historia', 'id_historia');
    }

    /**
     * Relación con User (quien registra).
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}
