<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteModel extends BaseModel
{

    protected $table = 'tb_cliente';

    // Foi necessario para corrigir o erro do cadastro
    protected $primaryKey = 'id';

    protected $fillable = [

        'id',
        'nome',
        'email',
        'id_endereco',
        'id_admin',
        'removido'

    ];

    public function endereco(): BelongsTo
    {
        return $this->belongsTo(EnderecoModel::class, 'id_endereco');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_admin');
    }
    // Coloquei por tem na controller e não tem na model
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }
}
