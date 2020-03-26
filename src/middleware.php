<?php
use siap\auth\models\Autenticador;
use siap\home\models\Menu;

$auth = function ($request, $response, $next) {
    $aut = Autenticador::instanciar();
  
    if (!$aut->logado())
    {
        $url = $this->router->pathFor('login');
        return $response->withRedirect($url);
    }
    
    $twig = $this->get('renderer')->getEnvironment();
    $menus_pais = Menu::getPaiByPrivilegio($aut->getUsuarioRol());
      
    $twig->addGlobal('current_user', $aut);
    $twig->addGlobal('menus_pais', $menus_pais);
    $twig->addGlobal('ip', getClientIp());
    
    
//    switch ($_SERVER["REMOTE_ADDR"]){
//      case '200.129.62.148': $twig->addGlobal('base_url', 'http://10.5.5.10/siap');      break;
//      default : $twig->addGlobal('base_url', 'http://200.129.62.148:10000/siap');
//    }

    
    $response = $next($request, $response);
    return $response;
};