<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class ProdutoModel extends BaseModel
{

    protected $table = 'tb_produto';

    protected $fillable = [

        'nome',
        'preco',
        'id_admin',
        'removido'
        
    ];

    protected $casts = [
        'preco' => 'float',
        'removido' => 'boolean',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_admin');
    }
}
