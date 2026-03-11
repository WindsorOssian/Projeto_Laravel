<?php

namespace App\Http\Controllers\Produtos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Produto\CriarProdutoRequest;
use App\Http\Requests\Produto\EditarProdutoRequest;
use App\Models\ProdutoModel;
use Inertia\Inertia;

class ProdutosController extends Controller
{
    public function index()
    {
        $produtos = ProdutoModel::query()
            ->where('removido', false)
            ->orderBy('nome', 'asc')
            ->paginate(10);

        return Inertia::render('produtos/Listar', [
            'produtos' => $produtos,
        ]);
    }

    public function create(CriarProdutoRequest $request)
    {
        // Já que vai ser só em uma tabela não precisa possuir o beginTransaction

        $produto = ProdutoModel::create([
            'nome' => $request->nome,
            'preco' => $request->preco,
            'id_admin' => auth()->user()->id,
        ]);

        return redirect()->route('produtos.listar');
    }

    public function update(EditarProdutoRequest $request, int $id)
    {
        // dd($id);
        // O findOrFail É um método nativo do Laravel (Eloquent ORM), Procura um registro pelo ID; Se não encontrar, lança erro automaticamente (404)
        $produto = ProdutoModel::findOrFail($id);

        // Até funciona, mas não era
        // $produto = ProdutoModel::query()
        //     ->where('id', $request->id)
        //     ->first();

        $produto->update([
            'nome' => $request->nome,
            'preco' => $request->preco,
        ]);

        return redirect()->route('produtos.listar');
    }

    public function remove($idProduto)
    {
        $produto = ProdutoModel::query()
            ->where('id', $idProduto)
            ->first();

        $produto->update([
            'removido' => true,
        ]);

        return redirect()
            ->route('produtos.listar')
            ->with('success', 'Produto removido com sucesso.');
    }
}
