<?php

include_once 'public/uteis/funcoes.php';
use siap\produto\models\Ativos;
use siap\setor\models\Setor;
use siap\relatorios\relatorio\RelatorioSetor;
use \siap\relatorios\relatorio\CategoriaFiltro;
use Dompdf\Dompdf;

use siap\auth\models\Autenticador;


$app->map(['GET', 'POST'], '/bens', function($request, $response, $args) {
    $setores = Setor::getAll();
    if($request->isPost()){
        $dompdf = new DOMPDF();
        $dompdf->setPaper('A4','landscape');
        $postParam = $request->getParams();
        $setor = Setor::getById(intval($postParam["radio"]));
        if(intval($postParam["radio"]) != -1){
            $relatorio_setor = RelatorioSetor::bundle(intval($postParam["radio"]),$setor,$setor->getNome());
//        $relatorio_setor->bundle(intval($postParam["radio"]));
            $header = $relatorio_setor->start_pdf();
//        var_dump($relatorio_setor->start_pdf());
            return $this->renderer->render($response, 'gerar_pdf.html', array('dompdf'=>$dompdf,'array' => array("Attachment" => FALSE), 'header' => $header));
        }else{
            $relatorio_all_setor = \siap\relatorios\relatorio\TodosSetores::bundle();
            $header = $relatorio_all_setor->start_pdf();
            return $this->renderer->render($response, 'gerar_pdf.html', array('dompdf'=>$dompdf,'array' => array("Attachment" => FALSE), 'header' => $header));
        }
        
    }
    return $this->renderer->render($response, 'bens_permanentes.html', array('setores' => $setores));
})->setName('RelatoriosBemPermanente');

$app->map(['GET', 'POST'], '/categoria', function($request, $response, $args) {
    $setores = Setor::getAll();
    if($request->isPost()){
        $dompdf = new DOMPDF();
        $dompdf->setPaper('A4','landscape');
        $postParam = $request->getParams();
        $relatorio_categoria = CategoriaFiltro::bundle();
        $header = $relatorio_categoria->start_pdf($postParam["nome"], $postParam['categoria'],$postParam["modelo"], $postParam["dataAtesto"], $postParam["status"], $postParam["conservacao"], $postParam["setor"], $postParam["fornecedor"], $postParam["notaFiscal"], $postParam['empenho'], $postParam['descricao']);
        return $this->renderer->render($response, 'gerar_pdf.html', array('dompdf'=>$dompdf,'array' => array("Attachment" => FALSE), 'header' => $header));
    }
    $modelos = \siap\cadastro\models\Modelo::getAll();
    $status = siap\cadastro\models\Status::getAll();
    $conservacoes = siap\cadastro\models\EConservacao::getAll();
    $categorias = siap\cadastro\models\Categoria::getAll();
    return $this->renderer->render($response, 'bens_categoria.html', array('modelos' => $modelos, 'status' => $status, 'conservacoes' => $conservacoes, 'setores' => $setores, 'categorias' => $categorias));
})->setName('RelatoriosBemCategoria');

$app->map(['GET', 'POST'], '/categoria/setor', function($request, $response, $args) {
    $setores = Setor::getAll();
    if($request->isPost()){
        $dompdf = new DOMPDF();
        $dompdf->setPaper('A4','landscape');
        $postParam = $request->getParams();
        $relatorio_categoria = \siap\relatorios\relatorio\CategoriaSetor::bundle();
        $header = $relatorio_categoria->start_pdf($postParam["nome"], $postParam['categoria'],$postParam["modelo"], $postParam["dataAtesto"], $postParam["status"], $postParam["conservacao"], $postParam["setor"], $postParam["fornecedor"], $postParam["notaFiscal"], $postParam['empenho'], $postParam['descricao']);
        return $this->renderer->render($response, 'gerar_pdf.html', array('dompdf'=>$dompdf,'array' => array("Attachment" => FALSE), 'header' => $header));
    }
    $modelos = \siap\cadastro\models\Modelo::getAll();
    $status = siap\cadastro\models\Status::getAll();
    $conservacoes = siap\cadastro\models\EConservacao::getAll();
    $categorias = siap\cadastro\models\Categoria::getAll();
    return $this->renderer->render($response, 'bens_categoria.html', array('modelos' => $modelos, 'status' => $status, 'conservacoes' => $conservacoes, 'setores' => $setores, 'categorias' => $categorias));
})->setName('RelatoriosCategoriaSetor');

$app->map(['GET', 'POST'], '/setor/movimentacao', function($request, $response, $args) {
    $setores = Setor::getAll();
    if($request->isPost()){
        $dompdf = new DOMPDF();
        $dompdf->setPaper('A4','landscape');
        $postParam = $request->getParams();
        $relatorio_categoria = \siap\relatorios\relatorio\CategoriaSetor::bundle();
        $header = $relatorio_categoria->start_pdf($postParam["nome"], $postParam['categoria'],$postParam["modelo"], $postParam["dataAtesto"], $postParam["status"], $postParam["conservacao"], $postParam["setor"], $postParam["fornecedor"], $postParam["notaFiscal"], $postParam['empenho'], $postParam['descricao']);
        return $this->renderer->render($response, 'gerar_pdf.html', array('dompdf'=>$dompdf,'array' => array("Attachment" => FALSE), 'header' => $header));
    }
    $modelos = \siap\cadastro\models\Modelo::getAll();
    $status = siap\cadastro\models\Status::getAll();
    $conservacoes = siap\cadastro\models\EConservacao::getAll();
    $categorias = siap\cadastro\models\Categoria::getAll();
    return $this->renderer->render($response, 'bens_categoria.html', array('modelos' => $modelos, 'status' => $status, 'conservacoes' => $conservacoes, 'setores' => $setores, 'categorias' => $categorias));
})->setName('RelatoriosSetorMovimentacao');