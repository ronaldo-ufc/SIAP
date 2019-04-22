<?php
#Rota para os serviços do template
use siap\usuario\models\Permicao;
use siap\cadastro\models\Modelo;

#Tetorma Menus Pais
$app->map(['GET', 'POST'], '/menu/{privilegio}', function($request, $response, $args){
  
  $_menu = Permicao::getMenuPaisByPrivilegio($args['privilegio']);
  $menu = [];
  foreach($_menu as $val){
    array_push($menu, array($val->getMenu_codigo(), $val->getMenu()->getMenu_nome(), $val->getPrivilegio_habilitado()));
  }
  return $response->withHeader('Content-Type', 'application/json')->withStatus(200)->withJson($menu, null, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);

});

#Retorma SubMenus
$app->map(['GET', 'POST'], '/submenu/{privilegio}/{menu}', function($request, $response, $args){
  
  $_menu = Permicao::getsubMenusByPrivilegioAndMenu($args['privilegio'], $args['menu']);
  $menu = [];
  foreach($_menu as $val){
    array_push($menu, array($val->getMenu_codigo(), $val->getMenu()->getMenu_nome(), $val->getPrivilegio_habilitado()));
  }
  return $response->withHeader('Content-Type', 'application/json')->withStatus(200)->withJson($menu, null, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);

});

#Retorma Modelos
$app->map(['GET', 'POST'], '/modelos/{fabricante_id}', function($request, $response, $args){
  
  $_modelo = Modelo::getByFabricante($args['fabricante_id']);
  $modelo = [];
  foreach($_modelo as $val){
    array_push($modelo, array($val->getModelo_Id(), $val->getnome()));
  }
  return $response->withHeader('Content-Type', 'application/json')->withStatus(200)->withJson($modelo, null, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);

});

#Salvar Itens
$app->post('/salvar/item', function($request, $response, $args){
  $postParam = $request->getParams();
  switch ($postParam['item']){
    case 'marca': $msg = siap\cadastro\models\Fabricante::create($postParam['nome']);
      break;
              
    case 'modelo': $msg = Modelo::create($postParam['nome'], $postParam['marca']);
      break;
              
    case 'tipo_de_aquisicao': $msg = siap\cadastro\models\Aquisicao::create($postParam['nome']);
      break;
              
    case 'status': $msg = siap\cadastro\models\Status::create($postParam['nome']);
      break;
              
    case 'setor': $msg = siap\setor\models\Setor::create($postParam['nome'],$postParam['sigla']);
      break;
              
    case 'estado_de_conservacao': $msg = siap\cadastro\models\EConservacao::create($postParam['nome']);
      break;
    case 'categoria': $msg = siap\cadastro\models\Categoria::create($postParam['nome']);
      break;
    
    case 'unidade': $msg = siap\cadastro\models\Unidade::create($postParam['nome']);
      break;
    
    case 'grupo': $msg = siap\cadastro\models\Grupo::create($postParam['nome']);
      break;
  }
  return ($msg[2])? $msg[2] : "Item :".$postParam['nome']." foi adicionado com sucesso.";
});

#Recebe Itens
$app->get('/receber/item/{item}', function($request, $response, $args){
  $item = [];
    
  switch ($args['item']){
    case 'marca': $objeto = siap\cadastro\models\Fabricante::getAll();
                      foreach($objeto as $val){
                        array_push($item, array($val->getFabricante_id(), $val->getnome()));
                      }
                      break;
    case 'tipo_de_aquisicao': $objeto = siap\cadastro\models\Aquisicao::getAll();
                      foreach($objeto as $val){
                        array_push($item, array($val->getAquisicao_id(), $val->getnome()));
                      }
                      break;
    case 'status': $objeto = siap\cadastro\models\Status::getAll();
                      foreach($objeto as $val){
                        array_push($item, array($val->getStatus_id(), $val->getnome()));
                      }
                      break;
    case 'setor': $objeto = siap\setor\models\Setor::getAll();
                      foreach($objeto as $val){
                        array_push($item, array($val->getSetor_id(), $val->getnome()));
                      }
                      break;
    case 'estado_de_conservacao': $objeto = siap\cadastro\models\EConservacao::getAll();
                      foreach($objeto as $val){
                        array_push($item, array($val->getConservacao_id(), $val->getnome()));
                      }
                      break;
    case 'categoria': $objeto = siap\cadastro\models\Categoria::getAll();
                      foreach($objeto as $val){
                        array_push($item, array($val->getCategoria_id(), $val->getnome()));
                      }
                      break;
  }
  return $response->withHeader('Content-Type', 'application/json')->withStatus(200)->withJson($item, null, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
});