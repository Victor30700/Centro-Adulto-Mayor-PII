<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Croquis extends Model
{
    protected $table = 'croquis';
    protected $primaryKey = 'id_referencia';

    protected $fillable = [
        'nombre_denunciante',
        'apellidos_denunciante',
        'ci_denunciante',
        'id_adulto',
    ];

    public function adulto()
    {
        return $this->belongsTo(AdultoMayor::class, 'id_adulto');
    }
}
