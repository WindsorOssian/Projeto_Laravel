<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendedorModel extends BaseModel
{

    protected $table = 'tb_vendedor';
    // Foi necessario para corrigir o erro do cadastro
    protected $primaryKey = 'id_vendedor';
    //
    protected $fillable = [

        'id_vendedor',
        'comissao',
        'cpf',
        'observacoes',
        'id_endereco',
        'id_admin',
        'removido'

    ];

    // Para o endereço
    public function endereco(): BelongsTo
    {
        return $this->belongsTo(EnderecoModel::class, 'id_endereco', 'id');
    }

    // Para o id vendedor
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_vendedor', 'id');
    }

    // Para o id admin mas não vamos usar
    // public function admin(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'id_admin', 'id');
    // }
}
