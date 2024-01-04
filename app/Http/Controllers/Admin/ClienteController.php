<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cliente;
use App\Models\TableCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Cliente\ClienteInsertRequest;
use App\Http\Requests\Admin\Cliente\ClienteUpdateRequest;
use App\Http\Requests\Admin\Cliente\ValidaCpfCnpj;

class ClienteController extends Controller
{


    public function __construct(Cliente $clientes)
    {
        $this->cliente = $clientes;

        // Default values
        $this->params['titulo']='Cliente';
        $this->params['main_route']='admin.cliente';

    }

    public function index(Request $request)
    {
        // PARAMS DEFAULT

        $this->params['subtitulo']='Clientes Cadastrados';
        $this->params['arvore'][0] = [
                    'url' => 'admin/cliente',
                    'titulo' => 'Clientes'
        ];
        $params = $this->params;

        $searchFields = $request->except('_token');
        $searchFields['cpf_cnpj']= (isset($searchFields['cpf_cnpj']) && $searchFields['cpf_cnpj'] !== null )?$searchFields['cpf_cnpj']:'';
        $searchFields['nome_razaosocial']= (isset($searchFields['nome_razaosocial'])  && $searchFields['nome_razaosocial'] !== null) ? $searchFields['nome_razaosocial']:'';

        $data = $this->cliente
                        ->select("id","cpf_cnpj" ,"nome_razaosocial","logradouro","telefone","numero","bairro")
                        ->where("nome_razaosocial","like","%".$searchFields['nome_razaosocial']."%")
                        ->where("cpf_cnpj","like","%".$searchFields["cpf_cnpj"]."%")
                        ->orderBy("nome_razaosocial")
                        ->paginate(30);
        return view('admin.cliente.index',compact('params','data','searchFields'));

    }

    public function create(TableCode $codes)
    {
        // PARAMS DEFAULT
        $this->params['subtitulo']='Cadastrar Cliente';
        $this->params['arvore']=[
           [
               'url' => 'admin/cliente',
               'titulo' => 'Cliente'
           ],
           [
               'url' => '',
               'titulo' => 'Cadastrar'
           ]];
       $params = $this->params;
       $preload['codes'] = $codes->select(2);
       return view('admin.cliente.create',compact('params','preload'));
    }

    public function store(ClienteInsertRequest $request)
    {
        $dataForm  = $request->all();
        $insert = $this->cliente->create($dataForm);
        if($insert){
            return redirect()->route($this->params['main_route'].'.index');
        }else{
            return redirect()->route($this->params['main_route'].'.create')->withErrors(['Falha ao fazer Inserir.']);
        }
    }

    public function show($id,TableCode $codes)
    {
        $this->params['subtitulo']='Deletar Cliente';
        $this->params['arvore']=[
           [
               'url' => 'admin/cliente',
               'titulo' => 'Cliente'
           ],
           [
               'url' => '',
               'titulo' => 'Deletar'
           ]];
       $params = $this->params;

       $data = $this->cliente->find($id);
       $preload['codes'] = $codes->select(2);
       return view('admin.cliente.show',compact('params','data','preload'));
    }

    public function edit($id,TableCode $codes)
    {
        $this->params['subtitulo']='Editar Cliente';
        $this->params['arvore']=[
           [
               'url' => 'admin/cliente',
               'titulo' => 'Cliente'
           ],
           [
               'url' => '',
               'titulo' => 'Cadastrar'
           ]];
       $params = $this->params;
       $data = $this->cliente->find($id);
       $preload['codes'] = $codes->select(2);
       return view('admin.cliente.create',compact('params', 'data','preload'));
    }

    public function update(ClienteUpdateRequest $request, $id)
    {
        $data = $this->cliente->find($id);

        $dataForm  = $request->all();

        if($data->update($dataForm)){
            return redirect()->route($this->params['main_route'].'.index');
        }else{
            return redirect()->route($this->params['main_route'].'.create')->withErrors(['Falha ao editar.']);
        }
    }

    public function destroy($id)
    {
        $data = $this->cliente->find($id);

        if($data->delete()){
            return redirect()->route($this->params['main_route'].'.index');
        }else{
            return back()->withErrors(['Falha ao deletar.']);
        }
    }

    public function getClienteCpfCnpj(ValidaCpfCnpj $request)
    {
        $data = $request->only('cpf_cnpj');

        $data = preg_replace('/[^0-9]/', '', $data['cpf_cnpj']);

        return response()->json($this->cliente->getClienteCpfCnpj($data));
    }
}
