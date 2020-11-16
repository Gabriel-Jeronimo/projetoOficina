<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\servicoController;
use App\Http\Controllers\clienteController;
use App\Http\Controllers\servicosProdutosController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CarroController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


// Serviços
Route::prefix('servicos')->group(function (){
    Route::get('', [servicoController::class, 'index'])->name('servicos.index');
    Route::get('cadastro', [servicoController::class, 'create'])->name('servicos.cadastro');
    Route::post('armazenar', [servicoController::class, 'store'])->name('servicos.armazenar');
    Route::get('{servico}', [servicoController::class, 'show'])->name('servicos.detalhes');
    Route::get('editar/{id}', [servicoController::class, 'edit'])->name('servicos.editar');
    Route::post('atualizar/{id}', [servicoController::class, 'update'])->name('servicos.atualizar');
    Route::get('deletar/{id}', [servicoController::class, 'destroy'])->name('servicos.deletar');
    Route::get('concluir/{id}', [servicoController::class, 'done'])->name('servicos.concluir');
});

// Clientes
Route::prefix('clientes')->group(function (){
    Route::get('', [clienteController::class, 'index'])->name('clientes.index');
    Route::get('/cadastro/fisico', [clienteController::class, 'createFisico'])->name('clientes.cadastroFisico');
    Route::get('/cadastro/juridico', [clienteController::class, 'createJuridico'])->name('clientes.cadastroJuridico');
    Route::post('/armazenar', [clienteController::class, 'store'])->name('clientes.armazenar');
    Route::delete('/deletar/{id}', [clienteController::class, 'destroy'])->name('clientes.deletar');
    Route::get('/{id}', [clienteController::class, 'show'])->name('clientes.detalhes');
});

// Funcionários
Route::prefix('funcionarios')->group(function (){
    Route::get('', [FuncionarioController::class, 'index'])->name('funcionarios.index');
    Route::get('cadastro', [FuncionarioController::class, 'create'])->name('funcionarios.cadastro');
    Route::post('armazenar', [FuncionarioController::class, 'store'])->name('funcionarios.armazenar');
    Route::get('{funcionario}', [FuncionarioController::class, 'show'])->name('funcionarios.detalhes');
});

// Produtos
Route::prefix('produtos')->group(function (){
    Route::get('', [ProdutoController::class, 'index'])->name('produtos.index');
    Route::get('cadastro', [ProdutoController::class, 'create'])->name('produtos.cadastro');
    Route::post('armazenar', [ProdutoController::class, 'store'])->name('produtos.armazenar');
});

// Carros
Route::prefix('carros')->group(function (){
    Route::get('cadastro', [CarroController::class, 'create'])->name('carros.cadastro');
    Route::post('armazenar', [CarroController::class, 'store'])->name('carros.armazenar');
});

