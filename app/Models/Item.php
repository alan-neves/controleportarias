<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'origem',
        'destino',
        'codpes',
        'nome',
        'tipo_documento',
        'documento',
        'patrimonio',
        'numero_serie',
        'observacao',
    ];
}
