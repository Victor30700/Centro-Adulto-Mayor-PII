<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeguimientoCaso extends Model
{
    protected $table = 'seguimiento_caso';
    protected $primaryKey = 'id_seg';

    protected $fillable = [
        'nro',
        'fecha',
        'accion_realizada',
        'resultado_obtenido',
        'id_usuario',
        'id_adulto',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function adulto()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function intervencion()
    {
        return $this->hasOne(Intervencion::class, 'id_seg');
    }
}
