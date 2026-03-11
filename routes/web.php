<?php

use App\Http\Controllers\Vendedores\VendedoresController;
use App\Http\Controllers\Clientes\ClientesController;
use App\Http\Controllers\Produtos\ProdutosController;
use App\Http\Controllers\Vendas\VendasController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\VendaModel;
use App\Models\ProdutoModel;
use App\Models\ClienteModel;
use App\Models\VendedorModel;

Route::redirect('/', '/login')->name('home');

/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Exigem Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'vendasHoje' => VendaModel::where('removido', false)
                ->when(auth()->user()->parent_user_id, function ($query) {
                    $query->where('id_vendedor', auth()->user()->id);
                })
                ->whereDate('data', today())
                ->count(),

            'vendasMes' => VendaModel::where('removido', false)
                ->when(auth()->user()->parent_user_id, function ($query) {
                    $query->where('id_vendedor', auth()->user()->id);
                })
                ->whereMonth('data', now()->month)
                ->whereYear('data', now()->year)
                ->count(),

            'clientes' => ClienteModel::where('removido', false)->count(),
            'produtos' => ProdutoModel::where('removido', false)->count(),
            'vendedores' => VendedorModel::where('removido', false)->count(),

            'ultimasVendas' => VendaModel::where('removido', false)
                ->when(auth()->user()->parent_user_id, function ($query) {
                    $query->where('id_vendedor', auth()->user()->id);
                })
                ->with(['cliente', 'vendedor.user', 'admin'])
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    })->name('dashboard');

    // VENDAS (Agora protegidas!)
    Route::prefix('vendas')->name('vendas.')->group(function () {
        Route::get('/', [VendasController::class, 'listar'])->name('listar');
        Route::get('/persistir/{id?}', [VendasController::class, 'persistir'])->name('persistir');
        Route::post('/create', [VendasController::class, 'create'])->name('create');
        Route::put('/update/{id}', [VendasController::class, 'update'])->name('update');
        Route::delete('/remover/{id}', [VendasController::class, 'remove'])->name('remover');
        Route::delete('remove-item/{idItem}', [VendasController::class, 'removerItem'])->name('remover-item');
        Route::get('/excel/{idVenda}', [VendasController::class, 'export'])->name('export');
        Route::get('/pdf/{idVenda}', [VendasController::class, 'exportPdf'])->name('exportPdf');
    });

    /*
    |--------------------------------------------------------------------------
    | Rotas Restritas (Apenas Admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        
        // Vendedores
        Route::prefix('/vendedores')->group(function () {
            Route::get('/', [VendedoresController::class, 'index'])->name('vendedores.listar');
            Route::get('/persistir/{id?}', [VendedoresController::class, 'persistir'])->name('vendedores.persistir');
            Route::post('/create', [VendedoresController::class, 'create'])->name('vendedores.create');
            Route::put('/update/{idVendedor}', [VendedoresController::class, 'update'])->name('vendedores.update');
            Route::delete('/remove/{idVendedor}', [VendedoresController::class, 'remove'])->name('vendedores.remove');
        });

        // Clientes
        Route::prefix('/clientes')->group(function () {
            Route::get('/', [ClientesController::class, 'index'])->name('clientes.listar');
            Route::get('/persistir/{id?}', [ClientesController::class, 'persistir'])->name('clientes.persistir');
            Route::post('/create', [ClientesController::class, 'create'])->name('clientes.create');
            Route::put('/update/{idCliente}', [ClientesController::class, 'update'])->name('clientes.update');
            Route::delete('/remove/{idCliente}', [ClientesController::class, 'remove'])->name('clientes.remove');
        });

        // Produtos
        Route::prefix('/produtos')->group(function () {
            Route::get('/', [ProdutosController::class, 'index'])->name('produtos.listar');
            Route::post('/create', [ProdutosController::class, 'create'])->name('produtos.create');
            Route::put('/update/{id}', [ProdutosController::class, 'update'])->name('produtos.update');
            Route::delete('/remove/{id}', [ProdutosController::class, 'remove'])->name('produtos.remove');
        });
    });
});

require __DIR__ . '/settings.php';