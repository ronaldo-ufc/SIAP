<?php

include_once 'public/uteis/funcoes.php';

use siap\auth\models\Autenticador;
use siap\produto\forms\TemplateProdutoForm;
use siap\produto\models\TemplateProduto;
use siap\produto\forms\AtivosForm;
use siap\produto\models\Ativos;
use siap\produto\models\Movimentacao;
use siap\produto\models\AtivosReabertura;
use Dompdf\Dompdf;

require_once(__DIR__ . '/router_temp.php');
// include autoloader
require_once 'models/dompdf/autoload.inc.php';


//AUXILIAR, VAI SER USADO DPS DOS TESTES.
$app->map(['GET', 'POST'], '/show', function($request, $response, $args) {
    $data = array();
    $data['nome'] = $_GET["nome"];
    $data['categoria'] = $_GET["categoria"];
    $data['modelo'] = $_GET["modelo"];
    $data['dataAtesto'] = $_GET["dataAtesto"];
    $data['status'] = $_GET["status"];
    $data['conservacao'] = $_GET["conservacao"];
    $data['setor'] = $_GET["setor"];
    $data['fornecedor'] = $_GET["fornecedor"];
    $data['notaFiscal'] = $_GET["notaFiscal"];
    $data['empenho'] = $_GET['empenho'];
    $data['descricao'] = $_GET['descricao'];
//    var_dump($data);
    $messages = $this->flash->getMessages();
    //Rota virou get, filtros todos na url
//    $ativos = Ativos::getAll();
    $ativos = Ativos::Filtrar($data['nome'], $data['categoria'], $data['modelo'],
                $data['dataAtesto'], $data['status'], $data['conservacao'], $data['setor'],
                $data['fornecedor'],$data['notaFiscal'],$data['empenho'],$data['descricao']);
    foreach ($data as &$value) {
        if($value == "" || $value == "n"){ 
//            var_dump("HSauhsuahsuah");
            unset($value);
        }
    }
//    var_dump($data);
    if ($request->isPost()) {
        $filtros = array();
        
//        $ativos = Ativos::Filtrar($_POST["nome"], $_POST["categoria"], $_POST["modelo"],
//                $_POST["dataAtesto"], $_POST["status"], $_POST["conservacao"], $_POST["setor"],
//                $_POST["fornecedor"],$_POST["notaFiscal"],$_POST['empenho'],$_POST['descricao']);
    }

    #Verificando se tem mensagem de erro
    if ($messages) {
        foreach ($messages as $_msg) {
            $mensagem = $_msg[0];
        }
    }
    $categorias = \siap\cadastro\models\Categoria::getAll();
    $modelos = \siap\cadastro\models\Modelo::getAll();
    $status = siap\cadastro\models\Status::getAll();
    $conservacoes = siap\cadastro\models\EConservacao::getAll();
    $setores = siap\setor\models\Setor::getAll();

    return $this->renderer->render($response, 'ativos_show.html', array('ativos' => $ativos, 'mensagem' => $mensagem, 'categorias' => $categorias,
                                    'modelos' => $modelos, 'status' => $status, 'conservacoes' => $conservacoes, 'setores' => $setores));
})->setName('AtivosShow');



//
//Mostra os modelos com opção de criar ativos em branco ou atravez de modelo 
//
$app->map(['GET', 'POST'], '/main', function($request, $response, $args) {
    $template = TemplateProduto::getAll();

    return $this->renderer->render($response, 'ativo_main.html', array('templates' => $template, 'mensagem' => $mensagem));
})->setName('Ativos.Main');


//
//Excluir um ativo por meio do número do patrimônio
//
$app->get('/delete/{patrimonio}', function($request, $response, $args) {
    if ($request->isGet()) {
        $aut = \siap\auth\models\Autenticador::instanciar();
        $msg = Ativos::delete($args['patrimonio'], $aut->getUsuario());

        if ($msg[2]) {
            $this->flash->addMessage('danger', $msg[2]);
        } else {
            $this->flash->addMessage('success', 'Registro excluido com sucesso');
        }
    }
    return $response->withStatus(301)->withHeader('Location', '../show');
})->setName('DeleteAtivo');

//
//cria um novo ativo em branco 
//
$app->map(['GET', 'POST'], '/novo/branco', function($request, $response, $args) {
    $mensagem = NULL;
    $mensagemErro = NULL;
    if ($request->isPost()) {
        #Caso seja POST ou seja o usuário está salvando o bem 
        $aut = \siap\auth\models\Autenticador::instanciar();
        $form = AtivosForm::create(["formdata" => $_POST]);

        #Recebe o nome da foto do bem que está numa variável tipo hiddem para pode mostrar, 
        #pois o form estava perdendo o valor quando fazia o post.
        $postParam = $request->getParams();
        
        if ($_FILES['foto']['error'] == 4){
          
            $foto =  $postParam['foto_bem'];
            
        } else{
          // Associamos a classe à variável $upload
          $upload = new siap\models\UploadImagem();
          // Determinamos nossa largura máxima permitida para a imagem
          $upload->width = 450;
          // Determinamos nossa altura máxima permitida para a imagem
          $upload->height = 350;
          // Exibimos a mensagem com sucesso ou erro retornada pela função salvar.
          //Se for sucesso, a mensagem também é um link para a imagem enviada.
          $filename = $upload->salvar($this->get('upload_directory_imagem'), $_FILES['foto']);
          
          if ($filename[0]){
            
            $mensagemErro = $filename[0];
            
          }else{
            
            $foto = $filename[1];
            
          }
        }
        //Verificando se foi digitado o número do TOMBAMENTO
        if($form->getPatrimonio() != ''){
            //Verificando se foi digitado o NOME do patrimonio
            if($form->getNome() != ''){
            $msg = Ativos::create($form->getPatrimonio(), $form->getNome(), $form->getData_atesto(), $form->getNota_fiscal(), $form->getFornecedor(), $form->getDescricao(), $form->getObservacao(), $foto, $form->getMarca(), $form->getModelo(), $form->getAquisicao(), $form->getStatus(), $form->getSetor(), $form->getConservacao(), $args['modelo_id'], $aut->getUsuario(), $form->getCategoria(), $form->getEmpenho());
            if ($msg[2]) {
                $mensagemErro = $msg[2];
                
            } else {
                $mensagem = 'Operação realizada com sucesso.';
                
            }
        } else {
            $mensagemErro = 'Um nome deve ser definido para o bem.';
        }
        } else{
            $mensagemErro = 'Um número do patrimônio deve ser definido para o bem.';
        }
    } else {
        $form = AtivosForm::create(["formdata" => $_GET]);
        $foto = 'sem_imagem.jpg';
    }
    return $this->renderer->render($response, 'ativo_novo.html', array('form' => $form, 'mensagem' => $mensagem, 'foto' => $foto, 'mensagemErro' => $mensagemErro));
})->setName('AtivoEmBranco');

//
//cria um ativo atraves de um modelo
//
$app->map(['GET', 'POST'], '/novo/modelo/{modelo_id}', function($request, $response, $args) {
    $mensagem = NULL;
    $mensagemErro = NULL;
    $template = TemplateProduto::getById($args['modelo_id']);
    if ($request->isGet()) {
        $foto = $template->getFoto();
        $form = AtivosForm::create(["formdata" => $_GET, "data" => [
                        "nome" => $template->getNome(),
                        "foto" => $foto,
                        "data_atesto" => $template->getData_atesto(),
                        "nota_fiscal" => $template->getNota_fiscal(),
                        "fornecedor" => $template->getFornecedor(),
                        "descricao" => $template->getDescricao(),
                        "observacao" => $template->getObservacao(),
                        "marca" => $template->getFabricante_id(),
                        "modelo" => $template->getModelo_id(),
                        "tipo_de_aquisicao" => $template->getAquisicao_id(),
                        "status" => $template->getStatus_id(),
                        "setor" => $template->getSetor_id(),
                        "estado_de_conservacao" => $template->getConservacao_id(),
                        "template_id" => $args['modelo_id'],
                        "categoria" => $template->getCategoria_id(),
                        "empenho" => $template->getEmpenho()
        ]]);
    } else {
        #Caso seja POst, ou seja, o usuário está salvando o bem 
        $aut = \siap\auth\models\Autenticador::instanciar();
        $form = AtivosForm::create(["formdata" => $_POST]);

        #Recebe o nome da foto do bem que está numa variável tipo hiddem para pode mostrar, 
        #pois o form estava perdendo o valor quando fazia o post.
        $postParam = $request->getParams();
        
        if ($_FILES['foto']['error'] == 4){
          
            $foto =  $template->getFoto();
            
        } else {
            // Associamos a classe à variável $upload
          $upload = new siap\models\UploadImagem();
          // Determinamos nossa largura máxima permitida para a imagem
          $upload->width = 450;
          // Determinamos nossa altura máxima permitida para a imagem
          $upload->height = 350;
          // Exibimos a mensagem com sucesso ou erro retornada pela função salvar.
          //Se for sucesso, a mensagem também é um link para a imagem enviada.
          $filename = $upload->salvar($this->get('upload_directory_imagem'), $_FILES['foto']);
          
          if ($filename[0]){
            
            $mensagemErro = $filename[0];
            
          }else{
            
            $foto = $filename[1];
            
          }
        }
        //Verificando se foi digitado o número do TOMBAMENTO
        if($form->getPatrimonio() != ''){
            //Verificando se foi digitado o NOME do patrimonio
            if($form->getNome() != ''){
            $msg = Ativos::create($form->getPatrimonio(), $form->getNome(), $form->getData_atesto(), $form->getNota_fiscal(), $form->getFornecedor(), $form->getDescricao(), $form->getObservacao(), $foto, $form->getMarca(), $form->getModelo(), $form->getAquisicao(), $form->getStatus(), $form->getSetor(), $form->getConservacao(), $args['modelo_id'], $aut->getUsuario(), $form->getCategoria(), $form->getEmpenho());
            if ($msg[2]) {
                $mensagemErro = $msg[2];
                
            } else {
                $mensagem = 'Operação realizada com sucesso.';
                
            }
        } else {
            $mensagemErro = 'Um nome deve ser definido para o bem.';
        }
        } else{
            $mensagemErro = 'Um número do patrimônio deve ser definido para o bem.';
        }
        
    }
    $form->setNome($args['modelo_id']);
    return $this->renderer->render($response, 'ativo_novo.html', array('form' => $form, 'mensagem' => $mensagem, 'foto' => $foto, 'mensagemErro' => $mensagemErro));
})->setName('AtivoProdutoNovo');



// **********************************************************************************************
// ROTAS RELACIONADAS AOS MODELOS (TEMPLATE)
// 
//***********************************************************************************************
//
//Mostra os modelos com opção gerenciamento (excluir um modelo) 
//


$app->map(['GET', 'POST'], '/modelo/main', function($request, $response, $args) {
    $template = TemplateProduto::getAll();

    return $this->renderer->render($response, 'template_main.html', array('templates' => $template, 'mensagem' => $mensagem));
})->setName('TemplateMain');

//
//cria um novo modelo
//
$app->map(['GET', 'POST'], '/modelo/novo', function($request, $response, $args) {
    $mensagem = NULL;
    $mensagemErro = NULL;

    if ($request->isPost()) {
        $form = TemplateProdutoForm::create(["formdata" => $_POST]);
        
         #Recebe o nome da foto do bem que está numa variável tipo hiddem para pode mostrar, 
        #pois o form estava perdendo o valor quando fazia o post.
        $postParam = $request->getParams();
            
        // handle single input with single file upload
       
        if ($_FILES['foto']['error'] == 4){
          
            $foto =  $postParam['foto_bem'];
            
        } else {
          // Associamos a classe à variável $upload
          $upload = new siap\models\UploadImagem();
          // Determinamos nossa largura máxima permitida para a imagem
          $upload->width = 450;
          // Determinamos nossa altura máxima permitida para a imagem
          $upload->height = 350;
          // Exibimos a mensagem com sucesso ou erro retornada pela função salvar.
          //Se for sucesso, a mensagem também é um link para a imagem enviada.
          $filename = $upload->salvar($this->get('upload_directory_imagem'), $_FILES['foto']);
          
          if ($filename[0]){
            
            $mensagemErro = $filename[0];
            
          }else{
            
            $foto = $filename[1];
            
          }
        }
        if($form->getNome() != ''){
            $msg = TemplateProduto::create($form->getNome(), $form->getData_atesto(), $form->getNota_fiscal(), $form->getFornecedor(), $form->getDescricao(), $form->getObservacao(), $foto, $form->getMarca(), $form->getModelo(), $form->getAquisicao(), $form->getStatus(), $form->getSetor(), $form->getConservacao(), $form->getCategoria(), $form->getEmpenho());
            if ($msg[2]) {
                $mensagemErro = $msg[2];
                
            } else {
                $mensagem = 'Operação realizada com sucesso.';
                
            }
        } else {
            $mensagemErro = 'Um nome deve ser definido para o modelo.';
        }
        
    } else {
        $form = TemplateProdutoForm::create(["formdata" => $_GET]);
    }
    return $this->renderer->render($response, 'template_novo.html', array('form' => $form, 'mensagem' => $mensagem, 'foto' => $foto, 'mensagemErro' => $mensagemErro));
})->setName('AtivoModelo');


$app->map(['GET', 'POST'], '/atualizar/modelo/{modelo_id}', function($request, $response, $args) {
    $modelo = \siap\produto\models\TemplateProduto::getById($args['modelo_id']);
    $mensagem = NULL;
    $mensagemErro = NULL;
    if ($request->isGet()) {
        $foto = $modelo->getFoto();
        $form = TemplateProdutoForm::create(["formdata" => $_GET, "data" => [ //AtivosForm::create(["formdata" => $_GET, "data" => [ 
                        "nome" => $modelo->getNome(),
                        "foto" => $foto,
                        "data_atesto" => $modelo->getData_atesto(),
                        "nota_fiscal" => $modelo->getNota_fiscal(),
                        "fornecedor" => $modelo->getFornecedor(),
                        "descricao" => $modelo->getDescricao(),
                        "observacao" => $modelo->getObservacao(),
                        "marca" => $modelo->getFabricante_id(),
                        "modelo" => $modelo->getModelo_id(),
                        "tipo_de_aquisicao" => $modelo->getAquisicao_id(),
                        "status" => $modelo->getStatus_id(),
                        "estado_de_conservacao" => $modelo->getConservacao_id(),
                        "setor" => $modelo->getSetor_id(),
                        "categoria" => $modelo->getCategoria_id(),
                        "empenho" => $modelo->getEmpenho()
        ]]);
    } else {
        #Caso seja POST ou seja o usuário está salvando o modelo 
        $aut = \siap\auth\models\Autenticador::instanciar();
        $form = TemplateProdutoForm::create(["formdata" => $_POST]);

        #Recebe o nome da foto do bem que está numa variável tipo hiddem para pode mostrar, 
        #pois o form estava perdendo o valor quando fazia o post.
        $postParam = $request->getParams();
        if ($_FILES['foto']['error'] == 4){
            $foto = $modelo->getFoto();
        } else{
         // Associamos a classe à variável $upload
        $upload = new siap\models\UploadImagem();
        // Determinamos nossa largura máxima permitida para a imagem
        $upload->width = 450;
        // Determinamos nossa altura máxima permitida para a imagem
        $upload->height = 350;
        // Exibimos a mensagem com sucesso ou erro retornada pela função salvar.
        //Se for sucesso, a mensagem também é um link para a imagem enviada.
        $filename = $upload->salvar($this->get('upload_directory_imagem'), $_FILES['foto']);

        if ($filename[0]){

          $mensagemErro = $filename[0];

        }else{
          $foto = $filename[1];
        }   
        }
        //Verificando se foi digitado o NOME do patrimonio
        if($form->getNome() != ''){
            $model = ($form->getModelo() == NULL)? $modelo->getModelo_id() : $form->getModelo();
            $msg = TemplateProduto::update($modelo->getTemplate_id(), $form->getNome(), $form->getData_atesto(), $form->getNota_fiscal(), $form->getFornecedor(), $form->getDescricao(), $form->getObservacao(), $foto, $form->getMarca(), $model, $form->getAquisicao(), $form->getStatus(), $form->getConservacao(), $form->getCategoria(), $form->getSetor(), $form->getEmpenho());
            if ($msg[2]) {
                $mensagemErro = $msg[2];
            }
            else {
                $mensagem = 'Operação realizada com sucesso.';
            }
        
        } else {
        $mensagemErro = 'Um nome deve ser definido para o modelo.';
        }
            
    }

    return $this->renderer->render($response, 'template_atualizacao.html', array('form' => $form, 'mensagem' => $mensagem, 'foto' => $foto, 'setor_nome' => $modelo->getSetor()->getNome(), 'mensagemErro' => $mensagemErro, 'template_id' => $modelo->getTemplate_id()));
})->setName('ModeloAtualizacao');


//
//Atualiza um  ativo atraves do número de patrimonio
//



$app->map(['GET', 'POST'], '/atualizar/{patrimonio_id}', function($request, $response, $args) {
    $ativo = Ativos::getById($args['patrimonio_id']);
    $mensagem = NULL;
    $mensagemErro = NULL;
    if ($request->isGet()) {
        $foto = $ativo->getFoto();
        $form = AtivosForm::create(["formdata" => $_GET, "data" => [
                        "patrimonio" => $args['patrimonio_id'],
                        "nome" => $ativo->getNome(),
                        "foto" => $ativo->getFoto(),
                        "data_atesto" => $ativo->getData_atesto(),
                        "nota_fiscal" => $ativo->getNota_fiscal(),
                        "fornecedor" => $ativo->getFornecedor(),
                        "descricao" => $ativo->getDescricao(),
                        "observacao" => $ativo->getObservacao(),
                        "marca" => $ativo->getFabricante_id(),
                        "modelo" => $ativo->getModelo_id(),
                        "tipo_de_aquisicao" => $ativo->getAquisicao_id(),
                        "status" => $ativo->getStatus_id(),
                        "estado_de_conservacao" => $ativo->getConservacao_id(),
                        "template_id" => $args['modelo_id'],
                        "categoria" => $ativo->getCategoria_id(),
                        "empenho" => $ativo->getEmpenho()
        ]]);
    } else {
        #Caso seja POST ou seja o usuário está salvando o bem 
        $aut = \siap\auth\models\Autenticador::instanciar();
        $form = AtivosForm::create(["formdata" => $_POST]);

        #Recebe o nome da foto do bem que está numa variável tipo hiddem para pode mostrar, 
        #pois o form estava perdendo o valor quando fazia o post.
        $postParam = $request->getParams();
        
        if ($_FILES['foto']['error'] == 4){
          
            $foto =  $ativo->getFoto();
            
        } else{
          // Associamos a classe à variável $upload
          $upload = new siap\models\UploadImagem();
          // Determinamos nossa largura máxima permitida para a imagem
          $upload->width = 450;
          // Determinamos nossa altura máxima permitida para a imagem
          $upload->height = 350;
          // Exibimos a mensagem com sucesso ou erro retornada pela função salvar.
          //Se for sucesso, a mensagem também é um link para a imagem enviada.
          $filename = $upload->salvar($this->get('upload_directory_imagem'), $_FILES['foto']);
          
          if ($filename[0]){
            
            $mensagemErro = $filename[0];
            
          }else{
            
            $foto = $filename[1];
            
          }
        }
        //Verificando se foi digitado o NOME do patrimonio
        if($form->getNome() != ''){
        $msg = Ativos::update($ativo->getPatrimonio(), $form->getNome(), $form->getData_atesto(), $form->getNota_fiscal(), $form->getFornecedor(), $form->getDescricao(), $form->getObservacao(), $foto, $form->getMarca(), $form->getModelo(), $form->getAquisicao(), $form->getStatus(), $form->getConservacao(), $aut->getUsuario(), $form->getCategoria(), $form->getEmpenho());
        if ($msg[2]) {
            $mensagemErro = $msg[2];

        } else {
            $mensagem = 'Operação realizada com sucesso.';

        }
        } else {
        $mensagemErro = 'Um nome deve ser definido para o bem.';
            }
    }

    return $this->renderer->render($response, 'ativo_atualizacao.html', array('form' => $form, 'mensagem' => $mensagem, 'foto' => $foto, 'setor_nome' => $ativo->getSetor()->getNome(), 'mensagem' => $mensagem , 'mensagemErro' => $mensagemErro, 'patrimonio' => $args['patrimonio_id']));
})->setName('AtivoAtualizacao');

// **********************************************************************************************
// ROTAS RELACIONADAS AS MOVIMENTAÇÕES 
// 
//***********************************************************************************************

$app->map(['GET', 'POST'], '/movimentacao/{patrimonio}', function($request, $response, $args) {
    $mensagem = NULL;
    $mensagemErro = NULL;
    if ($request->isPost()) {
        $directory = $this->get('upload_directory_documento');
        $uploadedFiles = $request->getUploadedFiles();
        // handle single input with single file upload
        $uploadedFile = $uploadedFiles['documento'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = moveUploadedFile($directory, $uploadedFile);
        }
        $filename = $filename? $filename:"Sem Documento";
        $aut = Autenticador::instanciar();
        $postParam = $request->getParams();
        $msg = Movimentacao::create($args['patrimonio'], $postParam['setor'], $postParam['movimentacao_data'], $filename, $postParam['observacao'], $aut->getUsuario());
        if ($msg[2]) {
          $mensagemErro = $msg[2];
          $form->errors = 'danger';
        } else {
            $mensagem = 'Operação realizada com sucesso.';
            $form->errors = 'success';
        }
    }

    $setores = \siap\setor\models\Setor::getAll();
    $ativo = Ativos::getById($args['patrimonio']);
    $movimentcoes = siap\produto\models\Movimentacao::getAllByPatrimonio($args['patrimonio']);

    return $this->renderer->render($response, 'ativo_movimentacao.html', array('mensagem' => $mensagem,'mensagemErro' => $mensagemErro,
                'setores' => $setores,
                'ativo' => $ativo,
                'movimentacoes' => $movimentcoes
    ));
})->setName('AtivoMovimentacao');


$app->get('/reabertura', function ($request, $response, $args) {


    $ativos_reabertura = AtivosReabertura::getAll();
    return $this->renderer->render($response, 'ativos_reabertura.html', array('mensagem' => $mensagem,
                'ativos_reabertura' => $ativos_reabertura
    ));
})->setName('AtivoReabertura');


$app->get('/reabertura/delete/{patrimonio}', function($request, $response, $args) {
    if ($request->isGet()) {
        $msg = AtivosReabertura::delete($args['patrimonio']);
    }
    return $response->withStatus(301)->withHeader('Location', '../../reabertura');
})->setName('AtivoDeleteReabertura');


//---------------------------------------------------------------------------------------------------------------------------------

$app->get('/main/delete/{template_id}', function($request, $response, $args) use ($app) {
    $mensagem = NULL;
    $mensagemErro = NULL;
    if ($request->isGet()) {
        $msg = TemplateProduto::delete($args['template_id']);
        if ($msg[2]) {
            $mensagemErro = $msg[2];
        } else {
            $mensagem = 'Registro excluido com sucesso';
        }
    }
//    $app->response->redirect($app->urlFor('Ativos.Main', array('mensagem' => $mensagem, 'mensagemErro' => $mensagemErro)));
    return $response->withStatus(301)->withHeader('Location', '../../main');
})->setName('TemplateDelete');


//---------------------------------------------------------------------------------------------------------------------------------

$app->map(['GET', 'POST'], '/mov/grupo[/{params:.*}]', function($request, $response, $args) {
    $lista = array();
    $mensagem = NULL;
    $mensagemErro = NULL;
    if ($request->isPost()) {
        $directory = $this->get('upload_directory_documento');
        $uploadedFiles = $request->getUploadedFiles();
        // handle single input with single file upload
        $uploadedFile = $uploadedFiles['documento'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = moveUploadedFile($directory, $uploadedFile);
            if ($filename) {
                $aut = Autenticador::instanciar();
                $postParam = $request->getParams();
                $patrimonio = $postParam['pat'];
                $tam = sizeof($patrimonio);
                $count = 0;
                foreach ($patrimonio as $numero){
                    $bem = Ativos::getById($numero);
                    if($bem){
                        array_push($lista, $bem);
                    }
                    $msg = Movimentacao::create($numero, $postParam['setor'], $postParam['movimentacao_data'], $filename, $postParam['observacao'], $aut->getUsuario());
                    if($msg){
                        $count += 1;
                    }
                }
                if ($count == sizeof($patrimonio)) {
                        $mensagem = 'Operação Realizada com Sucesso.';
                    } else {
                        $mensagemErro = 'No período desta movimentação não existe um Agente Setorial responsável pelo setor. Cadastre primeiro o Agente Setorial para o setor.';
                    }
            }
        }
    } else {
        //explode("&", $args["patrimonios"]):: Pegando tudo após grupo/ e separando pelo caractere '/'
        foreach (explode('/', $args['params']) as $tombamento) {
            $bem = Ativos::getById($tombamento);
            if($bem){
                array_push($lista, $bem);
            }
        }
    }
    
    $setores = \siap\setor\models\Setor::getAll();
    return $this->renderer->render($response, 'ativo_movimentacao_grupo.html', array('ativos' => $lista, 'setores' => $setores, 'mensagem' => $mensagem, 'mensagemErro' => $mensagemErro));
})->setName('Mov.Grupo');

$app->map(['GET', 'POST'], '/pdf', function($request, $response, $args) {
    
    $dompdf = new DOMPDF();
    //lendo o arquivo HTML correspondente
    $data = date("d-m-Y");
    $hora = date('H:i:s');
    
    $dompdf->set_option('defaultFont', 'Times New Roman');
    $header = '<body><div style="text-align: center;  border-style: solid; border-width: 1px; padding: 10px 2px 10px 2px;">
        <img style="max-width: 100px; max-height: 100px; margin-left: 20px;" src="assets/img/brasao_ufc.png" align="left">
        <p><b>UNIVERSIDADE FEDERAL DO CEARÁ<br />CAMPUS DE CRATEÚS<br />SISTEMA DE ALMOXARIFADO E PATRIMÔNIO - SIAP</b><br />
            EMITIDO EM '.$data.' '.$hora.'</p>'
            .'</div><br />';
    
    $html = '<div class="container" style="border:2px solid #f0f0f0; border-radius:10px;font-family:sans-serif;">'
            .'<div class="panel panel-default">'
            . '<div class="panel-heading" style="width:100%;display:block;background:#f0f0f0;padding:5px 10px;">Filtros</div>'
            . '<div class="panel-body" style="padding:10px; font-size:12px;">'
            . '<span><b> | Modelo: </b>SEM MODELO</span>'
            . '<span><b> | Modelo: </b>SEM MODELO</span>'
            . '<span><b> | Modelo: </b>SEM MODELO</span>'
            . '<span><b> | Modelo: </b>SEM MODELO</span>'
            . '</div>'
            .'</div>'
            . '</div><br />';
    
    
    
    
    $tabel = '<div class="container" style="border:2px solid #f0f0f0; border-radius:10px;font-family:sans-serif;">'
            .'<div class="panel panel-default">'
            . '<div class="panel-heading" style="width:100%;display:block;background:#f0f0f0;padding:5px 10px;">Resultados</div>'
            . '<div class="panel-body" style="padding:10px;">'
            . '<table class="relatorio" style="font-size:12px;">
                        <thead class="bg-primary">
                            <tr><th >Patrimônio</th><th >Nome</th><th >Categoria</th><th >Modelo</th><th >Data de Atesto</th><th >Status</th><th >Est. Conservação</th><th >Setor</th></tr>
                        </thead>
                        <tbody>
                            {% for ativo in ativos %}

                            <tr style="border-bottom:2px solid #f0f0f0;">
                                <td>0000000</td>
                                <td>CADEIRA SECRETARIA OPERACIONAL_1</td>
                                <td>CADEIRA GIRATORIA</td>
                                <td>SEM MODELO</td>
                                <td>21/08/2018</td>
                                <td>EM USO</td>
                                <td>NOVO</td>
                                <td>ADM</td>
                            </tr>
                            <tr style="border-bottom:1px #f0f0f0;">
                                <td>0000000</td>
                                <td>CADEIRA SECRETARIA OPERACIONAL_1</td>
                                <td>CADEIRA GIRATORIA</td>
                                <td>SEM MODELO</td>
                                <td>21/08/2018</td>
                                <td>EM USO</td>
                                <td>NOVO</td>
                                <td>ADM</td>
                            </tr>
                            {% endfor %}
                        </tbody>
                        <tfoot>
                            <tr><th >Patrimônio</th><th >Nome</th><th >Categoria</th><th >Modelo</th><th >Data de Atesto</th><th >Status</th><th >Est. Conservação</th><th >Setor</th></tr>
                        </tfoot>
                    </table>'
            . '</div>'
            .'</div>'
            . '</div><br /></body>';
    
//    $footer = '<div style="width:100%; height:80px; position:absolute; bottom:0; float:left;">'
//            . '<div class="container" style="width:100%; border-color:blue; text-align:center;">'
//            . '<div style="text-align: left; display: inline-block;">XXXX</div>'
//            . '<div style="text-align: center; display: inline-block;">SIAP | Patrimônio – (88) 3691-9707 | UFC Campus de Crateús – crateus.ufc.br</div>'
//            . '<div style="text-align: right; display: inline-block;">'.$data.' '.$hora.'</div>'
//            . '</div>'
//            . '</div>';
    $footer = '<footer><p>Posted by: Hege Refsnes</p><p>Contact information: <a href="mailto:someone@example.com">someone@example.com</a>.</p></footer>';

    $header .= $html;
    $header .= $tabel;
    $header .= $footer;
    
//    $dompdf->load_html_file($html);
//    $dompdf->setPaper('A4','landscape');
//    $dompdf->render();
//    $dompdf->stream("Relatório_SIAP.pdf", array("Attachment" => FALSE));
    
 
    return $this->renderer->render($response, 'gerar_pdf.html', array('dompdf'=>$dompdf,'array' => array("Attachment" => FALSE), 'header' => $header));
})->setName('Pdf');