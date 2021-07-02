<?php

include_once 'public/uteis/funcoes.php';

use siap\produto\models\Ativos;
use siap\setor\models\Setor;
use siap\relatorios\relatorio\RelatorioSetor;
use \siap\relatorios\relatorio\CategoriaFiltro;
use siap\relatorios\relatorio\ItemAprovados;
use siap\relatorios\relatorio\Dia;

$app->get('/bens', function($request, $response, $args) {
    $setores = Setor::getAll();
    $status = \siap\cadastro\models\Status::getAll();
    return $this->renderer->render($response, 'bens_permanentes.html', array(
        'setores' => $setores,
        'status' => $status
        ));
})->setName('RelatoriosBemPermanente');

$app->get('/gerarpdf-bens', function($request, $response, $args) {
    $postParam = $request->getParams();
    header ("Pragma: no-cache");
    header ("Content-type: application/x-msexcel");
    header ("Content-Description: PHP Generated Data" );
    if (intval($postParam["setor_id"]) == 'TODOS') {
        $relatorio_all_setor = \siap\relatorios\relatorio\TodosSetores::bundle($postParam['status']);
        if ($postParam['formato'] == 'xls'){
            $body = $relatorio_all_setor->geraXls();
            header ("Content-Disposition: attachment; filename=\"todos_bens_permanentes.xls\"" );
        }else{
            $body = $relatorio_all_setor->geraHtml();
        }
        echo $body[1]; return;
        
    }else{
        $setor = Setor::getById(intval($postParam["setor_id"]));
        $relatorio_setor = RelatorioSetor::bundle(intval($postParam["setor_id"]), $setor->getNome(), $postParam['status']);
        
        if ($postParam['formato'] == 'xls'){
            $body = $relatorio_setor->geraXls();
            header ("Content-Disposition: attachment; filename=\"".strtolower($setor->getNome()).".xls\"" );
        }else{
            $body = $relatorio_setor->geraHtml();
        }
        echo $body[1]; 
        return;
  } 
    
})->setName('gerarPDFBens');

$app->map(['GET', 'POST'], '/categoria', function($request, $response, $args) {
    $modelos = \siap\cadastro\models\Modelo::getAll();
    $status = siap\cadastro\models\Status::getAll();
    $conservacoes = siap\cadastro\models\EConservacao::getAll();
    $categorias = siap\cadastro\models\Categoria::getAll();
    $setores = Setor::getAll();
    if ($request->isPost()) {
        $dompdf = $this->DOMPDF;
        $dompdf->setPaper('A4', 'portrait'); //landscape
        
        $postParam = $request->getParams();
        $relatorio_categoria = CategoriaFiltro::bundle();
        $header = $relatorio_categoria->start_pdf($postParam["nome"], $postParam['categoria'], $postParam["modelo"], $postParam["dataAtesto"], $postParam["status"], $postParam["conservacao"], $postParam["setor"], $postParam["fornecedor"], $postParam["notaFiscal"], $postParam['empenho'], $postParam['descricao']);
        
        if ($header[2] != NULL) {
            return $this->renderer->render($response, 'bens_categoria.html', array('modelos' => $modelos, 'status' => $status, 'conservacoes' => $conservacoes, 'setores' => $setores, 'categorias' => $categorias, "mensagemErro" => $header[2]));
        } else {
            return $this->renderer->render($response, 'gerar_pdf.html', array('dompdf' => $dompdf, 'array' => array("Attachment" => FALSE), 'header' => $header[1]));
        }
    }
    return $this->renderer->render($response, 'bens_categoria.html', array('modelos' => $modelos, 'status' => $status, 'conservacoes' => $conservacoes, 'setores' => $setores, 'categorias' => $categorias));
})->setName('RelatoriosBemCategoria');

$app->map(['GET', 'POST'], '/categoria/setor', function($request, $response, $args) {
    $mensagemErro = NULL;
    $modelos = \siap\cadastro\models\Modelo::getAll();
    $status = siap\cadastro\models\Status::getAll();
    $conservacoes = siap\cadastro\models\EConservacao::getAll();
    $categorias = siap\cadastro\models\Categoria::getAll();
    $setores = Setor::getAll();
    if ($request->isPost()) {
        $dompdf = $this->DOMPDF;
        $dompdf->setPaper('A4', 'portrait'); //landscape
        $postParam = $request->getParams();
        $relatorio_categoria = \siap\relatorios\relatorio\CategoriaSetor::bundle();
        $header = $relatorio_categoria->start_pdf($postParam["nome"], $postParam['categoria'], $postParam["modelo"], $postParam["dataAtesto"], $postParam["status"], $postParam["conservacao"], $postParam["setor"], $postParam["fornecedor"], $postParam["notaFiscal"], $postParam['empenho'], $postParam['descricao']);
        if ($header[2] != NULL) {
            $mensagemErro = $header[2];
            return $this->renderer->render($response, 'bens_categoria.html', array('modelos' => $modelos, 'status' => $status, 'conservacoes' => $conservacoes, 'setores' => $setores, 'categorias' => $categorias, "mensagemErro" => $mensagemErro));
        } else {
            return $this->renderer->render($response, 'gerar_pdf.html', array('dompdf' => $dompdf, 'array' => array("Attachment" => FALSE), 'header' => $header[1]));
        }
    }
    return $this->renderer->render($response, 'bens_categoria.html', array('modelos' => $modelos, 'status' => $status, 'conservacoes' => $conservacoes, 'setores' => $setores, 'categorias' => $categorias));
})->setName('RelatoriosCategoriaSetor');

$app->map(['GET', 'POST'], '/movimentacao/setor', function($request, $response, $args) {
    $mensagemErro = NULL;
    if ($request->isPost()) {
        $dompdf = $this->DOMPDF;
        $dompdf->setPaper('A4', 'portrait'); //landscape
        $postParam = $request->getParams();
        $mov_bem_setor = \siap\relatorios\relatorio\MovimentacaoBemSetor::bundle();
        $header = $mov_bem_setor->start_pdf($postParam["dataAtesto"], $postParam["dataAtesto"]);
        if ($header[2] != NULL) {
            $mensagemErro = $header[2];
            return $this->renderer->render($response, 'bens_categoria.html', array('mensagemErro' => $mensagemErro));
        } else {
            return $this->renderer->render($response, 'gerar_pdf.html', array('dompdf' => $dompdf, 'array' => array("Attachment" => false), 'header' => $header[1]));
        }
    }
    $setores = Setor::getAll();
    $modelos = \siap\cadastro\models\Modelo::getAll();
    $status = siap\cadastro\models\Status::getAll();
    $conservacoes = siap\cadastro\models\EConservacao::getAll();
    $categorias = siap\cadastro\models\Categoria::getAll();
    return $this->renderer->render($response, 'bens_categoria.html', array('modelos' => $modelos, 'status' => $status, 'conservacoes' => $conservacoes, 'setores' => $setores, 'categorias' => $categorias));
})->setName('RelatoriosSetorMovimentacao');

$app->map(['GET', 'POST'], '/setor/movimentacao', function($request, $response, $args) {
    if ($request->isPost()) {
        $dompdf = $this->DOMPDF;
        $dompdf->setPaper('A4', 'portrait'); //landscape
        $postParam = $request->getParams();
        $mov_bem_setor = \siap\relatorios\relatorio\MovimentacaoBemSetor::bundle();
        $header = $mov_bem_setor->start_pdf($postParam["dataAtesto"], $postParam["dataAtesto"]);
        return $this->renderer->render($response, 'gerar_pdf.html', array('dompdf' => $dompdf, 'array' => array("Attachment" => FALSE), 'header' => $header));
    }
    $categorias = \siap\cadastro\models\Categoria::getAll();
    $modelos = \siap\cadastro\models\Modelo::getAll();
    $status = siap\cadastro\models\Status::getAll();
    $conservacoes = siap\cadastro\models\EConservacao::getAll();
    $setores = siap\setor\models\Setor::getAll();
    $ativos = Ativos::getAll();
    return $this->renderer->render($response, 'bens_por_setor.html', array('modelos' => $modelos, 'status' => $status, 'conservacoes' => $conservacoes, 'setores' => $setores, 'categorias' => $categorias, 'ativos' => $ativos));
})->setName('RelatoriosBemMovimentacao');

$app->map(['GET', 'POST'], '/setor/movimentacao/{patrimonio}', function($request, $response, $args) {
    $dompdf = $this->DOMPDF;
    $dompdf->setPaper('A4', 'portrait'); //landscape
    $mov_bem = \siap\relatorios\relatorio\MovimentacaoBem::bundle();
    $header = $mov_bem->start_pdf($args["patrimonio"]);
    return $this->renderer->render($response, 'gerar_pdf.html', array('dompdf' => $dompdf, 'array' => array("Attachment" => FALSE), 'header' => $header));
})->setName('RelatoriosMovimentacoesDoBem');

$app->map(['GET', 'POST'], '/setor/mov/grupo[/{params:.*}]', function($request, $response, $args) {
    $dompdf = $this->DOMPDF;
    $dompdf->setPaper('A4', 'portrait'); //landscape
    $mov_bem = \siap\relatorios\relatorio\MovimentacaoConjuntoBem::bundle();
    $lista = array();
    foreach (explode('/', $args['params']) as $tombamento) {
        array_push($lista, $tombamento);
    }
    $header = $mov_bem->start_pdf($lista);
    return $this->renderer->render($response, 'gerar_pdf.html', array('dompdf' => $dompdf, 'array' => array("Attachment" => FALSE), 'header' => $header));
})->setName('RelatoriosBemMovimentacao');

$app->map(['GET', 'POST'], '/empenho', function($request, $response, $args) {
    $mensagemErro = NULL;
    if ($request->isPost()) {
        $dompdf = $this->DOMPDF;
        $dompdf->setPaper('A4', 'portrait'); //landscape
        $postParam = $request->getParams();
        $empenho = siap\relatorios\relatorio\Empenho::bundle();
        $header = $empenho->start_pdf($postParam["nome"], $postParam['categoria'], $postParam["modelo"], $postParam["dataAtesto"], $postParam["status"], $postParam["conservacao"], $postParam["setor"], $postParam["fornecedor"], $postParam["notaFiscal"], $postParam['empenho'], $postParam['descricao']);
        if ($header[2] != NULL) {
            $mensagemErro = $header[2];
            return $this->renderer->render($response, 'bens_categoria.html', array('mensagemErro' => $mensagemErro));
        } else {
            return $this->renderer->render($response, 'gerar_pdf.html', array('dompdf' => $dompdf, 'array' => array("Attachment" => false), 'header' => $header[1]));
        }
    }
    $setores = Setor::getAll();
    $modelos = \siap\cadastro\models\Modelo::getAll();
    $status = siap\cadastro\models\Status::getAll();
    $conservacoes = siap\cadastro\models\EConservacao::getAll();
    $categorias = siap\cadastro\models\Categoria::getAll();
    return $this->renderer->render($response, 'bens_categoria.html', array('modelos' => $modelos, 'status' => $status, 'conservacoes' => $conservacoes, 'setores' => $setores, 'categorias' => $categorias));
})->setName('RelatoriosEmpenho');

$app->map(['GET', 'POST'], '/responsavel', function($request, $response, $args) {
  $mensagemErro = NULL;
  if ($request->isPost()) {
    $dompdf = $this->DOMPDF;
    $dompdf->setPaper('A4', 'portrait'); //landscape
    $postParam = $request->getParams();
    $responsavel = siap\relatorios\relatorio\Responsavel::bundle();
    $header = $responsavel->start_pdf($postParam['dataInicio'], $postParam['dataFim']);
    if ($header[2] != NULL) {
        $mensagemErro = $header[2];
        return $this->renderer->render($response, 'bens_responsavel.html', array('mensagemErro' => $mensagemErro));
    } else {
        return $this->renderer->render($response, 'gerar_pdf.html', array('dompdf' => $dompdf, 'array' => array("Attachment" => false), 'header' => $header[1]));
    }
  }
  return $this->renderer->render($response, 'bens_responsavel.html', array('mensagemErro' => $mensagemErro));
})->setName('RelatorioResponsavel');


$app->get('/materiais-aprovado/{requisicao}', function($request, $response, $args) {
  $relatorio = new ItemAprovados();
  $relatorio->criar($args['requisicao']);
  $relatorio->imprimir($this->DOMPDF);
})->setName('Relatoriomateriais-aprovado');

/*****************************************************************/

$app->get('/consumo/produto', function($request, $response, $args) {
  $msg = getMensagem($this->flash->getMessages());
  $ini = date('Y').'-01-01';
  $fim = date('Y-m-d');
  return $this->renderer->render($response, 'setor_consumo.html', array( 
      'ini'=>$ini, 
      'fim'=>$fim, 
      'classe'=> $msg[0], 
      'texto'=>$msg[1]
  ));
})->setName('Relatorioconsumo');

$app->get('/consumo/produto/pdf', function($request, $response, $args) {
  $postParam = $request->getParams();
  $relatorio = new \siap\relatorios\relatorio\ItemConsumo();

  $vazio = $relatorio->criar($postParam['produto_codigo'], $postParam['dataInicio'] , $postParam['dataFim']);
  if (!$vazio){
    $this->flash->addMessage('warning', 'Não existe dados para serem mostrados');
    return $response->withStatus(301)->withHeader('Location', $_SERVER['HTTP_REFERER']);
  }
  $relatorio->imprimir($this->DOMPDF);
})->setName('Relatorioconsumo-produto');


/*****************************************************************/

$app->get('/setor-consumo', function($request, $response, $args) {
  $msg = getMensagem($this->flash->getMessages());
  $setores = Setor::getAll();
  $ini = date('Y').'-01-01';
  $fim = date('Y-m-d');
  return $this->renderer->render($response, 'materiais_requisitados_por_setor.html', array(
      'setores'=>$setores, 
      'ini'=>$ini, 
      'fim'=>$fim, 
      'classe'=> $msg[0], 
      'texto'=>$msg[1]
  ));
  
})->setName('RelatorioSetorConsumo');

$app->get('/setor-consumo/pdf', function($request, $response, $args) {
  
  $relatorio = new \siap\relatorios\relatorio\SetorConsumo();
  $vazio = $relatorio->criar($request->getParams());
  
  if (!$vazio){
    $this->flash->addMessage('warning', 'Não existe dados para serem mostrados');
    return $response->withStatus(301)->withHeader('Location', $_SERVER['HTTP_REFERER']);
  }
  
  $relatorio->imprimir($this->DOMPDF);
            
})->setName('Relatorioconsumo-produto');

$app->get('/dia', function($request, $response, $args) {    
    header ("Pragma: no-cache");
    header ("Content-type: application/x-msexcel");
    header ("Content-Description: PHP Generated Data" );
    
    $dia = new Dia();
    $dia->geraXls();
    
    header ("Content-Disposition: attachment; filename=\"relatorio_dia.xls\"" );
            
})->setName('relatorio.dia');
