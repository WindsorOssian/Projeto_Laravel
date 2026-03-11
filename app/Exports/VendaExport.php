<?php

namespace App\Exports;

use App\Models\VendaModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class VendaExport implements FromCollection, ShouldAutoSize
{
    public function __construct(mixed $idVenda)
    {
        $this->idVenda = $idVenda;
    }

    public function collection()
    {
        $isAdmin = auth()->user()->hasRole('admin');

        $headers = [
            'Cliente',
            'Vendedor',
            'Data da Venda',
            'Produto',
            'Quantidade',
            'Valor Unitário',
            'Total Item',
            'Total da Venda',
            'Comissão %',
            'Valor Comissão'
        ];

        if ($isAdmin) {
            $headers[] = 'Lucro Empresa';
        }

        $linhas[] = $headers;

        $venda = VendaModel::with([
            'itens.produto',
            'cliente',
            'vendedor.user',
            'admin'
        ])
        ->where('id', $this->idVenda)
        ->first();

        if (!$venda) {
            return collect($linhas);
        }

        $totalVenda = $venda->itens->sum(fn ($item) => $item->quantidade * $item->valor);

        $comissaoPercent = $venda->vendedor?->comissao ?? 0;
        $valorComissao = ($totalVenda * $comissaoPercent) / 100;
        $lucroEmpresa = $totalVenda - $valorComissao;

        $vendedorNome =
            $venda->vendedor?->user?->name
            ?? $venda->admin?->name
            ?? '—';

        foreach ($venda->itens as $item) {

            $totalItem = $item->quantidade * $item->valor;

            $linha = [
                $venda->cliente->nome,
                $vendedorNome,
                $venda->data->format('d/m/Y H:i'),
                $item->produto->nome,
                $item->quantidade,
                number_format($item->valor, 2, ',', '.'),
                number_format($totalItem, 2, ',', '.'),
                number_format($totalVenda, 2, ',', '.'),
                $comissaoPercent . '%',
                number_format($valorComissao, 2, ',', '.')
            ];

            if ($isAdmin) {
                $linha[] = number_format($lucroEmpresa, 2, ',', '.');
            }

            $linhas[] = $linha;
        }

        return collect($linhas);
    }
}