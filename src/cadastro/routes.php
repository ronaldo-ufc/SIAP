<?php

include_once 'public/uteis/funcoes.php';

use siap\cadastro\models\Categoria;
use siap\cadastro\models\Fabricante;
use siap\cadastro\models\Modelo;
use siap\cadastro\models\Aquisicao;
use siap\cadastro\models\Status;
use siap\cadastro\models\EConservacao;

## ----------------------------------- ADICIONAR/REMOVER CATEGORIAS ------------------------------------------------------------------#
$app->map(['GET', 'POST'], '/categoria', function($request, $response, $args) {
    if(!empty($_SESSION['errors'])){
        echo $_SESSION['errors'];
    }
    if ($request->isPost()) {
        $postParam = $request->getParams();
        if ($postParam) {
            $categoria = $postParam['categoria'];
            Categoria::create($categoria);
            $mensagem = "Operação realizada com sucesso!";
        }
    }
    $categorias = Categoria::getAll();
    return $this->renderer->render($response, 'categoria_nova.html', array('categorias' => $categorias, 'mensagem' => $mensagem, 'mensagemErro' => $mensagemErro));
})->setName('CategoriaNova');


$app->get('/categoria/delete/{categoria_id}', function($request, $response, $args) {
    $mensagem = NULL;
    $mensagemErro = NULL;
    if ($request->isGet()) {
        $msg = Categoria::delete($args['categoria_id']);
        if ($msg[2]) {
            $mensagemErro = $msg[2];
        } else {
            $mensagem = 'Registro excluido com sucesso';
        }
    }
    return $response->withStatus(301)->withHeader('Location', '../../categoria',array('mensagem' => $mensagem, 'mensagemErro' => $mensagemErro));
})->setName('CategoriaDelete');
## -------------------------------------------------------------------------------------------------------------------------------------------#
## ------------------------------------------------ ADICIONAR/REMOVER FABRICANTES ------------------------------------------------------------#
$app->map(['GET', 'POST'], '/fabricante', function($request, $response, $args) {
    $mensagem = NULL;
    $mensagemErro = NULL;
    if ($request->isPost()) {
        $postParam = $request->getParams();
        if ($postParam) {
            $fabricante = $postParam['fabricante'];
            $msg = Fabricante::create($fabricante);
            if($msg[2]){
                $mensagemErro = msg[2];
            }else{
                $mensagem = "Operação realizada com sucesso!";
            }
        }
    }
    $fabricantes = Fabricante::getAll();
    return $this->renderer->render($response, 'fabricante_novo.html', array('fabricantes' => $fabricantes, 'mensagem' => $mensagem,'mensagemErro' => $mensagemErro));
})->setName('FabricanteNovo');

$app->get('/fabricante/key/{str}', function($request, $response, $args) {
    $str = strtoupper(tirarAcentos($args['str']));
    $fabricantes = Fabricante::getByNome($str);
    return $this->renderer->render($response, 'fabricante_novo.html', array('fabricantes' => $fabricantes, 'mensagem' => $mensagem));
})->setName('FabricanteKey');



$app->get('/fabricante/delete/{fabricante_id}', function($request, $response, $args) {
    if ($request->isGet()) {
        $msg = Fabricante::delete($args['fabricante_id']);

        if ($msg[2]) {
            $this->flash->addMessage('danger', $msg[2]);
        } else {
            $this->flash->addMessage('success', 'Registro excluido com sucesso');
        }
    }
    return $response->withStatus(301)->withHeader('Location', '../../fabricante');
})->setName('FabricanteDelete');
## ---------------------------------------------------------------------------------------------------------------------------------------#
## ------------------------------------------------ ADICIONAR/REMOVER MODELOS ------------------------------------------------------------#
$app->map(['GET', 'POST'], '/modelo/{fabricante_id}', function($request, $response, $args) {
    $mensagem = NULL;
    $mensagemErro = NULL;
    $fabricante_id = $args['fabricante_id'];
    if ($request->isPost()) {
        $postParam = $request->getParams();
        if ($postParam) {
            $modelo = $postParam['modelo_novo'];
           
            $msg = Modelo::create($modelo,$fabricante_id);
            if($msg[2]){
                $mensagemErro = $msg[2];
//                $form->errors = 'danger';
            }
            else {
                $mensagem = "Operação realizada com sucesso!";
//                $form->errors = 'success';
            }
        }
    }else{
        $messages = $this->flash->getMessages();
       
        #Verificando se tem mensagem de erro
        if($messages){
          foreach ($messages as $_msg){
            $mensagem = $_msg[0];
          }
          $form->errors = 'success';
        }
        
    }
    $fabricante = Fabricante::getById($fabricante_id);
    $modelos = Modelo::getByFabricante($fabricante_id);
    return $this->renderer->render($response, 'modelo_novo.html', array('modelos' => $modelos, 'mensagem' => $mensagem, 'fabricante' => $fabricante, 'mensagemErro' => $mensagemErro));
})->setName('ModeloNovo');

$app->get('/modelo/delete/{modelo_id}/{fabricante_id}', function($request, $response, $args) {
    if ($request->isGet()) {
        $msg = Modelo::delete($args['modelo_id']);

        if ($msg[2]) {
            $this->flash->addMessage('danger', $msg[2]);
        } else {
            $this->flash->addMessage('success', 'Registro excluido com sucesso');
        }
    }
    return $response->withStatus(301)->withHeader('Location', '../../'.$args['fabricante_id']);
})->setName('DeleteModelo');

## ---------------------------------------------------------------------------------------------------------------------------------------#

## ------------------------------------------------ ADICIONAR/REMOVER AQUISICAO ------------------------------------------------------------#
$app->map(['GET', 'POST'], '/aquisicao', function($request, $response, $args) {

    if ($request->isPost()) {
        $postParam = $request->getParams();
        if ($postParam) {
            $aquisicao = $postParam['aquisicao'];
            Aquisicao::create($aquisicao);
            $mensagem = "Operação realizada com sucesso!";
        }
    }
    $aquisicoes = Aquisicao::getAll();
    return $this->renderer->render($response, 'aquisicao_nova.html', array('aquisicoes' => $aquisicoes, 'mensagem' => $mensagem));
})->setName('AquisicaoNova');


$app->get('/aquisicao/delete/{aquisicao_id}', function($request, $response, $args) {
    if ($request->isGet()) {
        $msg = Aquisicao::delete($args['aquisicao_id']);

        if ($msg[2]) {
            $this->flash->addMessage('danger', $msg[2]);
        } else {
            $this->flash->addMessage('success', 'Registro excluido com sucesso');
        }
    }
    return $response->withStatus(301)->withHeader('Location', '../../aquisicao');
})->setName('AquisicaoDelete');
## ---------------------------------------------------------------------------------------------------------------------------------------#

## ------------------------------------------------ ADICIONAR/REMOVER STATUS ------------------------------------------------------------#
$app->map(['GET', 'POST'], '/status', function($request, $response, $args) {
    $mensagem = NULL;
    $mensagemErro = NULL;
    if ($request->isPost()) {
        $postParam = $request->getParams();
        if ($postParam) {
            $status = $postParam['status'];
            $msg = Status::create($status);
            if($msg[2]){
                $mensagemErro = msg[2];
            }
            else {
                $mensagem = "Operação realizada com sucesso!";
            }
        }
    }
    $statusT = Status::getAll();
    return $this->renderer->render($response, 'status_novo.html', array('statusT' => $statusT, 'mensagem' => $mensagem, 'mensagemErro' => $mensagemErro));
})->setName('StatusNovo');


$app->get('/status/delete/{status_id}', function($request, $response, $args) {
    if ($request->isGet()) {
        $msg = Status::delete($args['status_id']);

        if ($msg[2]) {
            $this->flash->addMessage('danger', $msg[2]);
        } else {
            $this->flash->addMessage('success', 'Registro excluido com sucesso');
        }
    }
    return $response->withStatus(301)->withHeader('Location', '../../status');
})->setName('StatusDelete');
## ---------------------------------------------------------------------------------------------------------------------------------------#

## ------------------------------------------------ ADICIONAR/REMOVER ESTAD. CONSERVAÇÃO ------------------------------------------------------------#
$app->map(['GET', 'POST'], '/conservacao', function($request, $response, $args) {
    $mensagem = NULL;
    $mensagemErro = NULL;
    if ($request->isPost()) {
        $postParam = $request->getParams();
        if ($postParam) {
            $conservacao = $postParam['conservacao'];
            $msg = EConservacao::create($conservacao);
            if($msg[2]){
                $mensagemErro = msg[2];
            }
            else{
                $mensagem = "Operação realizada com sucesso!";
            }
        }
    }
    $conservacoes = EConservacao::getAll();
    return $this->renderer->render($response, 'conservacao_nova.html', array('conservacoes' => $conservacoes, 'mensagem' => $mensagem, 'mensagemErro' => $mensagemErro));
})->setName('ConservacaoNovo');


$app->get('/conservacao/delete/{conservacao_id}', function($request, $response, $args) {
    if ($request->isGet()) {
        $msg = EConservacao::delete($args['conservacao_id']);

        if ($msg[2]) {
            $this->flash->addMessage('danger', $msg[2]);
        } else {
            $this->flash->addMessage('success', 'Registro excluido com sucesso');
        }
    }
    return $response->withStatus(301)->withHeader('Location', '../../conservacao');
})->setName('ConservacaoDelete');
## ---------------------------------------------------------------------------------------------------------------------------------------#