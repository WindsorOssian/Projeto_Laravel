<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class ItemModel extends Model
{
  
    protected $table = 'tb_item';

    protected $fillable = [

        'id_venda',
        'id_produto',
        'quantidade', // FALTAVA para fazer um controle geral de todos os itens
        'valor',
        'id_admin',
        'removido'
        
    ];

    public function venda(): BelongsTo
    {
        return $this->belongsTo(VendaModel::class, 'id_venda');
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(ProdutoModel::class, 'id_produto');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_admin');
    }
}
