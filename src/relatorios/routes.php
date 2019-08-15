<?php

include_once 'public/uteis/funcoes.php';

use siap\produto\models\Ativos;
use siap\setor\models\Setor;
use siap\relatorios\relatorio\RelatorioSetor;
use \siap\relatorios\relatorio\CategoriaFiltro;
use siap\relatorios\relatorio\ItemAprovados;

$app->map(['GET', 'POST'], '/bens', function($request, $response, $args) {
    $setores = Setor::getAll();
    if ($request->isPost()) {
        $dompdf = $this->DOMPDF;
        $dompdf->setPaper('A4', 'portrait'); //landscape
        $postParam = $request->getParams();
        $setor = Setor::getById(intval($postParam["radio"]));
        if (intval($postParam["radio"]) != -1) {
            $relatorio_setor = RelatorioSetor::bundle(intval($postParam["radio"]), $setor, $setor->getNome());
            $header = $relatorio_setor->start_pdf();
            if ($header[2] != NULL) {
                return $this->renderer->render($response, 'bens_permanentes.html', array('setores' => $setores, "mensagemErro" => $header[2]));
            } else {
                return $this->renderer->render($response, 'gerar_pdf.html', array('dompdf' => $dompdf, 'array' => array("Attachment" => FALSE), 'header' => $header[1]));
            }
        } else {
            $relatorio_all_setor = \siap\relatorios\relatorio\TodosSetores::bundle();
            $header = $relatorio_all_setor->start_pdf();
            if ($header[2] != NULL) {
                return $this->renderer->render($response, 'bens_permanentes.html', array('setores' => $setores, "mensagemErro" => $header[2]));
            } else {
                return $this->renderer->render($response, 'gerar_pdf.html', array('dompdf' => $dompdf, 'array' => array("Attachment" => FALSE), 'header' => $header[1]));
            }
        }
    }
    return $this->renderer->render($response, 'bens_permanentes.html', array('setores' => $setores));
})->setName('RelatoriosBemPermanente');

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


$app->get('/consumo/produto', function($request, $response, $args) {
  return $this->renderer->render($response, 'consumo_setor.html', array());
})->setName('Relatorioconsumo');

$app->get('/consumo/produto/pdf', function($request, $response, $args) {
  $postParam = $request->getParams();
  $relatorio = new \siap\relatorios\relatorio\ItemConsumo();

  $relatorio->criar($postParam['produto_codigo'], $postParam['dataInicio'] , $postParam['dataFim']);
  $relatorio->imprimir($this->DOMPDF);
})->setName('Relatorioconsumo-produto');
