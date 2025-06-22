<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnexoN3 extends Model
{
    protected $table = 'anexo_n3';
    protected $primaryKey = 'nro_an3';
    protected $fillable = ['id_natural', 'id_adulto', 'sexo'];

    public function persona()
    {
        return $this->belongsTo(PersonaNatural::class, 'id_natural');
    }
    public function personaNatural()
    {
        return $this->belongsTo(PersonaNatural::class, 'id_natural', 'id_natural');
    }
    public function adultoMayor()
    {
        // Asumiendo que id_adulto es la FK en anexo_n3 que referencia a id_adulto en adulto_mayor
        return $this->belongsTo(AdultoMayor::class, 'id_adulto', 'id_adulto');
    }
}
