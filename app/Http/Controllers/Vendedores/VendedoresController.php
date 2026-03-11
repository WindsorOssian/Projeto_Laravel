<?php

namespace App\Http\Controllers\Vendedores;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendedor\CriarVendedorRequest;
use App\Http\Requests\Vendedor\EditarVendedorRequest;
use App\Models\EnderecoModel;
use App\Models\User;
use App\Models\VendedorModel;
use Inertia\Inertia;
use Illuminate\Support\Facades\URL;

class VendedoresController extends Controller
{
    public function index()
    {   // user precisa ser igual a  public function user(): BelongsTo na model
        $vendedores = VendedorModel::query()
            ->join('users', 'users.id', '=', 'tb_vendedor.id_vendedor')
            ->with('user')
            ->where('tb_vendedor.removido', false)
            ->where('tb_vendedor.id_admin', auth()->user()->id)
            ->orderBy('users.name', 'asc')
            ->select('tb_vendedor.*')
            ->paginate(10);
        // dd($vendedores); ou dd($vendedores->toArray);

        $vendedores->getCollection()->transform(function ($vendedor) {

            $vendedor->edit_url = URL::signedRoute(
                'vendedores.persistir',
                ['id' => $vendedor->id_vendedor] // importante
            );

            return $vendedor;
        });

        return Inertia::render('vendedores/Listar', [
            'vendedores' => $vendedores,
        ]);
    }

    public function persistir($idVendedor = null)
    {

        if ($idVendedor !== null) {
            $vendedor = VendedorModel::with(['user', 'endereco'])
                ->where('id_vendedor', $idVendedor)
                ->first();
        }

        return Inertia::render(
            'vendedores/Persistir',
            [
                'vendedor' => $vendedor ?? null,
                'idVendedor' => $idVendedor,
            ]
        );
    }

    public function create(CriarVendedorRequest $request)
    {
        // dd("email");
        // Necessário simular um usuário logado, divide o email em um array, antes e após o @, pode dar erro em algo isso tem haver com a ide que criou um metodo interno
        // Vai funcionar mesmo com o "erro"
        // dd palavra reservada dd vai mostrar na tela as informações
        // dd($request->all());
        // dd($request);

        $conn = \DB::connection();
        try {
            $conn->beginTransaction();

            $emailAntesArroba = explode('@', $request->email)[0];

            $user = User::create([
                'name'              => $request->nome,
                'parent_user_id'    => auth()->user()->id,
                'email'             => $request->email,
                'password'          => password_hash($emailAntesArroba, PASSWORD_DEFAULT),
            ]);

            $user->addRole('vendedor');

            $endereco = EnderecoModel::query()->create([
                'cep'           => $request->cep,
                'rua'           => $request->rua,
                'numero'        => $request->numero,
                'complemento'   => $request->complemento,
                'bairro'        => $request->bairro,
                'cidade'        => $request->cidade,
                'estado'        => $request->estado,
            ]);

            $vendedor = VendedorModel::query()->create([
                'id_vendedor'   => $user->id,
                'comissao'      => $request->comissao,
                'cpf'           => $request->cpf,
                'observacoes'   => $request->observacoes,
                'id_endereco'   => $endereco->id,
                'id_admin'      => auth()->user()->id,

            ]);
            $conn->commit();
            return redirect()->route('vendedores.listar');
        } catch (\Exception $e) {
            $conn->rollBack();
            dd($e);
        }
    }

    public function update(EditarVendedorRequest $request)
    {
        // dd("email");
        // Necessário simular um usuário logado, divide o email em um array, antes e após o @, pode dar erro em algo isso tem haver com a ide que criou um metodo interno
        // Vai funcionar mesmo com o "erro"
        // dd palavra reservada dd vai mostrar na tela as informações
        // dd($request->all());
        // dd($request);

        $conn = \DB::connection();
        try {
            $conn->beginTransaction();

            $vendedor = VendedorModel::query()->where('id_vendedor', $request->id_vendedor)->first();

            $vendedor->user->update([
                'name'              => $request->nome,
                'email'             => $request->email,
            ]);

            $vendedor->endereco->update([
                'cep'           => $request->cep,
                'rua'           => $request->rua,
                'numero'        => $request->numero,
                'complemento'   => $request->complemento,
                'bairro'        => $request->bairro,
                'cidade'        => $request->cidade,
                'estado'        => $request->estado,
            ]);

            $vendedor->update([
                'comissao'      => $request->comissao,
                'cpf'           => $request->cpf,
                'observacoes'   => $request->observacoes,

            ]);
            $conn->commit();
            return redirect()->route('vendedores.listar');
        } catch (\Exception $e) {
            $conn->rollBack();
            dd($e);
        }
    }

    public function remove($idVendedor)
    {
        // dd('id_vendedor');
        $vendedor = VendedorModel::query()
            ->where('id_vendedor', $idVendedor)
            ->first();

        $vendedor->update([
            'removido' => true,
        ]);

        return redirect()
            ->route('vendedores.listar')
            ->with('success', 'Vendedor removido com sucesso.');
    }
}
