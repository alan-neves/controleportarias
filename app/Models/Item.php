<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Item extends Model
{
    public const TIPOS_DOCUMENTO = ['CPF', 'RG', 'RNE/CRNM', 'Passaporte', 'CNH'];
    
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

    protected function patrimonio(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? preg_replace('/\D/', '', $value) : $value,
        );
    }

    public function fotos()
    {
        return $this->hasMany(Foto::class);
    }
}
