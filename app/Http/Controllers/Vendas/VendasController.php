<?php

namespace App\Http\Controllers\Vendas;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Venda\CriarVendaRequest;
use App\Http\Requests\Venda\EditarVendaRequest;
use App\Models\ClienteModel;
use App\Models\ItemModel;
use App\Models\ProdutoModel;
use App\Models\VendaModel;
use App\Models\VendedorModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use League\Uri\Builder;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VendaExport;
use Dompdf\Dompdf;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\URL;

class VendasController extends Controller
{
    public function listar(Request $request)
    {
        // dd(auth()->user()->roleName);
        $filtroVendedor = $request->id_vendedor;
        $filtroCliente  = $request->id_cliente;

        $vendedores = VendedorModel::query()
            ->join('users', 'users.id', '=', 'tb_vendedor.id_vendedor')
            ->with('user')
            ->where('tb_vendedor.removido', false)
            ->where('tb_vendedor.id_admin', auth()->user()->id)
            ->orderBy('users.name', 'asc')
            ->select('tb_vendedor.*')
            ->get();
        $clientes = ClienteModel::query()
            ->join('users', 'users.id', '=', 'tb_cliente.id')
            ->with('user')
            ->where('tb_cliente.removido', false)
            ->where('tb_cliente.id_admin', auth()->user()->id)
            ->orderBy('users.name', 'asc')
            ->select('tb_cliente.*')
            ->get();
        $vendas = VendaModel::query()
            ->with(['cliente', 'vendedor.user', 'admin', 'itens'])
            ->where('removido', false)
            ->when($filtroVendedor, function ($query) use ($filtroVendedor) {
                $query->where('id_vendedor', $filtroVendedor);
            })
            ->when($filtroCliente, function ($query) use ($filtroCliente) {
                $query->where('id_cliente', $filtroCliente);
            })
            ->when(auth()->user()->parent_user_id, function ($query) {
                $query->where('id_vendedor', auth()->user()->id);
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $vendas->getCollection()->transform(function ($venda) {

            $venda->vendedor_nome =
                $venda->vendedor?->user?->name
                ?? $venda->admin?->name
                ?? '—';

            // 🔐 URL assinada para editar
            $venda->edit_url = URL::signedRoute(
                'vendas.persistir',
                ['id' => $venda->id]
            );

            return $venda;
        });

        // dd($vendas->first()->toArray());

        return Inertia::render(
            'vendas/Listar',
            [
                'vendas' => $vendas,
                'vendedores' => $vendedores,
                'clientes' => $clientes,
            ]
        );
    }

    public function persistir($idVenda = null)
    {
        $produtos = ProdutoModel::query()
            ->where('removido', false)
            ->orderBy('nome', 'asc')
            ->get();
        $clientes = ClienteModel::query()
            ->where('removido', false)
            ->orderBy('nome', 'asc')
            ->get();

        $venda = VendaModel::query()->with(['itens.produto'])->where('id', $idVenda)->first();

        if ($venda) {
            $venda->data_formatada = $venda->data?->format('Y-m-d');
        }

        return Inertia::render(
            'vendas/Persistir',
            [
                'clientes' => $clientes,
                'produtos' => $produtos,
                'venda'    => $venda ?? null,
            ]
        );
    }

    // 🔥 CREATE
    public function create(CriarVendaRequest $request)
    {
        $conn = \DB::connection();

        try {
            // dd(now());
            $conn->beginTransaction();
            $idAdmin = auth()->user()->parent_user_id ?? auth()->user()->id;
            $venda   = VendaModel::query()->create([
                'data' => Carbon::parse($request->data_venda)->setTimeFrom(now()),
                'id_cliente'  => $request->id_cliente,
                'id_vendedor' => auth()->user()->id,
                'id_admin'    => $idAdmin,
            ]);

            // 🔒 valida soma de quantidade por produto
            $quantidades = [];

            foreach ($request->itens as $item) {

                $produtoId = $item['id_produto'];

                if (!isset($quantidades[$produtoId])) {
                    $quantidades[$produtoId] = 0;
                }

                $quantidades[$produtoId] += $item['quantidade'];

                if ($quantidades[$produtoId] > 99) {
                    throw ValidationException::withMessages([
                        'itens' => 'Quantidade máxima por produto é 99.'
                    ]);
                }
            }

            foreach ($request->itens as $item) {
                ItemModel::query()->create([
                    'id_venda'   => $venda->id,
                    'id_produto' => $item['id_produto'],
                    'valor'      => $item['valor'],
                    'quantidade' => $item['quantidade'],
                    'id_admin'   => $idAdmin,

                ]);
            }

            $conn->commit();
            return redirect()->route('vendas.listar');
        } catch (\Exception $e) {
            $conn->rollBack();
            dd($e);
        }
    }

    // 🔥 UPDATE
    public function update(CriarVendaRequest $request, $idVenda)
    {
        $conn = \DB::connection();
        try {
            $conn->beginTransaction();

            $venda = VendaModel::query()->where('id', $idVenda)->first();

            $venda->update([
                'data' => Carbon::parse($request->data_venda)->setTimeFrom(now()),
                'id_cliente' => $request->id_cliente,
            ]);

            foreach ($request->itens as $item) {
                if (!empty($item['id'])) {
                    $itemVez = ItemModel::query()->where('id', $item['id'])->first();
                    $itemVez->update([
                        'id_produto' => $item['id_produto'],
                        'valor'      => $item['valor'],
                        'quantidade' => $item['quantidade'],
                    ]);
                } else {
                    ItemModel::query()->create([
                        'id_venda'   => $idVenda,
                        'id_produto' => $item['id_produto'],
                        'valor'      => $item['valor'],
                        'quantidade' => $item['quantidade'], // ⭐ FALTAVA ISSO
                        'id_admin'   => auth()->user()->parent_user_id ?? auth()->user()->id,
                    ]);
                }
            }

            $conn->commit();
            return redirect()->route('vendas.listar');
        } catch (\Exception $e) {
            $conn->rollBack();
            dd($e);
        }
    }

    public function remove($idVenda)
    {
        $venda = VendaModel::query()->where('id', $idVenda)->first();
        $venda->update([
            'removido' => true,
        ]);
    }

    public function removerItem($idItem)
    {
        $item    = ItemModel::query()->where('id', $idItem)->first();
        $idVenda = $item->id_venda;
        $item->delete();
        return redirect()->route('vendas.persistir', $idVenda);
    }

    public function export($idVenda)
    {
        // dd($idVenda);
        try {
            return Excel::download(new VendaExport($idVenda), 'vendas.xlsx');
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function exportPdf($idItem)
    {
        // Lógica para exportar a venda em PDF
        // Você pode usar uma biblioteca como Dompdf ou Snappy para gerar o PDF
        $venda = VendaModel::with(['itens.produto', 'cliente', 'vendedor.user', 'admin'])->find($idItem);

        // dd($venda->data); // 👈 coloque aqui para testar

        $isAdmin = auth()->user()->roleName === 'admin';

        $dompdf = new Dompdf();

        $dompdf->loadHtml(
            view('exports.venda', [
                'venda' => $venda,
                'isAdmin' => $isAdmin
            ])
        );

        $dompdf->setPaper('A4', 'landscape');

        $dompdf->render();

        return $dompdf->stream();
    }
}
