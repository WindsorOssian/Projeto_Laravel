<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnderecoModel extends Model
{

    protected $table = 'tb_endereco';

    protected $fillable = [

        'cep',
        'rua',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'removido'

    ];

}
