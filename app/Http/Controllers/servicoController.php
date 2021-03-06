<?php

namespace App\Http\Controllers;
use App\balancoFinanceiro;
use App\Carro;
use App\Funcionario;
use App\Produto;
use App\produtoServico;
use App\Servico;
use App;

use App\Services\Pdf as PdfDownloader;
use App\Repositories\BalancoFinanceiroRepository;
use App\Repositories\CarroRepository;
use App\Repositories\ServicosFuncionariosRepository;
use App\Repositories\servicosProdutosRepository;
use App\servicosFuncionarios;
use App\Repositories\ServicoRepository;
use App\Repositories\ProdutoRepository;
use App\Repositories\FuncionarioRepository;

use Barryvdh\DomPDF\Facade as PDF;
use Dompdf\Dompdf;
use Dotenv\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use SebastianBergmann\CodeCoverage\TestFixture\C;


class servicoController extends Controller
{

    private $model;
    private $modelProdutos;
    private $modelFuncionarios;
    private $modelCarros;

    public function __construct(){
        $this->model = new ServicoRepository(new Servico());
        $this->modelProdutos = new ProdutoRepository(new Produto());
        $this->modelFuncionarios = new FuncionarioRepository(new Funcionario());
        $this->modelCarros = new CarroRepository(new Carro());
    }

    public function index(Request $request)
    {
        return view('Servicos.listagemServicos', [
            'servicos' => $this->model->paginate(8)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
//        $pdfHandler = new Pdf(new Dompdf());
//        $pdfHandler->criaPdf();
        return view('Servicos.cadastroServicos', [
            'funcionarios' => $this->modelFuncionarios->findAll(),
            'carros' => $this->modelCarros->findAll(),
            'produtos' => $this->modelProdutos->findAll()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
//        $request->validate([
//            'tipoServico' => ['max:255'],
//            'IdFuncionario' => ['exists:mysql.funcionarios,IdFuncionario'],
//            'Carro' => ['exists:mysql.carros,Idcarro'],
//            'produtos' => ['exists:mysql.produtos,Codigo']
//        ]);



        if($request->option == 'false' or $request->option == null)
        {
            $balancoFinanceiro = new BalancoFinanceiroRepository(new balancoFinanceiro());



            $servicoInserido = $this->model->create([
                'Carro' => $request->input('Carro'),
                "Tiposervico" => $request->input('Tiposervico'),
                "StatusServ" => 'Em andamento',
                "DataChegada" => $request->input('DataChegada'),
                "PrevisãoSaida" => $request->input('PrevisãoSaida')
            ]);

            $servicoFuncionarios = new ServicosFuncionariosRepository(new servicosFuncionarios());


            $servicoFuncionarios->create($request->input('IdFuncionario'), $servicoInserido->IdServico);

            if($request->input('produtos'))
            {
                $servicoProduto = new servicosProdutosRepository(new produtoServico());
                $servicoProduto->create($servicoInserido->IdServico, $request->input('produtos'));
            }

            $balancoFinanceiro->newProfit($servicoInserido, $request->input('valor'));
        }

        // Criando o PDF
        if($request->option == 'true' or $request->option == null)
        {
            $carroRepository = new CarroRepository(new Carro());
            $produtos = array();
            $total = 0;
            foreach($request->produtos as $key => $produto)
            {
                if($produto != Null)
                {
                    $collection = collect($this->modelProdutos->find($key));
                    $total += $collection->get('Valor');
                    $produtos[$key] = $collection->merge(['quantidadeUsada' => $produto]);
                }
            }

            $nomeCliente = $carroRepository->find($request->Carro)->proprietario->Nome;

            return PDF::loadView('Servicos.orcamentoServico', [
                'servico' => $request->all(),
                'produtos' => $produtos,
                'somaProdutos' => $total,
                'cliente' => $nomeCliente

            ])->stream();


        }


    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id)
    {

        $servico = $this->model->find($id);

        $date = new \DateTime($servico->DataChegada);
        $servico->DataChegada = $date->format('Y-m-d\TH:i:s');

        $date = new \DateTime($servico->Previsãosaida);
        $servico->Previsãosaida = $date->format('Y-m-d\TH:i:s');


        return view('Servicos.editaServicos', [
            'servico' => $servico
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Servico $servico
     */
    public function update(Request $request, Servico $servico)
    {

        $servicoEditado = $this->model->update($servico->IdServico, [
            "Tiposervico" => $request->input('Tiposervico'),
            "DataChegada" => date('Y-m-d h:m:s', strtotime($request->input('DataChegada'))),
            "PrevisãoSaida" => date('Y-m-d h:m:s', strtotime($request->input('PrevisãoSaida'))),
        ]);

        if($servicoEditado){
            return redirect(route('servicos.index'))->with('mensagemSucesso', 'O registro foi editado com sucesso');
        }else{
            return redirect(route('servicos.index'))->with('mensagemErro', 'Não foi possível editar o serviço');
        }
    }

    /**
     * Show the specified resource from storage.
     *
     * @param  Servico  $servico
     */

    public function show(Servico $servico){
        return view('servicos.detalhesServicos', [
            'servico' => $servico
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        $resultado = $this->model->delete($id);
        if($resultado){
            return redirect()->back()->with('mensagemSucesso', 'O serviço foi deletado com sucesso.');
        }else{
            return redirect()->back()->with('mensagemErro', 'Não foi possível deletar o serviço.');
        }
    }

    /**
     * Conclude a service.
     *
     * @param  int  $id
     */
    public function done($id){
        $this->model->done($id);
        return redirect()->back();
    }
}
