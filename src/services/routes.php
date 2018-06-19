<?php
#Rota para os serviços do template
use siap\usuario\models\Permicao;

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