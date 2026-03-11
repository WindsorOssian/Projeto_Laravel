<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use App\Models\User;

class VendaModel extends BaseModel
{

    protected $table = 'tb_venda';

    protected $fillable = [

        'data',
        'id_cliente',
        'id_vendedor',
        'id_admin',
        'removido',

    ];

    protected $casts = [
        'data' => 'datetime',
        'removido' => 'boolean',
    ];


    public function cliente(): BelongsTo
    {
        return $this->belongsTo(ClienteModel::class, 'id_cliente');
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(VendedorModel::class, 'id_vendedor', 'id_vendedor');
    }

    // Tem muitos
    public function itens(): HasMany
    {
        return $this->hasMany(ItemModel::class, 'id_venda');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_admin');
    }
}
