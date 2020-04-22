<?php
use siap\material\models\Produto;
use siap\material\models\Requisicao;
use siap\cadastro\models\Unidade;
use siap\cadastro\models\Grupo;
use siap\auth\models\Autenticador;
use siap\usuario\models\Usuario;
use siap\material\models\MontaBuscasItens;
use siap\material\models\RequisicaoItens;
use siap\material\models\Estoque;
use siap\imagem\models\Imagem;
include_once 'public/uteis/funcoes.php';

$app->get('/produto', function($request, $response, $args) {
  $postParam = $request->getParams();
  $c_ufc = $postParam['c_ufc'];
  $c_barras = $postParam['c_barras'];
  $nome = $postParam['nome'];
  $unidade = $postParam['unidade'];
  $grupo = $postParam['grupo'];
  $observacao = $postParam['observacao'];
  
  $produtos = Produto::getAllByParams($c_ufc , $c_barras, $nome, $unidade, $grupo, $observacao);
  
  $msg = getMensagem($this->flash->getMessages());
  return $this->renderer->render($response, 'produto_main.html', array('unidades'=>Unidade::getAll(), 'grupos'=> Grupo::getAll(),'produtos'=>$produtos, 'classe'=> $msg[0], 'texto'=>$msg[1]));
})->setName('visualizaProduto');

$app->get('/produto/novo', function($request, $response, $args) {
  return $this->renderer->render($response, 'produto_novo.html', array('unidades'=>Unidade::getAll(), 'grupos'=> Grupo::getAll()));
})->setName('NovoProduto');

$app->post('/produto/novo', function($request, $response, $args) {
  $postParam = $request->getParams();
  $c_ufc = $postParam['c_ufc'];
  $c_barras = $postParam['c_barras'];
  $nome = $postParam['nome'];
  $unidade = $postParam['unidade'];
  $grupo = $postParam['grupo'];
  $observacao = $postParam['observacao'];
  $quantidade_minima = $postParam['quantidade_minima'];
  $localizacao = $postParam['localizacao'];
  
  $msg = Produto::create($c_ufc, $c_barras, $nome, $unidade, $grupo, $observacao, $quantidade_minima,$localizacao);
  if ($msg[2]){ return $this->renderer->render($response, 'produto_novo.html', array('unidades'=>Unidade::getAll(), 'grupos'=> Grupo::getAll(), 'mensagemErro'=>$msg[2])); }
  
  return $response->withStatus(301)->withHeader('Location', '../produto');
})->setName('NovoProduto');

$app->map(['GET', 'DELETE'], '/excluir/{produto_codigo}', function($request, $response, $args) {
  $produto_codigo = $args['produto_codigo'];
  
  $msg = Produto::delete($produto_codigo);
  
  if ($msg[2]) {
      $this->flash->addMessage('danger', $msg[2]);
  } else {
      $this->flash->addMessage('success', 'Registro excluido com sucesso');
  }
  
  return $response->withStatus(301)->withHeader('Location', '../produto');
  
})->setName('excluirProduto');

$app->post('/produto/editar/{produto_codigo}', function($request, $response, $args) {
  $postParam = $request->getParams();
  
  if (!$postParam['imagem_produto'] && $_FILES['img']){
    $upload = new siap\models\UploadImagem();
    $file = $upload->preparar($_FILES['img'], $this->get('upload_directory_imagem'));
  }else{
     $file = $postParam['imagem_produto'];
  }
  //var_dump($file);
  $msg = Produto::update($postParam['c_ufc'], $postParam['c_barras'], $postParam['nome'], $postParam['unidade'], $postParam['grupo'], $postParam['observacao'], $postParam['quantidade_minima'], $file, $postParam['localizacao'], $args['produto_codigo']);
  if ($msg[2]) {
      $this->flash->addMessage('danger', $msg[2]);
  } else {
      $this->flash->addMessage('success', 'Produto atualizado com sucesso.');
  }
  return $response->withStatus(301)->withHeader('Location', '../../produto');
})->setName('SalvarEditarProduto');

$app->get('/produto/editar/{produto_codigo}', function($request, $response, $args) {
  $produto = Produto::getById($args['produto_codigo']);
  $unidades = Unidade::getAllById($produto->getUnidade_codigo());
  $grupos = Grupo::getAllById($produto->getGrupo_codigo());
  $imagens = Imagem::getAll();
  
  return $this->renderer->render($response, 'produto_editar.html', array('imagens'=>$imagens,  'produto'=>$produto, 'unidades'=>$unidades, 'grupos'=> $grupos));
  
})->setName('editarProduto');

$app->post('/movimentacao', function($request, $response, $args) {
  
  $msg = Estoque::movimentacao($request->getParams());
  
  if ($msg[2]) {
      $this->flash->addMessage('danger', $msg[2]);
  } else {
      $this->flash->addMessage('success', 'Movimentação realizada com sucesso');
  }
  return $response->withStatus(301)->withHeader('Location', $_SERVER['HTTP_REFERER']);
})->setName('MovimentacaoDeProduto');



/******************* SOLICITAÇÕES ********************************/
$app->get('/solicitacoes', function($request, $response, $args) {
  $aut = Autenticador::instanciar();
  $usuario = Usuario::getByLogin($aut->getUsuario());
  $requisicoes = Requisicao::getAllBySetor($usuario->getSetor());
  $msg = getMensagem($this->flash->getMessages());
  return $this->renderer->render($response, 'solicitacao_main.html', array('setor_nome'=>$usuario->getSetorNome(), 'solicitacoes'=>$requisicoes, 'classe'=> $msg[0], 'texto'=>$msg[1]));
})->setName('visualizaSolicitacoes');

$app->get('/solicitacoes/novo', function($request, $response, $args) {
  
  $aut = Autenticador::instanciar();
  $usuario = Usuario::getByLogin($aut->getUsuario());
  
  #Verifica se tem Requisição em aberto no ano corrente
  $requisicao = new Requisicao();
  $solicitacao_aberta = $requisicao->haveRequisicaoAberta($aut->getUsuario());
  if ($solicitacao_aberta){
    $this->flash->addMessage('danger', 'A solicitação de número <strong>'.$solicitacao_aberta->getNumero().'</strong> ainda não foi enviada.');
    return $response->withStatus(301)->withHeader('Location', '../solicitacoes');
  }
  
  #Verifica pendencia de recebimento
  $r = Requisicao::isPendendeRecebimentoBySetor($aut->getUsuario());
  if ($r){
    $this->flash->addMessage('danger', 'Não foi possível criar uma nova requisição. Existe uma que ainda não foi dada o recebimento. Requisição nº '.$r->getNumero());
    return $response->withStatus(301)->withHeader('Location', '../solicitacoes');
  }
  
  Requisicao::create($aut->getUsuario(), COD_ALMOXARIFADO, $usuario->getSetor());
  
  return $response->withStatus(301)->withHeader('Location', '../solicitacoes');
})->setName('NovaSolicitacao');

$app->get('/solicitacoes/{requisicao_codigo}', function($request, $response, $args) {
  $msg = getMensagem($this->flash->getMessages());
  $requisicao_codigo = $args['requisicao_codigo'];
  $aut = Autenticador::instanciar();
  $usuario = Usuario::getByLogin($aut->getUsuario());
  $requisicao = Requisicao::getByCodigo($requisicao_codigo);
  $itens = RequisicaoItens::getByRequisicao($requisicao_codigo);
  return $this->renderer->render($response, 'solicitacao_itens.html', array('setor_nome'=>$usuario->getSetorNome(), 'requisicao'=>$requisicao, 'itens'=>$itens, 'classe'=> $msg[0], 'texto'=>$msg[1]));
})->setName('ItensSolicitacoes');

$app->post('/solicitacoes/{requisicao_codigo}', function($request, $response, $args) {
  $postParam = $request->getParams();
  $produto = $postParam['produto'];
  $quantidade = $postParam['quantidade'];
  
  $msg = RequisicaoItens::create($args['requisicao_codigo'], $produto, $quantidade);
    
  return $response->withStatus(301)->withHeader('Location', $args['requisicao_codigo']);
})->setName('SalvarItensSolicitacoes');

$app->post('/solicitacoes/estorno/{requisicao_codigo}', function($request, $response, $args) {
  $postParam = $request->getParams();

  $item = $postParam['item'];
  $quantidade = $postParam['quantidade'];
 
  $msg = RequisicaoItens::estorno($args['requisicao_codigo'], $item, $quantidade);
  if ($msg[2]) {
   $this->flash->addMessage('danger', $msg[2]);
  }else{
    $this->flash->addMessage('success', 'Estorno realizado com sucesso.');
  }
  return $response->withStatus(301)->withHeader('Location', '../'.$args['requisicao_codigo']);
})->setName('SalvarItensSolicitacoes');



$app->post('/seach/{nome}', function($request, $response, $args) {
  $p = Produto::getAllByNome($args['nome']);
  $tabela = new MontaBuscasItens($p);
  echo $tabela->getTabela();
});

$app->post('/seach/produto/{nome}', function($request, $response, $args) {
  $p = Produto::getAllByNome($args['nome']);
  $tabela = new MontaBuscasItens($p);
  return $response->write($tabela->getProduto());
});

$app->post('/seach/itens/{codigo}', function($request, $response, $args) {
  $p = Produto::getById($args['codigo']);
  
  return $this->renderer->render($response, 'resChoice.html', array('produto'=>$p));
});

$app->post('/inserir/produto/{codigo}', function($request, $response, $args) {
  $p = Produto::getById($args['codigo']);
  $a = "<br><label>Produto: ".$p->getNome()."</label><br><br><input type='hidden' name='produto_codigo' value='".$p->getProduto_codigo()."'>";
  return $response->write($a);
  //return $this->renderer->render($response, 'resChoice.html', array('produto'=>$p));
});

$app->map(['GET', 'DELETE'],'/solicitacoes/item/excluir/{solicitacao}/{produto_codigo}', function($request, $response, $args) {
  RequisicaoItens::delete($args['solicitacao'], $args['produto_codigo']);
  return $response->withStatus(301)->withHeader('Location', '../../../'.$args['solicitacao']);
});

$app->map(['GET', 'DELETE'],'/solicitacoes/excluir/{solicitacao}', function($request, $response, $args) {
  Requisicao::delete($args['solicitacao']);
  return $response->withStatus(301)->withHeader('Location', '../../solicitacoes');
});

$app->get('/solicitacoes/enviar/{codigo}', function($request, $response, $args) {
  if (Requisicao::isItens($args['codigo'])){
    $msg = Requisicao::enviar($args['codigo']);
    if ($msg[2]) {
     $this->flash->addMessage('danger', $msg[2]);
    }
  }else{
    $this->flash->addMessage('danger', 'Não é possível enviar a solicitação sem itens cadastrados');
  }
  
  return $response->withStatus(301)->withHeader('Location', '../../solicitacoes');
});





/********************************  GERENCIAR ************************************/

$app->get('/gerenciar', function($request, $response, $args) {
  $postParam = $request->getParams();
  if ($postParam){
    $requisicoes = Requisicao::getAllByFiltro($postParam['numero'], $postParam['status'], $postParam['setor'], $postParam['inicio'], $postParam['fim']);
  }else{
    $requisicoes = Requisicao::getAllEnviadas();
  }
  $msg = getMensagem($this->flash->getMessages());
  $setores = siap\setor\models\Setor::getAll();
  return $this->renderer->render($response, 'gerenciar_solicitacao_main.html', array('solicitacoes'=>$requisicoes, 'setores'=>$setores, 'classe'=> $msg[0], 'texto'=>$msg[1]));
});

$app->get('/gerenciar/{requisicao_codigo}', function($request, $response, $args) {
  $requisicao_codigo = $args['requisicao_codigo'];
  $aut = Autenticador::instanciar();
  $usuario = Usuario::getByLogin($aut->getUsuario());
  $requisicao = Requisicao::getByCodigo($requisicao_codigo);
  $itens = RequisicaoItens::getByRequisicao($requisicao_codigo);
  return $this->renderer->render($response, 'gerenciar_solicitacao_itens.html', array('setor_nome'=>$usuario->getSetorNome(), 'requisicao'=>$requisicao, 'itens'=>$itens));
})->setName('ItensSolicitacoes');

$app->post('/gerenciar/{requisicao_codigo}', function($request, $response, $args) {
  $postParam = $request->getParams();
  $solicitante = $postParam['solicitante'];
  define('ALMOXARIFADO', 22);
  
  //echo $args['requisicao_codigo']; return;
  
  $aut = Autenticador::instanciar();

  $itens = $postParam['quantidade'];
  $flag_aprovar =  false;
  try {
    foreach($itens as $produto => $quantidade)
    {
      $msg = Estoque::saida($args['requisicao_codigo'], $produto, $quantidade, $aut->getUsuario(), $solicitante, ALMOXARIFADO, $postParam['destino']);
      if ($msg[2]){
         throw new \Exception($msg[2]);
      }
      if ($quantidade > 0){
        $flag_aprovar =  true;
      }
      
    }
    if ($flag_aprovar){
      $msg = Requisicao::Aprovar($args['requisicao_codigo']);
    }else{
      $msg = Requisicao::Cancelar($args['requisicao_codigo']);
    }
    if ($msg[2]){
      throw new \Exception($msg[2]);
    }
    return $response->withStatus(301)->withHeader('Location', '../gerenciar');
  } catch (Exception $ex) {
    echo $ex->getMessage();
  }
    
})->setName('ItensSolicitacoes');

$app->post('/confirmar-recebimento', function($request, $response, $args) {
 $postParam = $request->getParams();
 
 $senha = trim ($postParam['senha']);
 $login = trim ($postParam['login']);
 $requisicao = trim ($postParam['requisicao']);
 
 $usuario = Usuario::getByLogin($login);
 if (!$usuario){$this->flash->addMessage('danger', 'Usuario não encontrado');return $response->withStatus(301)->withHeader('Location', '../materiais/solicitacoes');}
 if ($usuario->getSenha() == md5($senha)){
    $msg = Requisicao::AprovarRecebimento($requisicao, $login);
    if ($msg[2]) {
      $this->flash->addMessage('danger', $msg[2]);
    }else{
      $this->flash->addMessage('success', 'Recebimento foi confirmado');
    }
 }else{
   $this->flash->addMessage('danger', 'Senha não confere');
 }
 return $response->withStatus(301)->withHeader('Location', '../materiais/solicitacoes');
})->setName('ItensSolicitacoes');


//******************************************************* Estorno ********************************************//

$app->get('/estorno', function($request, $response, $args) {
  $msg = getMensagem($this->flash->getMessages());
  $itens = RequisicaoItens::getAllEstornos();
  return $this->renderer->render($response, 'receber_estorno.html', array('itens'=>$itens, 'classe'=> $msg[0], 'texto'=>$msg[1]));
})->setName('Estorno');

$app->post('/estorno', function($request, $response, $args) {
  $postParam = $request->getParams();

  $senha = trim ($postParam['senha']);
  $login = trim ($postParam['login']);
  $requisicao = trim ($postParam['requisicao']);
  $produto = $postParam['produto'];
 $usuario = Usuario::getByLogin($login);
 if (!$usuario){$this->flash->addMessage('danger', 'Usuario não encontrado');return $response->withStatus(301)->withHeader('Location', 'estorno');}
 if ($usuario->getSenha() == md5($senha)){
    $msg = RequisicaoItens::AprovarRecebimento($requisicao, $produto, $login);
    if ($msg[2]) {
      $this->flash->addMessage('danger', $msg[2]);
    }else{
      $this->flash->addMessage('success', 'Recebimento foi confirmado');
    }
 }else{
   $this->flash->addMessage('danger', 'Senha não confere');
 }
 return $response->withStatus(301)->withHeader('Location', 'estorno');
})->setName('Estorno');

