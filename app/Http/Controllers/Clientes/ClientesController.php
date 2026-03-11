<?php

namespace App\Http\Controllers\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cliente\CriarClienteRequest;
use App\Http\Requests\Cliente\EditarClienteRequest;
use App\Models\EnderecoModel;
use App\Models\User;
use App\Models\ClienteModel;
use Inertia\Inertia;
use Illuminate\Support\Facades\URL;

class ClientesController extends Controller
{
    public function index()
    {
        // user precisa ser igual a  public function user(): BelongsTo na model
        $clientes = ClienteModel::query()
            ->join('users', 'users.id', '=', 'tb_cliente.id')
            ->with('user')
            ->where('tb_cliente.removido', false)
            ->where('tb_cliente.id_admin', auth()->user()->id)
            ->orderBy('users.name', 'asc')
            ->select('tb_cliente.*')
            ->paginate(10);
        // dd($clientes); ou dd($clientes->toArray);

        // 🔐 criar URL assinada para edição
        $clientes->getCollection()->transform(function ($cliente) {

            $cliente->edit_url = URL::signedRoute(
                'clientes.persistir',
                ['id' => $cliente->id]
            );

            return $cliente;
        });

        return Inertia::render('clientes/Listar', [
            'clientes' => $clientes,
        ]);
    }

    public function persistir($id = null)
    {
        // dd($idCliente);
        if ($id !== null) {
            $cliente = ClienteModel::with(['user', 'endereco'])
                ->where('id', $id)
                ->first();
        }

        return Inertia::render(
            'clientes/Persistir',
            [
                'cliente' => $cliente ?? null,
                'idCliente' => $id,
            ]
        );
    }

    public function create(CriarClienteRequest $request)
    {
        // Necessário simular um usuário logado, divide o email em um array, antes e após o @, pode dar erro em algo isso tem haver com a ide que criou um metodo interno
        // Vai funcionar mesmo com o "erro"
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

            $endereco = EnderecoModel::query()->create([
                'cep'           => $request->cep,
                'rua'           => $request->rua,
                'numero'        => $request->numero,
                'complemento'   => $request->complemento,
                'bairro'        => $request->bairro,
                'cidade'        => $request->cidade,
                'estado'        => $request->estado,
            ]);

            $cliente = ClienteModel::query()->create([
                'id'   => $user->id,
                'nome' => $request->nome,
                'email' => $request->email,
                'id_endereco' => $endereco->id,
                'id_admin' => auth()->user()->id,

            ]);
            $conn->commit();
            return redirect()->route('clientes.listar');
        } catch (\Exception $e) {
            $conn->rollBack();
            dd($e);
        }
    }

    public function update(EditarClienteRequest $request)
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

            $cliente = ClienteModel::query()->where('id', $request->id)->first();

            $cliente->user->update([
                'name'              => $request->nome,
                'email'             => $request->email,
            ]);

            $cliente->endereco->update([
                'cep'           => $request->cep,
                'rua'           => $request->rua,
                'numero'        => $request->numero,
                'complemento'   => $request->complemento,
                'bairro'        => $request->bairro,
                'cidade'        => $request->cidade,
                'estado'        => $request->estado,
            ]);

            $conn->commit();
            return redirect()->route('clientes.listar');
        } catch (\Exception $e) {
            $conn->rollBack();
            dd($e);
        }
    }

    public function remove($idCliente)
    {
        // dd('id_cliente');
        $cliente = ClienteModel::query()
            ->where('id', $idCliente)
            ->first();

        $cliente->update([
            'removido' => true,
        ]);

        return redirect()
            ->route('clientes.listar')
            ->with('success', 'Cliente removido com sucesso.');
    }
}
